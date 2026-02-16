<div class="main-content">
    <div class="container mt-4">
        <h4 class="mb-3">Admin Dashboard Overview</h4>

        {{-- Date Range Filter --}}
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
        </div>

        <br /><br />


        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary  h-100">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <p class="fs-4">{{ $userCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-dark  h-100">
                    <div class="card-body">
                        <h5>Total Content</h5>
                        <p>Polls: {{ $contentStats['polls'] ?? 0 }}</p>
                        <p>Quizzes: {{ $contentStats['quizzes'] ?? 0 }}</p>
                        <p>Trivias: {{ $contentStats['trivias'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white  h-100">
                    <div class="card-body">
                        <h5>Quiz Stats</h5>
                        <p>Avg Points: {{ $quizStats['avg_points'] }}</p>
                        <p>Top: {{ $quizStats['most_attempted']?->title ?? '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white  h-100">
                    <div class="card-body">
                        <h5>Polls</h5>
                        <p>Total Votes: {{ $pollStats['total_votes'] }}</p>
                        <p>Top: {{ $pollStats['top_poll']?->title ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4 ">
                <div class="card bg-warning  h-100">
                    <div class="card-body">
                        <h5>Chat Stats</h5>
                        <p>Total Messages: {{ $chatStats['messages'] }}</p>
                        <p>Top User: {{ $chatStats['top_user']?->user->display_name ?? '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white  h-100">
                    <div class="card-body">
                        <h5>Reports</h5>
                        <p>Pending: {{ $reportStats['pending'] }}</p>
                        <p>Banned Users: {{ $reportStats['banned_users'] }}</p>
                    </div>
                </div>
            </div>
        </div>


        {{-- Charts --}}
        <div class="row g-3 mb-4">
    <div class="col-md-3">
            <div class="card h-100 cursor-pointer" data-bs-toggle="modal" data-bs-target="#drilldownModal" onclick="loadDrilldown('user-registrations')">
            <div class="card-body">
                <h5 class="card-title">User Registrations (Last 30 Days)</h5>
                <canvas id="userChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
         <div class="card h-100 cursor-pointer" data-bs-toggle="modal" data-bs-target="#drilldownModal" onclick="loadDrilldown('content-type-breakdown')">
            <div class="card-body">
                <h5 class="card-title">Content Type Breakdown</h5>
                <canvas id="contentChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Average Quiz Scores by Day</h5>
                <canvas id="quizAvgChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Poll Votes Per Day</h5>
                <canvas id="pollTrendChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>


        
    </div>
</div>

<!-- Drilldown Modal -->
<div class="modal fade" id="drilldownModal" tabindex="-1" aria-labelledby="drilldownModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="drilldownModalLabel">Loading...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="drilldownModalContent">
        <p class="text-muted">Fetching details...</p>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-outline-primary" id="fullReportLink" target="_blank">View Full Report</a>
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
                labels,
                datasets: [{
                    label: 'Users',
                    data,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    function renderContentChart(labels, data) {
        const ctx = document.getElementById('contentChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: ['#007bff', '#28a745', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    function renderQuizAvgChart(labels, data) {
        const ctx = document.getElementById('quizAvgChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Avg Points',
                    data,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    function renderPollTrendChart(labels, data) {
        const ctx = document.getElementById('pollTrendChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Poll Votes',
                    data,
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    }

    document.addEventListener('livewire:load', () => {
        renderUserChart(@json($userChartLabels), @json($userChartData));
        renderContentChart(@json($contentChartLabels), @json($contentChartData));
        renderQuizAvgChart(@json($quizAvgChartLabels), @json($quizAvgChartData));
        renderPollTrendChart(@json($pollChartLabels), @json($pollChartData));
    });

    Livewire.hook('message.processed', () => {
        renderUserChart(@json($userChartLabels), @json($userChartData));
        renderContentChart(@json($contentChartLabels), @json($contentChartData));
        renderQuizAvgChart(@json($quizAvgChartLabels), @json($quizAvgChartData));
        renderPollTrendChart(@json($pollChartLabels), @json($pollChartData));
    });
</script>

<script>
    function loadDrilldown(type) {
        const modalTitle = {
            'user-registrations': 'User Registrations Details',
            'content-type-breakdown': 'Content Type Breakdown',
            'quiz-stats': 'Quiz Statistics',
            'poll-trends': 'Poll Trends',
            // add more types as needed
        };

        const reportLinks = {
            'user-registrations': '/admin/reports/users',
            'content-type-breakdown': '/admin/reports/content-types',
            // add more as needed
        };

        document.getElementById('drilldownModalLabel').textContent = modalTitle[type] || 'Details';
        document.getElementById('drilldownModalContent').innerHTML = '<p class="text-muted">Loading data...</p>';
        document.getElementById('fullReportLink').href = reportLinks[type] || '#';

        // Use Livewire or AJAX to load detailed content dynamically
        fetch(`/admin/dashboard/drilldown/${type}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('drilldownModalContent').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('drilldownModalContent').innerHTML = '<p class="text-danger">Failed to load details.</p>';
            });
    }
</script>

@endpush
