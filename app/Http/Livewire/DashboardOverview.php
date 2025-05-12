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

class DashboardOverview extends Component
{
    public $userCount;
    public $contentStats = [];
    public $quizStats = [];
    public $pollStats = [];
    public $chatStats = [];
    public $reportStats = [];

    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->loadStats();
    }

    public function updatedStartDate()
    {
        $this->loadStats();
    }

    public function updatedEndDate()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $this->userCount = User::whereBetween('created_at', [$start, $end])->count();

        $this->contentStats = [
            'total' => Content::whereBetween('created_at', [$start, $end])->count(),
            'polls' => Content::where('type', 'poll')->whereBetween('created_at', [$start, $end])->count(),
            'quizzes' => Content::where('type', 'quiz')->whereBetween('created_at', [$start, $end])->count(),
            'trivias' => Content::where('type', 'trivia')->whereBetween('created_at', [$start, $end])->count(),
        ];

        $this->quizStats = [
            'avg_points' => round(UserActivity::where('activity_type', 'quiz_completed')
                ->whereBetween('created_at', [$start, $end])->avg('points'), 1),
            'most_attempted' => Content::where('type', 'quiz')
                ->whereBetween('created_at', [$start, $end])
                ->withCount(['activities' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }])
                ->orderByDesc('activities_count')
                ->first(),
        ];

        $this->pollStats = [
            'total_votes' => PollVote::whereBetween('created_at', [$start, $end])->count(),
            'top_poll' => Content::where('type', 'poll')
                ->whereBetween('created_at', [$start, $end])
                ->withCount(['pollVotes' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }])
                ->orderByDesc('poll_votes_count')
                ->first(),
        ];

        $this->chatStats = [
            'messages' => ChatMessage::whereBetween('created_at', [$start, $end])->count(),
            'top_user' => ChatMessage::selectRaw('user_id, count(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->with('user')
                ->first(),
        ];

        $this->reportStats = [
            'pending' => Report::where('status', 'pending')->whereBetween('created_at', [$start, $end])->count(),
            'banned_users' => \App\Models\ChatBan::whereBetween('created_at', [$start, $end])->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-overview');
    }
}


