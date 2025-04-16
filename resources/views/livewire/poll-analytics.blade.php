@extends('layouts.admin.layout')

@section('content')
<div class="main-content">
<div class="container mt-4">
    <h4>Poll Analytics</h4>

    <div class="d-flex gap-3 mb-4">
        <div>
            <label class="form-label">Start Date</label>
            <input type="date" wire:model="startDate" class="form-control">
        </div>
        <div>
            <label class="form-label">End Date</label>
            <input type="date" wire:model="endDate" class="form-control">
        </div>
    </div>

    <div class="row">
        @forelse($polls as $poll)
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">{{ $poll->title }} ({{ $poll->poll_votes_count }} votes)</div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($poll->options as $option)
                                <li class="list-group-item d-flex justify-content-between">
                                    {{ $option->option_text }}
                                    <span class="badge bg-primary">
                                        {{ $option->pollVotes()->count() }} votes
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No polls found in this period.</p>
        @endforelse
    </div>
</div>
</div>
@endsection

