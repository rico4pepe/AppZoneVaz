
<div class="main-content">
<div class="container mt-4">
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Top Users – Leaderboard</h5>
            <div class="d-flex gap-2">
                <select wire:model="period" class="form-select w-auto">
                    <option value="all">All Time</option>
                    <option value="month">This Month</option>
                    <option value="week">This Week</option>
                </select>
                <select wire:model="activityType" class="form-select w-auto">
                    <option value="">All Activities</option>
                    <option value="quiz_completed">Quiz</option>
                    <option value="poll_vote">Poll</option>
                    <option value="trivia_completed">Trivia</option>
                    <option value="chat_message">Chat</option>
                </select>
            </div>
        </div>
    </div>

    {{-- @if($myRank)
    <div class="alert alert-info">
        Your Rank: <strong>#{{ $myRank['rank'] }}</strong>,
        Points: <strong>{{ $myRank['points'] }}</strong>,
        Name: <strong>{{ $myRank['name'] }}</strong>
    </div>
    @endif --}}

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rank</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td>#{{ $index + 1 }}</td>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['points'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No data found for this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>



