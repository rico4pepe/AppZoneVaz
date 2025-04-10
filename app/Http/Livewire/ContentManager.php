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

    public function mount()
    {
        $this->options = [['text' => '']];
    }

    public function addOption()
    {
        $this->options[] = ['text' => ''];
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
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

        $content = Content::create([
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'published_at' => $this->publishAt ? Carbon::parse($this->publishAt) : null,
            'is_featured' => $this->isFeatured,
            'is_active' => $this->isActive,
        ]);

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
