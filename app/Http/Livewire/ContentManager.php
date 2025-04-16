<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Content;
use App\Models\ContentOption;
use Carbon\Carbon;

class ContentManager extends Component
{

    public $type, $title, $description, $options = [], $correctOption = null;
    public $publishAt, $isFeatured = false, $isActive = true;
    public $contentId = null;

    public function mount($id = null)
{
    if ($id) {
        $this->loadContent($id);
    } else {
        $this->options = [['text' => '']];
    }
}

    public function addOption()
    {
        $this->options[] = ['text' => ''];
    }

    public function removeOption($index)
    {
        // Check if the option being removed is the selected correct option
        // Add this check:
        if ($this->type === 'quiz' && $this->correctOption !== null && $this->correctOption == $index) {
            $this->correctOption = null; // Reset the correct option index
        }
    
        unset($this->options[$index]);
        $this->options = array_values($this->options); // Re-index the array
    }



    protected function loadContent($id)
{
    $content = Content::with('options')->findOrFail($id);

    $this->contentId = $id;
    $this->type = $content->type;
    $this->title = $content->title;
    $this->description = $content->description;
    $this->publishAt = optional($content->published_at)->format('Y-m-d\TH:i');
    $this->isFeatured = $content->is_featured;
    $this->isActive = $content->is_active;
    $this->options = $content->options->map(function ($opt) {
        return ['text' => $opt->option_text];
    })->toArray();

    if ($content->type === 'quiz') {
        $this->correctOption = $content->options->search(function ($opt) {
            return $opt->is_correct;
        });
    }
}



    public function save()
    {
        $this->validate([
            'type' => 'required|in:poll,quiz,trivia',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string|max:255',
            'publishAt' => 'nullable|date',
        ]);

        if ($this->type === 'quiz') {
            if ($this->correctOption === null || !isset($this->options[$this->correctOption])) {
                $this->addError('correctOption', 'Please select the correct answer.');
                return;
            }
        }

        
            if ($this->contentId) {
                $content = Content::findOrFail($this->contentId);
                $content->update([
                    'type' => $this->type,
                    'title' => $this->title,
                    'description' => $this->description,
                    'published_at' => $this->publishAt ? Carbon::parse($this->publishAt) : null,
                    'is_featured' => $this->isFeatured,
                    'is_active' => $this->isActive,
                ]);
                $content->options()->delete(); // Remove old options
        }else{

            $content = Content::create([
                'type' => $this->type,
                'title' => $this->title,
                'description' => $this->description,
                'published_at' => $this->publishAt ? Carbon::parse($this->publishAt) : null,
                'is_featured' => $this->isFeatured,
                'is_active' => $this->isActive,
            ]);
        }

        

        foreach ($this->options as $index => $option) {
            ContentOption::create([
                'content_id' => $content->id,
                'option_text' => $option['text'],
                'is_correct' => $this->type === 'quiz' && $this->correctOption == $index,
            ]);
        }

        session()->flash('message', 'Content created successfully.');
        $this->reset(['type', 'title', 'description', 'options', 'correctOption', 'publishAt', 'isFeatured', 'isActive']);
        $this->options = [['text' => '']];
    }



    public function render()
    {
        return view('livewire.content-manager');
    }
}
