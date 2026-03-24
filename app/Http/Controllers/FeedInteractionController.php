<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\PollVote;
use App\Models\UserActivity;
use App\Models\UserContentStat;
use Illuminate\Support\Facades\DB;

class FeedInteractionController extends Controller
{
    //
            public function interact(Request $request)
        {
            $validated = $request->validate([
                'content_id' => 'required|exists:contents,id',
                'answer' => 'required'
            ]);

            $user = auth()->user();

            // 1. Fetch content
            $content = Content::with('options')->findOrFail($validated['content_id']);

            // 2. Check interactable
            abort_if(!$content->isInteractable(), 403);

            // 3. Route by type
            return match ($content->type) {
                'poll'   => $this->handlePoll($user, $content, $validated['answer']),
                'quiz'   => $this->handleQuiz($user, $content, $validated['answer']),
                'trivia' => $this->handleTrivia($user, $content, $validated['answer']),
                default  => response()->json([
                    'success' => false,
                    'message' => 'Unsupported content type'
                ], 400),
            };
        }

        private function handlePoll($user, $content, $answer)
        {
            // Prevent duplicate
            if (PollVote::where('user_id', $user->id)
                ->where('content_id', $content->id)
                ->exists()) {

                return response()->json([
                    'success' => false,
                    'message' => 'You already voted'
                ], 403);
            }

            // Validate option
            $option = $content->options->firstWhere('id', $answer);
            abort_if(!$option, 400);

            // Save vote
            PollVote::create([
                'user_id' => $user->id,
                'content_id' => $content->id,
                'content_option_id' => $option->id,
            ]);

            // Activity
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'poll_vote',
                'content_id' => $content->id,
                'points' => 1,
            ]);

            // Results
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

            return response()->json([
                'success' => true,
                'data' => [
                    'played' => true,
                    'answer' => $option->id,
                    'results' => $results,
                    'is_correct' => null
                ]
            ]);
        }

        private function handleQuiz($user, $content, $answer)
        {
            $existing = UserContentStat::where('user_id', $user->id)
                ->where('content_id', $content->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already answered'
                ], 403);
            }

            $correctOption = $content->options->firstWhere('is_correct', true);

            $isCorrect = $correctOption && $correctOption->id == $answer;

            UserContentStat::create([
                'user_id' => $user->id,
                'content_id' => $content->id,
                'selected_options' => json_encode([$answer]),
                'answered_correctly' => $isCorrect,
                'attempted_at' => now(), // ✅ FIX
            ]);

            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'quiz_answer',
                'content_id' => $content->id,
                'points' => $isCorrect ? 2 : 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'played' => true,
                    'answer' => $answer,
                    'is_correct' => $isCorrect,
                    'correct_option_id' => $correctOption?->id
                ]
            ]);
        }

        private function handleTrivia($user, $content, $answer)
        {
            $existing = UserContentStat::where('user_id', $user->id)
                ->where('content_id', $content->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already answered'
                ], 403);
            }

            $correctOption = $content->options->firstWhere('is_correct', true);

            $correctAnswer = strtolower(trim($correctOption?->option_text ?? ''));
            $userAnswer = strtolower(trim($answer));

            $isCorrect = $correctAnswer === $userAnswer;

            UserContentStat::create([
                'user_id' => $user->id,
                'content_id' => $content->id,
                'selected_options' => json_encode([$answer]),
                'answered_correctly' => $isCorrect,
                'attempted_at' => now(), // ✅ FIX
            ]);

            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'trivia_answer',
                'content_id' => $content->id,
                'points' => $isCorrect ? 3 : 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'played' => true,
                    'answer' => $answer,
                    'is_correct' => $isCorrect,
                    'correct_answer' => $correctOption?->option_text
                ]
            ]);
        }
}
