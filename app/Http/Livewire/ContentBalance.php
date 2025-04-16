<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Content;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;

class ContentBalance extends Component
{
    public $type = ''; // poll, quiz, trivia
    public $startDate;
    public $endDate;

    public $data = [];

    public function mount()
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        $this->loadData();
    }

    public function updatedType()
    {
        $this->loadData();
    }

    public function updatedStartDate()
    {
        $this->loadData();
    }

    public function updatedEndDate()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $query = Content::with(['league', 'team'])
            ->whereBetween('created_at', [$start, $end]);

        if ($this->type) {
            $query->where('type', $this->type);
        }

        $contents = $query->get();

        $this->data = $contents->groupBy(function ($item) {
            return $item->league->name ?? 'Unassigned';
        })->map(function ($group) {
            return $group->groupBy(function ($item) {
                return $item->team->name ?? 'General';
            })->map(function ($subgroup) {
                return $subgroup->count();
            });
        })->toArray();
    }

    public function render()
    {
        return view('livewire.content-balance');
    }
}

