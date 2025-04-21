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
        $request->validate([
            'option_id' => 'required|integer|exists:content_options,id',
        ]);

        $user = auth()->user();

        // Check if already answered
        $already = UserActivity::where('user_id', $user->id)
            ->where('content_id', $content->id)
            ->where('activity_type', 'quiz_answered')
            ->exists();

        if ($already) {
            return response()->json(['message' => 'Already answered this quiz.'], 403);
        }

        $correctOption = $content->options()->where('is_correct', true)->first();
        $selected = $request->option_id;
        $isCorrect = $correctOption && $selected == $correctOption->id;

        // Track answer
        UserActivity::create([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'activity_type' => 'quiz_answered',
            'points' => $isCorrect ? 10 : 0
        ]);

        UserContentStat::create([
            'user_id' => $user->id,
            'content_id' => $content->id,
            'answered_correctly' => $isCorrect,
            'selected_options' => json_encode([$selected]),
            'attempted_at' => now(),
        ]);

        // Update stats
        $stats = ContentStat::firstOrCreate(['content_id' => $content->id]);
        $stats->increment('attempts');
        if ($isCorrect) {
            $stats->increment('correct_answers');
        }

        $counts = json_decode($stats->option_counts, true) ?? [];
        $counts[$selected] = ($counts[$selected] ?? 0) + 1;
        $stats->option_counts = json_encode($counts);
        $stats->save();

        return response()->json([
            'correct' => $isCorrect,
            'correct_option_id' => $correctOption->id,
        ]);
    }

public function list()
{
    $quizzes = Content::with('options')
        ->where('type', 'quiz')
        ->where('is_active', true)
        ->select('id', 'title', 'description')
        ->latest()
        ->take(5)
        ->get();

    return response()->json($quizzes);
}

public function checkAnswered($id)
{
    $userId = auth()->id();
    $activity = UserActivity::where('user_id', $userId)
        ->where('content_id', $id)
        ->where('activity_type', 'quiz_answered')
        ->first();

    $hasAnswered = $activity !== null;
    
    if (!$hasAnswered) {
        return response()->json(['answered' => false]);
    }
    
    // Get the correct option and selected answer details
    $quiz = Content::with('options')->findOrFail($id);
    $correctOption = $quiz->options->where('is_correct', true)->first();
    
    $stat = UserContentStat::where('user_id', $userId)
        ->where('content_id', $id)
        ->latest()
        ->first();
        
    $selectedOption = $stat && $stat->selected_options ? json_decode($stat->selected_options)[0] : null;
    
    return response()->json([
        'answered' => true,
        'correct' => $stat->answered_correctly ?? false,
        'correct_option_id' => $correctOption?->id,
        'selected_option_id' => $selectedOption
    ]);
}

public function answeredDetail($id)
{
    $userId = auth()->id();

    $quiz = Content::with('options')->findOrFail($id);

    $activity = UserActivity::where('user_id', $userId)
        ->where('content_id', $id)
        ->where('activity_type', 'quiz_answered')
        ->first();

    if (!$activity) {
        return response()->json(['answered' => false]);
    }

    $stat = UserContentStat::where('user_id', $userId)
        ->where('content_id', $id)
        ->latest()
        ->first();

    $correctOption = $quiz->options->where('is_correct', true)->first();
    $selectedOption = optional($stat)->selected_options ? json_decode($stat->selected_options)[0] : null;

    return response()->json([
        'answered' => true,
        'correct' => $stat->answered_correctly ?? false,
        'correct_option_id' => $correctOption?->id,
        'selected_option_id' => $selectedOption
    ]);
}



}

