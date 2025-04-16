<?php

namespace App\Http\Livewire;

use App\Models\Content;
use Livewire\Component;
use Livewire\WithPagination;

class ContentList extends Component
{
    use WithPagination;

    public $filterType = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        session()->flash('message', 'Edit clicked for ID: ' . $id);
        // Logic for redirecting to edit page or emitting event to another component
        return redirect()->route('admin.content.edit', ['id' => $id]);
    }

    public function viewStats($id)
    {
        session()->flash('message', 'Stats clicked for ID: ' . $id);
        // Show stats modal or redirect
    }

    public function confirmDelete($id)
    {
        $content = Content::find($id);
        if ($content) {
            $content->delete();
            session()->flash('message', 'Content deleted successfully.');
        }
    }

    public function render()
    {
        $contents = Content::when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.content-list', compact('contents'));
    }
}
