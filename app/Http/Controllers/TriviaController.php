<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\UserActivity;
use App\Models\UserContentStat;
use App\Models\ContentStat;

class TriviaController extends Controller
{
    public function answer(Request $request, Content $content)
    {
        abort_if(!$content->isInteractable(), 403);

        $request->validate([
            'answer' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        $already = UserActivity::where('user_id', $user->id)
            ->where('content_id', $content->id)
            ->where('activity_type', 'trivia_answered')
            ->exists();

        if ($already) {
            return response()->json(['message' => 'Already answered this trivia.'], 403);
        }

        $correctOption = $content->options()
            ->where('is_correct', true)
            ->firstOrFail();

        $userAnswer = strtolower(trim($request->answer));
        $correctAnswer = strtolower(trim($correctOption->option_text));

        $isCorrect = $userAnswer === $correctAnswer;

        UserActivity::create([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'activity_type' => 'trivia_answered',
            'points' => $isCorrect ? 10 : 0
        ]);

        UserContentStat::create([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'answered_correctly' => $isCorrect,
            'selected_options' => json_encode([$request->answer]),
            'attempted_at' => now(),
        ]);

        $stats = ContentStat::firstOrCreate(['content_id' => $content->id]);

        $stats->increment('attempts');

        if ($isCorrect) {
            $stats->increment('correct_answers');
        }

        return response()->json([
            'correct' => $isCorrect,
            'correct_answer' => $correctOption->option_text
        ]);
    }
}
