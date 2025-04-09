<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\ContentOption;
use App\Models\UserActivity;
use App\Models\ContentStat;
use App\Models\UserContentStat;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function submit(Request $request, Content $content)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.option_id' => 'required|integer|exists:content_options,id',
        ]);

        $user = auth()->user();

        // Check if already submitted
        $alreadySubmitted = UserActivity::where('user_id', $user->id)
            ->where('content_id', $content->id)
            ->where('activity_type', 'quiz_completed')
            ->exists();

        if ($alreadySubmitted) {
            return response()->json(['message' => 'You already completed this quiz.'], 403);
        }

        // ✅ Explicit use of ContentOption
        $correctOptions = ContentOption::where('content_id', $content->id)
            ->where('is_correct', true)
            ->pluck('id')
            ->toArray();

        $submittedAnswers = collect($request->answers);
        $correctCount = $submittedAnswers->whereIn('option_id', $correctOptions)->count();

        // Points logic
        $basePoints = 5;
        $bonusPoints = $correctCount * 10;
        $totalPoints = $basePoints + $bonusPoints;

        // Log activity
        UserActivity::create([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'activity_type' => 'quiz_completed',
            'points' => $totalPoints,
        ]);

        return response()->json([
            'message' => 'Quiz submitted!',
            'correct_answers' => $correctCount,
            'points_awarded' => $totalPoints,
        ]);
    }



    public function submitSingleAnswer(Request $request, Content $content)
{
    $user = auth()->user();

    $correctOption = $content->options()->where('is_correct', true)->first();
    $selectedOption = $request->input('option');

    $isCorrect = $selectedOption == $correctOption->id;

    // Store user attempt
    UserContentStat::create([
        'user_id' => $user->id,
        'content_id' => $content->id,
        'answered_correctly' => $isCorrect,
        'selected_options' => json_encode([$selectedOption]),
        'attempted_at' => now(),
    ]);

    // Update content stats
    $stats = ContentStat::firstOrCreate(['content_id' => $content->id]);
    $stats->increment('attempts');
    if ($isCorrect) {
        $stats->increment('correct_answers');
    }
    $optionCounts = $stats->option_counts ? json_decode($stats->option_counts, true) : [];
    $optionCounts[$selectedOption] = ($optionCounts[$selectedOption] ?? 0) + 1;
    $stats->option_counts = json_encode($optionCounts);
    $stats->save();

    return response()->json(['correct' => $isCorrect]);
}

}

