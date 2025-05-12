<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Content;
use App\Models\UserActivity;
use App\Models\ChatMessage;
use App\Models\Report;
use App\Models\PollVote;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DashboardOverview extends Component
{
    // Potential Curly Brace Issue Areas
    public $userCount;
    public $contentStats = [];
    public $quizStats = [];
    public $pollStats = [];
    public $chatStats = [];
    public $reportStats = [];

    // Closure and Array Potential Issues
    private function getQuizStats($start, $end): array
    {
        return [
            'avg_points' => round(UserActivity::where('activity_type', 'quiz_completed')
                ->whereBetween('created_at', [$start, $end])
                ->avg('points') ?? 0, 1),
            'most_attempted' => Content::with(['user'])
                ->where('type', 'quiz')
                ->whereBetween('created_at', [$start, $end])
                ->withCount(['activities' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }])
                ->orderByDesc('activities_count')
                ->first(),
        ];
    }

    private function getPollStats($start, $end): array
    {
        return [
            'total_votes' => PollVote::whereBetween('created_at', [$start, $end])->count(),
            'top_poll' => Content::with(['user'])
                ->where('type', 'poll')
                ->whereBetween('created_at', [$start, $end])
                ->withCount(['pollVotes' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }])
                ->orderByDesc('poll_votes_count')
                ->first(),
            'recent_votes' => PollVote::with(['user', 'content'])
                ->whereBetween('created_at', [$start, $end])
                ->latest()
                ->take($this->perPage)
                ->get()
        ];
    }
}