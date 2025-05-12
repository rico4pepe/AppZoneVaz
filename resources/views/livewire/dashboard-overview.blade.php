
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

                {{-- Bar Chart --}}
        <div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">User Registrations (Last 30 Days)</h5>
        <canvas id="userChart" width="400" height="200"></canvas>
    </div>
</div>

            {{-- Pie Chart --}}
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Content Type Breakdown SS</h5>
                    <canvas id="contentChart" width="400" height="200"></canvas>
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
                        <p>Polls: {{ $contentStats['polls'] ?? 0 }}</p>
                        <p>Quizzes: {{ $contentStats['quizzes'] ?? 0 }}</p>
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
@push('scripts')
<script>
    function renderUserChart(labels, data) {
        const ctx = document.getElementById('userChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Users',
                    data: data,
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
    }

    function renderContentChart(labels, data) {
    const ctx = document.getElementById('contentChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#007bff', '#28a745', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}


    document.addEventListener('livewire:load', function () {
        renderUserChart(@json($userChartLabels), @json($userChartData));
        renderContentChart(@json($contentChartLabels), @json($contentChartData));
    });

    Livewire.hook('message.processed', (message, component) => {
        renderUserChart(@json($userChartLabels), @json($userChartData));
        renderContentChart(@json($contentChartLabels), @json($contentChartData));
    });
</script>
@endpush

