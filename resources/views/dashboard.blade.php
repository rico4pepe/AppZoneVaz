@extends('layouts.front.layout_dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
  
        {{-- Sidebar --}}
        @include('partials.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            {{-- Navbar/Header --}}
            @include('partials.navbar')

            @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

                        @if(session('auth_token'))
                    <script>
                        @if(session('auth_token'))
                            localStorage.setItem('auth_token', @json(session('auth_token')));
                        @endif
                    </script>
                @endif

            {{-- Onboarding Modal --}}

            {{-- Dashboard Content (Cards/Sections) --}}
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">Live Scores</h5>
                            <p class="card-text">Embed live score component here.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">Upcoming Matches</h5>
                            <p class="card-text">Upcoming match content here.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title">Latest News</h5>
                            <p class="card-text">News updates or feeds here.</p>
                        </div>
                    </div>
                </div>
            </div>


            {{-- 🏆 Leaderboard Widget Section --}}
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

            {{-- React-mountable component areas --}}
        <div id="chat-app" 
            data-user="{{ auth()->id() }}" 
            data-username="{{ str_replace(' ', '', auth()->user()?->name ?? 'Guest') }}">
        </div>
            <div id="quiz-zone"></div>
            <div id="leaderboard-widget"></div>


            

            {{-- Footer --}}
            @include('partials.footer')
        </main>
    </div>
</div>

@if (auth()->check() && empty(auth()->user()->team_id))
    <script>
   
        document.addEventListener('DOMContentLoaded', function () {
            let modal = new bootstrap.Modal(document.getElementById('onboardingModal'));
            modal.show();
        }); 
    </script>
@endif
@endsection
