<div class="main-content">

    <div class="container mt-4">
        <h4 class="mb-3">Admin Dashboard Overview</h4>

        

        {{-- Date Filter --}}
        <div class="card mb-4">
            <div class="card-body d-flex flex-wrap gap-3 align-items-end">
                <div>
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div>
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>
            </div>
            {{--- Date Filter Buttons --}}
        </div>

        {{-- Pie Chart --}}
        <div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">User Registrations (Last 30 Days)</h5>
        <canvas id="userChart" width="400" height="200"></canvas>
    </div>
</div>

        {{-- Statistics Cards --}}

        <div class="row g-3">
            {{-- Total Users --}}
            <div class="col-md-3">
                <div class="card text-white bg-primary shadow-sm">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <p class="fs-4 mb-0">{{ $userCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Content Breakdown --}}
            <div class="col-md-3">
                <div class="card text-white bg-dark shadow-sm">
                    <div class="card-body">
                        <h5>Total Content</h5>
                        <p>Polls: {{ $contentStats['polls'] ?? 0 }}</p>
                        <p>Quizzes: {{ $contentStats['quizzes'] ?? 0 }}</p>
                        <p>Trivias: {{ $contentStats['trivias'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            {{-- Quiz Stats --}}
            <div class="col-md-3">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <h5>Quiz Stats</h5>
                        <p>Avg Points: {{ $quizStats['avg_points'] ?? '0.0' }}</p>
                        <p title="Most Attempted Quiz">
                            Top: {{ optional($quizStats['most_attempted'])->title ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Poll Stats --}}
            <div class="col-md-3">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body">
                        <h5>Polls</h5>
                        <p>Total Votes: {{ $pollStats['total_votes'] ?? 0 }}</p>
                        <p title="Most Voted Poll">
                            Top: {{ optional($pollStats['top_poll'])->title ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Chat Stats --}}
            <div class="col-md-4">
                <div class="card bg-warning shadow-sm">
                    <div class="card-body">
                        <h5>Chat Stats</h5>
                        <p>Total Messages: {{ $chatStats['messages'] ?? 0 }}</p>
                        <p>
                            Top User:
                            <span title="User ID: {{ optional($chatStats['top_user'])->user->id ?? '' }}">
                                {{ optional($chatStats['top_user'])->user->display_name ?? '—' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Report Stats --}}
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow-sm">
                    <div class="card-body">
                        <h5>Reports</h5>
                        <p>Pending: {{ $reportStats['pending'] ?? 0 }}</p>
                        <p>Banned Users: {{ $reportStats['banned_users'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--- End of Container  hdghg  hjhh djhd djd ---}}

</div>
@push('scripts')
<script>
document.addEventListener('livewire:load', function () {
    const ctx = document.getElementById('userChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($userChartLabels),
            datasets: [{
                label: 'Users',
                data: @json($userChartData),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endpush

