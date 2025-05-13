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

    public $userChartLabels = [];
    public $userChartData = [];

    public $quizAvgChartLabels = [];
    public $quizAvgChartData = [];


    public $contentChartLabels = [];
    public $contentChartData = [];

            public $pollChartLabels = [];
        public $pollChartData = [];

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

       // $this->userCount = User::whereBetween('created_at', [$start, $end])->count();
       $this->userCount = $this->getUserCount($start, $end); 
       

        $this->contentStats = [
            'total' => Content::whereBetween('created_at', [$start, $end])->count(),
            'polls' => Content::where('type', 'poll')->whereBetween('created_at', [$start, $end])->count(),
            'quizzes' => Content::where('type', 'quiz')->whereBetween('created_at', [$start, $end])->count(),
            'trivias' => Content::where('type', 'trivia')->whereBetween('created_at', [$start, $end])->count(),
        ];


        // load charts
        $this->contentChartLabels = ['Polls', 'Quizzes', 'Trivias'];
        $this->contentChartData = [
            $this->contentStats['polls'],
            $this->contentStats['quizzes'],
            $this->contentStats['trivias']
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

            $this->loadQuizAverages($start, $end);


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

        $this->loadPollTrends($start, $end);

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

                private function getUserCount($start, $end): int
            {
                $dailyCounts = User::whereBetween('created_at', [$start, $end])
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('count', 'date')
                    ->toArray();

                $this->userChartLabels = array_keys($dailyCounts);
                $this->userChartData = array_values($dailyCounts);

                return array_sum($dailyCounts);
            }

                                        private function loadQuizAverages($start, $end): void
                        {
                            $averages = UserActivity::where('activity_type', 'quiz_completed')
                                ->whereBetween('created_at', [$start, $end])
                                ->selectRaw('DATE(created_at) as day, AVG(points) as avg_score')
                                ->groupBy('day')
                                ->orderBy('day')
                                ->pluck('avg_score', 'day')
                                ->toArray();

                            $this->quizAvgChartLabels = array_keys($averages);
                            $this->quizAvgChartData = array_map(fn($v) => round($v, 1), array_values($averages));
                        }

                    private function loadPollTrends($start, $end): void
                    {
                        $votes = \App\Models\PollVote::whereBetween('created_at', [$start, $end])
                            ->selectRaw('DATE(created_at) as day, COUNT(*) as vote_count')
                            ->groupBy('day')
                            ->orderBy('day')
                            ->pluck('vote_count', 'day')
                            ->toArray();

                        $this->pollChartLabels = array_keys($votes);
                        $this->pollChartData = array_values($votes);
                    }


    public function render()
    {
        return view('livewire.dashboard-overview');
    }
}


