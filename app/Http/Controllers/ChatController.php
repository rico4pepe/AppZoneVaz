<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Report;
use App\Models\User;
use App\Models\ChatBan;
use App\Models\UserWarning;
use App\Models\Mention;
use App\Models\ModeratedMessage;
use App\Models\Fixture;
use Illuminate\Support\Facades\DB;
use App\Services\SubscriptionState;


class ChatController extends Controller
{
    // Send a message
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'match_id' => 'nullable|exists:matches,id',
        ]);

        $user = Auth::user();

        // 🔒 Ban check (highest priority)
        $ban = ChatBan::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNull('banned_until')
                ->orWhere('banned_until', '>', now());
            })->first();

        if ($ban) {
            return response()->json([
                'message' => 'You are banned from sending messages.',
                'reason' => $ban->reason,
            ], 403);
        }

        // 🔒 Feature Gate - send permission
        if (!\App\Services\FeatureGate::allows($user, 'chat.send')) {
            return response()->json([
                'message' => 'Upgrade to send messages',
                'upgrade_required' => true
            ], 403);
        }

        // ⚡ Feature Gate - rate limiting (Daily users)
        if (!\App\Services\FeatureGate::allows($user, 'chat.unlimited')) {

            $lastMessage = ChatMessage::where('user_id', $user->id)
                ->latest()
                ->first();

            if ($lastMessage && now()->diffInSeconds($lastMessage->created_at) < 10) {
                return response()->json([
                    'message' => 'Slow down! Upgrade to send messages faster.',
                    'upgrade_required' => true
                ], 429);
            }
        }

        \Log::info('Chat message from user: ' . $user->id);

        // ✅ Create message
        $message = ChatMessage::create([
            'user_id' => $user->id,
            'match_id' => $validated['match_id'] ?? null,
            'message' => $validated['message'],
            'is_hidden' => false,
        ]);

        // 🔔 Mentions handling
        preg_match_all('/@(\w+)/', $validated['message'], $matches);

        foreach ($matches[1] ?? [] as $username) {
            $mentionedUser = User::where('display_name', $username)
                ->orWhere('name', $username)
                ->first();

            if ($mentionedUser) {
                Mention::create([
                    'chat_message_id' => $message->id,
                    'mentioned_user_id' => $mentionedUser->id,
                ]);
            }
        }

        // 📦 Prepare response
        $messageData = $message->load('user')->toArray();

        // ⭐ Premium flag (monthly users)
        $messageData['user']['is_premium'] = \App\Services\FeatureGate::allows($user, 'chat.vip');

        return response()->json([
            'message' => 'Message sent',
            'data' => $messageData,
        ]);
    }


    // Fetch messages (optionally filter by event)y
   public function fetchMessages(Request $request)
    {
        $matchId = $request->query('match_id');

        // 🔒 Validate "after"
        $after = is_numeric($request->query('after'))
            ? (int) $request->query('after')
            : null;

        $query = ChatMessage::with('user')
            ->where('is_hidden', false);

        // 🎯 Match or Global
        if ($matchId) {
            $query->where('match_id', $matchId);
        } else {
            $query->whereNull('match_id');
        }

        // 📩 Incremental polling
        if ($after) {
            $messages = $query
                ->where('id', '>', $after)
                ->orderBy('id', 'asc')
                ->limit(100)
                ->get();
        } else {
            $messages = $query
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get()
                ->reverse()
                ->values();
        }

        // ⚡ Throttled presence update
        if (rand(1, 3) === 1) {
            DB::table('chat_presence')->updateOrInsert(
                [
                    'user_id' => auth()->id(),
                    'match_id' => $matchId
                ],
                [
                    'last_seen' => now()
                ]
            );
        }

        // 👥 Active fans count
        $activeFans = DB::table('chat_presence')
            ->where('last_seen', '>=', now()->subSeconds(30))
            ->when(
                $matchId,
                fn($q) => $q->where('match_id', $matchId),
                fn($q) => $q->whereNull('match_id')
            )
            ->count();

            $typingUsers = DB::table('chat_typing')
    ->join('users', 'users.id', '=', 'chat_typing.user_id')
    ->where('chat_typing.updated_at', '>=', now()->subSeconds(5))
    ->when(
        $matchId,
        fn($q) => $q->where('match_id', $matchId),
        fn($q) => $q->whereNull('match_id')
    )
    ->where('chat_typing.user_id', '!=', auth()->id())
    ->pluck('users.name');

        return response()->json([
            'messages' => $messages,
            'fans' => $activeFans,
            'typing' => $typingUsers
        ]);
    }



    public function hideMessage($messageId)
    {
        // Check if the user is an admin
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message = ChatMessage::findOrFail($messageId);
        $message->update(['is_hidden' => true]);

        return response()->json(['message' => 'Message hidden successfully.']);
    }   

    public function banUser(Request $request, $userId)
    {
        // Check if the user is an admin
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'banned_until' => 'nullable|date',
        ]);

        $user = User::findOrFail($userId);

        // Create a ban record
        ChatBan::create([
            'user_id' => $userId,
            'reason' => $validated['reason'],
            'banned_until' => $validated['banned_until'] ?? null,
            'issued_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'User banned successfully.']);
    }

    public function issueWarning(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500',
        ]);

        $warning = UserWarning::create([
            'user_id' => $validated['user_id'],
            'issued_by' => Auth::id(),
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'message' => 'Warning issued successfully',
            'data' => $warning
        ]);
    }



    // Fetch all warnings for a specific user
    public function getUserWarnings($userId)
    {
        $warnings = UserWarning::where('user_id', $userId)->get();

        return response()->json($warnings);
    }

  public function rooms()
{
    $user = Auth::user();

    // ✅ Get unique team IDs (defensive safety)
    $teamIds = $user->teams()
        ->pluck('teams.id')
        ->unique()
        ->values();

    // 🌍 Always include Global Chat
    $rooms = [
        ['id' => null, 'name' => '🌍 Global Chat']
    ];

    // 🚨 No teams selected → only global
    if ($teamIds->isEmpty()) {
        return response()->json($rooms);
    }

    // 🎯 Fetch matches for ALL selected teams
    $fixtures = Fixture::with(['homeTeam', 'awayTeam'])
        ->where(function ($q) use ($teamIds) {
            $q->whereIn('home_team_id', $teamIds)
              ->orWhereIn('away_team_id', $teamIds);
        })
        ->orderByDesc('kickoff_at')
        ->limit(10)
        ->get();

    // 🧱 Build chat rooms
    foreach ($fixtures as $fixture) {
        $rooms[] = [
            'id' => $fixture->id,
            'name' => "{$fixture->homeTeam->name} vs {$fixture->awayTeam->name}"
        ];
    }

    return response()->json($rooms);
}

    public function typing(Request $request)
    {
        $user = auth()->user();

        DB::table('chat_typing')->updateOrInsert(
            [
                'user_id' => $user->id,
                'match_id' => $request->match_id
            ],
            [
                'updated_at' => now()
            ]
        );

        return response()->json(['status' => 'ok']);
    }





}

