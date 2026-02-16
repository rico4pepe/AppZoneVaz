@extends('layouts.front.layout_dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

            {{-- Navbar/Header --}}
            @include('partials.navbar')

            {{-- Success Alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Store token for React --}}
            @if(session('auth_token'))
                <script>
                    localStorage.setItem('auth_token', @json(session('auth_token')));
                </script>
            @endif

            {{-- ========================= --}}
            {{-- Dashboard Overview Cards --}}
            {{-- ========================= --}}
            <div class="row mt-4">

                {{-- Live Scores --}}
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">Live Matches</h5>

                            @if(isset($liveMatches) && $liveMatches->count())
                                @foreach($liveMatches as $match)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ $match->homeTeam->name }}</span>
                                        <strong>
                                            {{ $match->home_score ?? '-' }}
                                            -
                                            {{ $match->away_score ?? '-' }}
                                        </strong>
                                        <span>{{ $match->awayTeam->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No live matches right now.</p>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Recent Results --}}
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">Recent Results</h5>

                            @if(isset($recentMatches) && $recentMatches->count())
                                @foreach($recentMatches as $match)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ $match->homeTeam->name }}</span>
                                        <strong>
                                            {{ $match->home_score }}
                                            -
                                            {{ $match->away_score }}
                                        </strong>
                                        <span>{{ $match->awayTeam->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No recent matches found.</p>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Latest News (Placeholder) --}}
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">Latest News</h5>
                            <p class="text-muted">
                                News updates and football headlines will appear here.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========================= --}}
            {{-- Leaderboard Section --}}
            {{-- ========================= --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">🏆 Leaderboard</h5>

                            <div id="leaderboard-widget"
                                 data-user="{{ auth()->id() }}"
                                 data-token="{{ session('auth_token') }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================= --}}
            {{-- React Mount Areas --}}
            {{-- ========================= --}}

            {{-- Global Chat on Dashboard --}}
            <div id="chat-app"
                 data-user="{{ auth()->id() }}"
                 data-username="{{ auth()->user()->name }}">
            </div>

            <div id="quiz-zone"></div>

            {{-- Footer --}}
            @include('partials.footer')

        </main>
    </div>
</div>
@endsection
