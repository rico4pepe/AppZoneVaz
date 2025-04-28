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
    $validated = $request->validate([
        'content_id' => 'required|exists:contents,id',
        'option_id' => 'required|exists:content_options,id',
    ]);

    $content = Content::where('id', $validated['content_id'])->where('type', 'poll')->firstOrFail();

    // Prevent multiple votes
    if (PollVote::where('user_id', auth()->id())->where('content_id', $content->id)->exists()) {
        return response()->json(['message' => 'You have already voted on this poll.'], 403);
    }

    PollVote::create([
        'user_id' => auth()->id(),
        'content_id' => $content->id,
        'content_option_id' => $validated['option_id'],
    ]);

    UserActivity::create([
        'user_id' => auth()->id(),
        'activity_type' => 'poll_vote',
        'content_id' => $content->id,
        'points' => 1, // Assuming 1 point for voting
    ]);


    return response()->json(['message' => 'Vote recorded successfully.']);
}


public function results($id)
{
    $content = Content::with('options')->findOrFail($id);

    if ($content->type !== 'poll') {
        return response()->json(['message' => 'Not a poll.'], 400);
    }

    $totalVotes = PollVote::where('content_id', $content->id)->count();

    $results = $content->options->map(function ($option) use ($totalVotes) {
        $votes = PollVote::where('content_option_id', $option->id)->count();
        $percentage = $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0;
        return [
            'option_id' => $option->id,
        'option_text' => $option->option_text,
        'votes' => $votes,
        'percentage' => $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0,
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
    $polls = Content::with(['options' => function($query) {
        $query->select('id', 'content_id', 'option_text');
    }])
    ->where('type', 'poll')
    ->where('is_active', true) // ✅ Only active polls
    // ✅ Only polls that haven't expired
    ->select('id', 'title', 'description', 'type', 'created_at')
    ->latest()
    ->take(5)
    ->get();

    return response()->json($polls);
}


}
