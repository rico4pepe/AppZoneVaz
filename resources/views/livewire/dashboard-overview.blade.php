@extends('layouts.admin.main_layout')

@section('content')
<div class="main-content">
<div class="container mt-4">
    <h4 class="mb-3">Admin Dashboard Overview</h4>

    <div class="d-flex gap-3 align-items-center mb-3">
    <div>
        <label class="form-label">Start Date</label>
        <input type="date" wire:model="startDate" class="form-control">
    </div>
    <div>
        <label class="form-label">End Date</label>
        <input type="date" wire:model="endDate" class="form-control">
    </div>
</div>


    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <p class="fs-4">{{ $userCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h5>Total Content</h5>
                    <p>Polls: {{ $contentStats['polls'] }}</p>
                    <p>Quizzes: {{ $contentStats['quizzes'] }}</p>
                    <p>Trivias: {{ $contentStats['trivias'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Quiz Stats</h5>
                    <p>Avg Points: {{ $quizStats['avg_points'] }}</p>
                    <p>Top: {{ $quizStats['most_attempted']?->title ?? '—' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Polls</h5>
                    <p>Total Votes: {{ $pollStats['total_votes'] }}</p>
                    <p>Top: {{ $pollStats['top_poll']?->title ?? '—' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning">
                <div class="card-body">
                    <h5>Chat Stats</h5>
                    <p>Total Messages: {{ $chatStats['messages'] }}</p>
                    <p>Top User: {{ $chatStats['top_user']?->user->display_name ?? '—' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Reports</h5>
                    <p>Pending: {{ $reportStats['pending'] }}</p>
                    <p>Banned Users: {{ $reportStats['banned_users'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

