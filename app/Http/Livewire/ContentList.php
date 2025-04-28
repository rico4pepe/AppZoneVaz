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
    public $contentToDelete = null;

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        session()->flash('message', 'Edit clicked for ID: ' . $id);
        // Logic for redirecting to edit page or emitting event to another component
        return redirect()->route('admin.dashboard', ['id' => $id]);
    }

    public function viewStats($id)
    {
        session()->flash('message', 'Stats clicked for ID: ' . $id);
        // Show stats modal or redirect
    }

    public function confirmDelete($id)
{
    $this->contentToDelete = $id;
    //$this->dispatchBrowserEvent('show-delete-modal');
    $this->dispatchBrowserEvent('show-sweet-alert');
}


public function deleteConfirmed()
{
    if ($this->contentToDelete) {
        $content = Content::find($this->contentToDelete);
        if ($content) {
            $content->delete();
        }

        $this->contentToDelete = null;
        session()->flash('message', 'Content deleted successfully.');
        $this->dispatchBrowserEvent('deleted');
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
