<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserReport extends Component
{
    use WithPagination;

    public $search = '';
    public $plan;
    public $team;
    public $status;

    protected $updatesQueryString = ['search', 'plan', 'team', 'status'];

    public function updatingSearch()
    {
        $this->resetPage(); // Reset to first page on search
    }

    public function render()
    {
        $query = User::with(['team', 'subscriptionRenewalLogs'])
            ->withCount([
                'activities as quiz_count' => fn ($q) => $q->where('activity_type', 'quiz_completed'),
                'activities as trivia_count' => fn ($q) => $q->where('activity_type', 'trivia_completed'),
                'pollVotes',
            ])
            ->when($this->plan, fn($q) => $q->where('plan', $this->plan))
            ->when($this->team, fn($q) => $q->where('team', $this->team))
            ->when($this->status, function ($q) {
                $now = now();
                return $this->status === 'active'
                    ? $q->where('expires_at', '>', $now)
                    : $q->where('expires_at', '<=', $now);
            })
            ->when($this->search, function ($q) {
                $term = $this->search;
                $q->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%$term%")
                          ->orWhere('phone_number', 'like', "%$term%");
                });
            });

        $users = $query->latest()->paginate(25);

        return view('livewire.user-report', compact('users'));
    }
}
