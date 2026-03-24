<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\PollVote;
use App\Models\UserActivity;
use App\Models\UserContentStat;
use Illuminate\Support\Facades\DB;

class ContentFeedController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $contents = Content::visible()
            ->with('options')
            ->latest()
            ->take(10)
            ->get();

        // =========================
        // POLLS
        // =========================
        $pollVotes = PollVote::where('user_id', $userId)->get();

        $votedPollIds = $pollVotes->pluck('content_id')->toArray();

        $pollResults = PollVote::whereIn('content_id', $votedPollIds)
            ->select('content_id', 'content_option_id', DB::raw('COUNT(*) as votes'))
            ->groupBy('content_id', 'content_option_id')
            ->get()
            ->groupBy('content_id');

        // =========================
        // QUIZ
        // =========================
        $quizStats = UserContentStat::where('user_id', $userId)
            ->get()
            ->keyBy('content_id');

        // =========================
        // TRIVIA
        // =========================
        $triviaStats = UserContentStat::where('user_id', $userId)
            ->get()
            ->keyBy('content_id');

        // =========================
        // TRANSFORM
        // =========================
        $data = $contents->map(function ($content) use ($pollVotes, $pollResults, $quizStats, $triviaStats) {

            $base = [
                'id'          => $content->id,
                'type'        => $content->type,
                'title'       => $content->title,
                'description' => $content->description,
            ];

            // ================= POLL =================
            if ($content->type === 'poll') {

                $hasVoted = in_array($content->id, $pollVotes->pluck('content_id')->toArray());

                $results = [];

                if ($hasVoted && isset($pollResults[$content->id])) {
                    $contentVotes = $pollResults[$content->id];
                    $totalVotes = $contentVotes->sum('votes');

                    $results = $content->options->map(function ($option) use ($contentVotes, $totalVotes) {
                        $optionVote = $contentVotes->firstWhere('content_option_id', $option->id);
                        $votes = $optionVote ? $optionVote->votes : 0;

                        return [
                            'option_id'   => $option->id,
                            'votes'       => $votes,
                            'percentage'  => $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0,
                        ];
                    })->values()->toArray();
                }

                $userVote = $pollVotes->firstWhere('content_id', $content->id);

                return [
                    ...$base,
                    'options'            => $content->options,
                    'hasVoted'           => $hasVoted,
                    'selected_option_id' => $userVote->content_option_id ?? null,
                    'results'            => $results,
                    'user_state' => [
                        'played' => $hasVoted,
                        'answer' => $userVote->content_option_id ?? null,
                        'is_correct' => null, // not applicable for poll
                    ],
                ];
            }

            // ================= QUIZ =================
            if ($content->type === 'quiz') {

                $stat = $quizStats[$content->id] ?? null;

                $correctOption = $content->options->firstWhere('is_correct', true);

                return [
                    ...$base,
                    'options'            => $content->options,
                    'hasAnswered'        => $stat !== null,
                    'selected_option_id' => $stat ? json_decode($stat->selected_options)[0] ?? null : null,
                    'correct_option_id'  => $correctOption?->id,
                    'isCorrect'          => $stat->answered_correctly ?? false,
                    // 🔥 NEW STRUCTURE
                    'user_state' => [
                        'played' => $stat !== null,
                        'answer' => $stat ? json_decode($stat->selected_options)[0] ?? null : null,
                        'is_correct' => $stat->answered_correctly ?? false,
                    ],
                ];
            }

            // ================= TRIVIA =================
            if ($content->type === 'trivia') {

                $stat = $triviaStats[$content->id] ?? null;

                $correctOption = $content->options->firstWhere('is_correct', true);

                return [
                    ...$base,
                    'hasAnswered'   => $stat !== null,
                    'user_answer'   => $stat ? json_decode($stat->selected_options)[0] ?? null : null,
                    'correct_answer'=> $correctOption?->option_text,
                    'isCorrect'     => $stat->answered_correctly ?? false,
                    // 🔥 NEW STRUCTURE
                    'user_state' => [
                        'played' => $stat !== null,
                        'answer' => $stat ? json_decode($stat->selected_options)[0] ?? null : null,
                        'is_correct' => $stat->answered_correctly ?? false,
                    ],
                ];
            }

            return $base;
        });

        return response()->json($data);
    }
}