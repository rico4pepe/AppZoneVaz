<?php


namespace App\Http\Livewire;

use App\Models\User;
use App\Models\UserActivity;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Leaderboard extends Component
{
    public $period = 'all';
    public $activityType = '';
    public $users = [];

    public function mount()
    {
        $this->fetchLeaderboard();
    }

    public function updatedPeriod()
    {
        $this->fetchLeaderboard();
    }

    public function updatedActivityType()
    {
        $this->fetchLeaderboard();
    }

    public function fetchLeaderboard()
    {
        $query = UserActivity::select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points');

        if ($this->activityType) {
            $query->where('activity_type', $this->activityType);
        }

        if ($this->period === 'week') {
            $query->where('created_at', '>=', now()->startOfWeek());
        } elseif ($this->period === 'month') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        $rows = $query->take(10)->get();

        $this->users = $rows->map(function ($row) {
            $user = User::find($row->user_id);
            return [
                'id' => $user->id,
                'name' => $user->display_name ?? $user->name,
                'points' => $row->total_points,
            ];
        });
    }

    public function getMyRankProperty()
    {
        $query = UserActivity::select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points');

        if ($this->activityType) {
            $query->where('activity_type', $this->activityType);
        }

        if ($this->period === 'week') {
            $query->where('created_at', '>=', now()->startOfWeek());
        } elseif ($this->period === 'month') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        $rows = $query->get();

        $rank = $rows->search(fn($row) => $row->user_id === auth()->id());

        return $rank !== false ? [
            'rank' => $rank + 1,
            'points' => $rows[$rank]->total_points,
            'name' => auth()->user()->display_name ?? auth()->user()->name,
        ] : null;
    }

    public function render()
    {
        return view('livewire.leaderboard');
    }
}
