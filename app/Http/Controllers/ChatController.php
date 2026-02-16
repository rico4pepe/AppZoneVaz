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


class ChatController extends Controller
{
    // Send a message
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'event_id' => 'nullable|exists:fixtures,id',

        ]);
    
        $ban = ChatBan::where('user_id', Auth::id())
                    ->where(function ($q) {
                        $q->whereNull('banned_until')->orWhere('banned_until', '>', now());
                    })->first();
    
        if ($ban) {
            return response()->json([
                'message' => 'You are banned from sending messages.',
                'reason' => $ban->reason,
            ], 403);
        }
    
        // 🔍 Moderate message using OpenAI
        // $client = app(\OpenAI\Client::class);
        // $result = $client->moderations()->create([
        //     'input' => $validated['message']
        // ]);
    
        // $flagged = $result->results[0]->flagged;
    
        // if ($flagged) {
        //     // Log the flagged message
        //     ModeratedMessage::create([
        //         'user_id' => Auth::id(),
        //         'message' => $validated['message'],
        //         'categories' => json_encode($result->results[0]->categories),
        //         'severity' => $result->results[0]->category_scores->toArray()['sexual'] ?? null, // optional
        //     ]);
    
        //     return response()->json([
        //         'message' => 'Your message was flagged by moderation and not sent.'
        //     ], 422);
        // }

        Log::info('Chat message from user: ' . Auth::id());
    
        // ✅ Continue sending
        $message = ChatMessage::create([
            'user_id' => Auth::id(),
            'event_id' => $validated['event_id'] ?? null,
            'message' => $validated['message'],
            'is_hidden' => false,
        ]);
    
        // Mentions
        preg_match_all('/@(\w+)/', $validated['message'], $matches);
        foreach ($matches[1] ?? [] as $username) {
            $mentionedUser = \App\Models\User::where('username', $username)->first();
            if ($mentionedUser) {
                Mention::create([
                    'chat_message_id' => $message->id,
                    'mentioned_user_id' => $mentionedUser->id,
                ]);
            }
        }
    
        return response()->json([
            'message' => 'Message sent',
            'data' => $message->load('user'),
        ]);
    }

    // Fetch messages (optionally filter by event)y
    public function fetchMessages(Request $request)
    {
        $eventId = $request->query('event_id');

        $query = ChatMessage::with('user')->where('is_hidden', false);

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $messages = $query->latest()->take(50)->get()->reverse()->values();

        return response()->json($messages);
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

            $leagueIds = $user->leagues()->pluck('leagues.id');
            $teamIds   = $user->teams()->pluck('teams.id');

            $query = Fixture::with(['homeTeam', 'awayTeam']);

            if ($teamIds->isNotEmpty()) {
                $query->where(function ($q) use ($teamIds) {
                    $q->whereIn('home_team_id', $teamIds)
                    ->orWhereIn('away_team_id', $teamIds);
                });
            } elseif ($leagueIds->isNotEmpty()) {
                $query->whereIn('league_id', $leagueIds);
            }

            $fixtures = $query->orderByDesc('kickoff_at')
                            ->limit(10)
                            ->get();

            $rooms = [
                ['id' => null, 'name' => '🌍 Global Chat']
            ];

            foreach ($fixtures as $fixture) {
                $rooms[] = [
                    'id' => $fixture->id,
                    'name' => "{$fixture->homeTeam->name} vs {$fixture->awayTeam->name}"
                ];
            }

            return response()->json($rooms);
        }





}

