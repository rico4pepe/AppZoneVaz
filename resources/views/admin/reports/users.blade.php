@extends('layouts.admin.main_layout')

@section('content')
<div class="main-content">
<div class="container mt-4">
    <h4>User Report</h4>
    <form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="plan" class="form-select">
            <option value="">-- Filter by Plan --</option>
            <option value="Daily">Daily</option>
            <option value="Weekly">Weekly</option>
            <option value="Monthly">Monthly</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="team" class="form-select">
            <option value="">-- Filter by Team --</option>
            @foreach(\App\Models\Team::all() as $team)
                <option value="{{ $team->id }}">{{ $team->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">-- Subscription Status --</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100">Apply Filters</button>
    </div>
</form>

    <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Plan</th>
                <th>Subscription</th>
                <th>Team</th>
                <th>Quiz</th>
                <th>Trivia</th>
                <th>Polls</th>
                <th>Auto Renew</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name ?? '—' }}</td>
                    <td>{{ $user->phone_number }}</td>
                    <td>{{ $user->plan }}</td>
                    <td>
                        @if ($user->expires_at && now()->lessThan($user->expires_at))
                            <span class="text-success">Active</span><br>
                            <small>Expires: {{ $user->expires_at->format('d M Y') }}</small>
                        @else
                            <span class="text-danger">Expired</span><br>
                            <small>Expired: {{ optional($user->expires_at)->format('d M Y') }}</small>
                        @endif
                    </td>
                    <td>{{ optional($user->team)->name ?? '—' }}</td>
                    <td>{{ $user->quiz_count }}</td>
                    <td>{{ $user->trivia_count }}</td>
                    <td>{{ $user->poll_votes_count }}</td>
                    <td>{{ $user->auto_renew ? '✅' : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

   {{-- ✅ Pagination --}}
<div class="mt-3">
    {{ $users->links('pagination::bootstrap-5') }}
</div>
</div>
</div>
@endsection
