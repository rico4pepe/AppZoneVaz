<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Content;
use App\Models\PollVote;
use Carbon\Carbon;

class PollAnalytics extends Component
{
    public $polls = [];
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->loadPolls();
    }

    public function updatedStartDate()
    {
        $this->loadPolls();
    }

    public function updatedEndDate()
    {
        $this->loadPolls();
    }

    public function loadPolls()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $this->polls = Content::withCount(['pollVotes' => function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end]);
            }])
            ->where('type', 'poll')
            ->whereBetween('created_at', [$start, $end])
            ->with('options')
            ->orderByDesc('poll_votes_count')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.poll-analytics');
    }
}
