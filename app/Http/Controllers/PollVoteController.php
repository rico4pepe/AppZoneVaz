<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\PollVote;
use App\Models\UserActivity;
use App\Models\User;

class PollVoteController extends Controller
{
    //

    //This ensures every vote is also logged as activity and scored.
 public function vote(Request $request)
{
    // 1. Validate input
    $validated = $request->validate([
        'content_id' => 'required|exists:contents,id',
        'option_id' => 'required|exists:content_options,id',
    ]);

    // 2. Fetch content with options (important)
    $content = Content::with('options')
        ->where('id', $validated['content_id'])
        ->where('type', 'poll')
        ->firstOrFail();

    // 3. Check if content is interactable
    abort_if(!$content->isInteractable(), 403);

    // 4. Ensure option belongs to this poll
    $selectedOption = \App\Models\ContentOption::where('id', $validated['option_id'])
        ->where('content_id', $content->id)
        ->firstOrFail();

    $userId = auth()->id();

    // 5. Prevent duplicate voting
    if (PollVote::where('user_id', $userId)
        ->where('content_id', $content->id)
        ->exists()) {

        return response()->json([
            'success' => false,
            'message' => 'You have already voted on this poll.'
        ], 403);
    }

    // 6. Save vote
    PollVote::create([
        'user_id' => $userId,
        'content_id' => $content->id,
        'content_option_id' => $selectedOption->id,
    ]);

    // 7. Log activity (for leaderboard)
    UserActivity::create([
        'user_id' => $userId,
        'activity_type' => 'poll_vote',
        'content_id' => $content->id,
        'points' => 1,
    ]);

    // 8. Calculate results (reuse logic)
    $voteCounts = PollVote::where('content_id', $content->id)
        ->selectRaw('content_option_id, COUNT(*) as total')
        ->groupBy('content_option_id')
        ->pluck('total', 'content_option_id');

    $totalVotes = $voteCounts->sum();

    $results = $content->options->map(function ($opt) use ($voteCounts, $totalVotes) {
        $votes = $voteCounts[$opt->id] ?? 0;

        return [
            'option_id' => $opt->id,
            'votes' => $votes,
            'percentage' => $totalVotes > 0
                ? round(($votes / $totalVotes) * 100)
                : 0,
        ];
    })->values()->toArray();

    // 9. Return unified response (single source for frontend)
    return response()->json([
        'success' => true,
        'data' => [
            'hasVoted' => true,
            'selected_option_id' => $selectedOption->id,
            'results' => $results,
        ]
    ]);
}


   public function results($id)
    {
        $content = Content::with('options')->findOrFail($id);

        if ($content->type !== 'poll') {
            return response()->json(['message' => 'Not a poll.'], 400);
        }

        $voteCounts = PollVote::where('content_id', $content->id)
            ->selectRaw('content_option_id, COUNT(*) as total')
            ->groupBy('content_option_id')
            ->pluck('total', 'content_option_id');

        $totalVotes = $voteCounts->sum();

        $results = $content->options->map(function ($option) use ($voteCounts, $totalVotes) {

            $votes = $voteCounts[$option->id] ?? 0;

            return [
                'option_id' => $option->id,
                'option_text' => $option->option_text,
                'votes' => $votes,
                'percentage' => $totalVotes > 0
                    ? round(($votes / $totalVotes) * 100)
                    : 0,
            ];
        });

        return response()->json([
            'poll' => $content->title,
            'results' => $results,
            'total_votes' => $totalVotes,
        ]);
    }

    public function checkVote($pollId)
    {
        $hasVoted = PollVote::where('user_id', auth()->id())
                            ->where('content_id', $pollId)
                            ->exists();

        return response()->json(['hasVoted' => $hasVoted]);
    }

    public function leaderboard()
    {
        $leaders = User::select('id', 'name')
            ->withSum('activities as total_points', 'points')
            ->orderByDesc('total_points')
            ->take(10)
            ->get();

        return response()->json($leaders);
    }

public function myRank()
    {
        $userId = auth()->id();

        $users = User::withSum('activities as total_points', 'points')
            ->orderByDesc('total_points')
            ->get();

        $rank = $users->search(fn($u) => $u->id == $userId) + 1;

        return response()->json([
            'user_id' => $userId,
            'rank' => $rank,
            'points' => $users[$rank - 1]->total_points ?? 0,
        ]);
    }


public function list()
{
    $userId = auth()->id();

    $polls = Content::with(['options' => function($query) {
            $query->select('id', 'content_id', 'option_text');
        }])
        ->where('type', 'poll')
        ->where('is_active', true)
        ->where(function($query) {
            $query->whereNull('expire_at')
                  ->orWhere('expire_at', '>', now());
        })
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($poll) use ($userId) {

            $hasVoted = \App\Models\PollVote::where('user_id', $userId)
                ->where('content_id', $poll->id)
                ->exists();

            $selectedOptionId = \App\Models\PollVote::where('user_id', $userId)
                ->where('content_id', $poll->id)
                ->value('content_option_id');

            $totalVotes = \App\Models\PollVote::where('content_id', $poll->id)->count();

            $results = $poll->options->map(function ($option) use ($totalVotes) {
                $votes = \App\Models\PollVote::where('content_option_id', $option->id)->count();

                return [
                    'option_id' => $option->id,
                    'option_text' => $option->option_text,
                    'votes' => $votes,
                    'percentage' => $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0,
                ];
            });

            return [
                'id' => $poll->id,
                'title' => $poll->title,
                'description' => $poll->description,
                'options' => $poll->options,

                // 🔥 NEW FIELDS (CRITICAL)
                'hasVoted' => $hasVoted,
                'selected_option_id' => $selectedOptionId,
                'results' => $hasVoted ? $results : [],
            ];
        });

    return response()->json($polls);
}

}
