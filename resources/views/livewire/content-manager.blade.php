@extends('layouts.admin.layout')
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Create New Content</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select wire:model="type" class="form-select" id="type">
                        <option value="">Select Type</option>
                        <option value="poll">Poll</option>
                        <option value="quiz">Quiz</option>
                        <option value="trivia">Trivia</option>
                    </select>
                    @error('type') <small class="text-danger">{{ \$message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input wire:model="title" type="text" class="form-control" id="title">
                    @error('title') <small class="text-danger">{{ \$message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea wire:model="description" class="form-control" id="description"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Options</label>
                    @foreach($options as $index => $option)
                        <div class="d-flex align-items-center mb-2">
                            <input wire:model="options.{{ \$index }}.text" type="text" class="form-control me-2" placeholder="Option text">
                            @if($type === 'quiz')
                                <input type="radio" wire:model="correctOption" value="{{ \$index }}" class="form-check-input me-2">
                            @endif
                            <button type="button" wire:click="removeOption({{ \$index }})" class="btn btn-danger btn-sm">&times;</button>
                        </div>
                    @endforeach
                    <button type="button" wire:click="addOption" class="btn btn-secondary btn-sm">Add Option</button>
                    @error('options') <div class="text-danger mt-1">{{ \$message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="publish_at" class="form-label">Publish At</label>
                    <input wire:model="publishAt" type="datetime-local" class="form-control" id="publish_at">
                </div>

                <div class="form-check mb-3">
                    <input wire:model="isFeatured" class="form-check-input" type="checkbox" id="featured">
                    <label class="form-check-label" for="featured">Feature this content</label>
                </div>

                <div class="form-check mb-3">
                    <input wire:model="isActive" class="form-check-input" type="checkbox" id="active" checked>
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Save Content</button>
            </form>
        </div>
    </div>
</div>
@endsection
