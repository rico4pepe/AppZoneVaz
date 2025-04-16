@extends('layouts.admin.layout')

@section('content')
<div class="main-content">
<div class="container mt-4">
    <h4>Content Balance by League & Team</h4>

    <div class="d-flex gap-3 mb-3">
        <div>
            <label>Start Date</label>
            <input type="date" wire:model="startDate" class="form-control">
        </div>
        <div>
            <label>End Date</label>
            <input type="date" wire:model="endDate" class="form-control">
        </div>
        <div>
            <label>Type</label>
            <select wire:model="type" class="form-select">
                <option value="">All</option>
                <option value="poll">Poll</option>
                <option value="quiz">Quiz</option>
                <option value="trivia">Trivia</option>
            </select>
        </div>
    </div>

    @if (count($data))
        @foreach ($data as $league => $teams)
            <div class="card mb-3">
                <div class="card-header fw-bold">{{ $league }}</div>
                <div class="card-body">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Team</th>
                                <th># of Contents</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teams as $team => $count)
                                <tr>
                                    <td>{{ $team }}</td>
                                    <td>{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">No content found in this period.</p>
    @endif
</div>
</div>
@endsection

