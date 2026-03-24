@extends('layouts.front.layout_dashboard')

@section('title', 'FanZone — Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Success alert --}}
@if(session('success'))
    <div class="fz-alert fz-alert-success" role="alert">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
        <button class="fz-alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif



{{-- ============================================
     MAIN GRID — Matches + Leaderboard
============================================ --}}
<div class="fz-dash-stack">

{{-- ============================================
     LATEST NEWS — placeholder until news module
============================================ --}}
<div class="fz-card fz-dash-news" onclick="window.location='{{ route('feed') }}'">
    <div class="fz-card-header">
        <span class="fz-section-title">
            <i class="fas fa-newspaper" style="margin-right:6px;color:var(--fz-muted)"></i>
            Latest Update
        </span>
        <a href="{{ Route::has('news.index') ? route('news.index') : '#' }}"
           class="fz-link">See all</a>
    </div>
    <div class="fz-card-body">
        <div class="fz-empty-state">
            <i class="fas fa-bolt"></i>
    <span>No updates yet — check Feed</span>
        </div>
    </div>
</div>

    {{-- ======= MATCHES COLUMN ======= --}}
 

        {{-- Live Matches --}}
        <div class="fz-card">
            <div class="fz-card-header">
                <span class="fz-section-title">
                    <i class="fas fa-circle-dot fz-green me-1" style="font-size:10px"></i>
                    Live Matches
                </span>
                <a href="{{ Route::has('matches.index') ? route('matches.index') : '#' }}"
                   class="fz-link">View all</a>
            </div>
            <div class="fz-card-body fz-match-list">
                @if(isset($liveMatches) && $liveMatches->count())
                    @foreach($liveMatches as $match)
                        <div class="fz-match-row">
                            <div class="fz-match-team">
                                <div class="fz-team-init">
                                    {{ strtoupper(substr($match->homeTeam->name, 0, 3)) }}
                                </div>
                                <span class="fz-team-name">{{ $match->homeTeam->name }}</span>
                            </div>
                            <div class="fz-match-score-wrap">
                                <span class="fz-match-score">
                                    {{ $match->home_score ?? '0' }}
                                    <span class="fz-score-sep">—</span>
                                    {{ $match->away_score ?? '0' }}
                                </span>
                                <span class="fz-match-min fz-green">
                                    {{ $match->minute ?? '' }}'
                                </span>
                            </div>
                            <div class="fz-match-team fz-match-team--right">
                                <span class="fz-team-name">{{ $match->awayTeam->name }}</span>
                                <div class="fz-team-init">
                                    {{ strtoupper(substr($match->awayTeam->name, 0, 3)) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="fz-empty-state">
                        <i class="fas fa-futbol"></i>
                        <span>No live matches right now</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Results --}}
        <div class="fz-card">
            <div class="fz-card-header">
                <span class="fz-section-title">Recent Results</span>
                <a href="{{ Route::has('matches.index') ? route('matches.index') : '#' }}"
                   class="fz-link">View all</a>
            </div>
            <div class="fz-card-body fz-match-list">
                @if(isset($recentMatches) && $recentMatches->count())
                    @foreach($recentMatches as $match)
                        <div class="fz-match-row fz-match-row--finished">
                            <div class="fz-match-team">
                                <div class="fz-team-init">
                                    {{ strtoupper(substr($match->homeTeam->name, 0, 3)) }}
                                </div>
                                <span class="fz-team-name">{{ $match->homeTeam->name }}</span>
                            </div>
                            <div class="fz-match-score-wrap">
                                <span class="fz-match-score fz-match-score--finished">
                                    {{ $match->home_score }}
                                    <span class="fz-score-sep">—</span>
                                    {{ $match->away_score }}
                                </span>
                                <span class="fz-match-status">FT</span>
                            </div>
                            <div class="fz-match-team fz-match-team--right">
                                <span class="fz-team-name">{{ $match->awayTeam->name }}</span>
                                <div class="fz-team-init">
                                    {{ strtoupper(substr($match->awayTeam->name, 0, 3)) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="fz-empty-state">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>No recent results</span>
                    </div>
                @endif
            </div>
        </div>

    

    {{-- ======= LEADERBOARD COLUMN ======= --}}


</div>



{{-- ============================================
     CHAT MOUNT — Desktop floating widget
============================================ --}}
<div id="chat-app"
     data-user="{{ auth()->id() }}"
     data-username="{{ auth()->user()->display_name ?? auth()->user()->name }}"
     data-default-room="{{ $defaultMatchId ?? '' }}">
</div>

{{-- Quiz mount --}}
<div id="quiz-zone"></div>

@endsection

{{-- ============================================
     ONBOARDING TRIGGER
     Only fires if profile is incomplete —
     but preference page handles this via redirect.
     This is a fallback safety net only.
============================================ --}}
@if(auth()->check() && is_null(auth()->user()->display_name) && is_null(auth()->user()->team_id))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Redirect to preference page if somehow they slipped through
        window.location.href = "{{ Route::has('preference') ? route('preference') : '#' }}";
    });
</script>
@endpush
@endif

@push('scripts')
<script>
    /**
     * Wire live match count to the bottom nav Live button.
     * Called after match data is available on the page.
     * React components can also call this directly.
     */
    document.addEventListener('DOMContentLoaded', function () {
        const liveCount = {{ $liveMatches->count() ?? 0 }};
        if (window.FanZoneApp && window.FanZoneApp.setLiveCount) {
            FanZoneApp.setLiveCount(liveCount);
        }
    });
</script>
@endpush
@push('styles')
<style>
    /* ============================================
       ALERT
    ============================================ */
    .fz-alert {
        display: flex;
        align-items: center;
        gap: 10px;
         padding: 10px 14px;
        border-radius: var(--fz-radius-md);
        margin-bottom: 16px;
        font-size: 13px;
    }

    .fz-dash-stack {
     display: flex;
    flex-direction: column;
    gap: 18px; /* increase slightly for better breathing */
}

.fz-card {
    width: 100%;
    display: block;
     border-radius: 14px;              /* smoother */
    padding: 0;                       /* keep header/body control */
    box-shadow: 0 2px 10px rgba(0,0,0,0.25); /* subtle depth */
}

.fz-dash-stack > .fz-card {
    margin: 0;
}

    .fz-alert-success {
        background: var(--fz-green-dim);
        border: 1px solid var(--fz-green-border);
        color: var(--fz-green);
    }

    .fz-alert-close {
        margin-left: auto;
        background: transparent;
        border: none;
        color: inherit;
        opacity: 0.6;
        font-size: 14px;
        padding: 0 4px;
    }

    .fz-alert-close:hover { opacity: 1; }

    /* ============================================
       STATS ROW
    ============================================ */
    .fz-dash-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }

    .fz-dash-news {
         margin-bottom: 4px;
    opacity: 0.95;
}

.fz-dash-news .fz-card-body {
    padding: 14px 16px;  /* was smaller */
}

    @media (max-width: 480px) {
        .fz-dash-stats {
            grid-template-columns: 1fr 1fr;
        }

        .fz-dash-stats .fz-stat-card:last-child {
            grid-column: 1 / -1;
        }
    }

    .fz-stat-card {
        background: var(--fz-surface);
        border: 1px solid var(--fz-border);
        border-radius: var(--fz-radius-md);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .fz-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: var(--fz-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .fz-stat-icon--green  { background: var(--fz-green-dim); color: var(--fz-green); }
    .fz-stat-icon--amber  { background: var(--fz-amber-dim); color: var(--fz-amber); }
    .fz-stat-icon--neutral { background: var(--fz-surface-2); color: var(--fz-muted); }

    .fz-stat-body {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .fz-stat-label {
    font-size: 11px; /* was 10px */
    letter-spacing: 0.6px; /* reduce from 0.8 */
}

   .fz-stat-value {
    font-size: 18px; /* was 22px */
    font-weight: 700; /* slightly softer */
}

    .fz-stat-card .fz-live-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .fz-stat-card {
    transition: transform 0.15s ease, background 0.15s ease;
}

.fz-stat-card:active {
    transform: scale(0.97);
}

.fz-stat-card:hover {
    background: var(--fz-surface-2);
}

    /* ============================================
       MAIN GRID
    ============================================ */
    .fz-dash-grid {
         display: block !important;
    }

    @media (max-width: 767px) {
        .fz-dash-grid {
            grid-template-columns: 1fr;
        }
    }

    .fz-dash-col {
        display: flex;
        flex-direction: column;
        gap: var(--fz-space-lg);
        min-width: 0;
    }

    .fz-card--full {
        flex: 1;
         border-color: var(--fz-border-md);
    }

    /* ============================================
       MATCH ROWS
    ============================================ */
    .fz-match-list {
        display: flex;
        flex-direction: column;
        gap: 0;
        padding: 0;
    }

    .fz-match-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid var(--fz-border);
        gap: 8px;
        transition: background 0.15s ease, transform 0.08s ease;
    }

    .fz-match-row:last-child { border-bottom: none; }
    .fz-match-row:hover { background: var(--fz-surface-2); }

    .fz-match-row--finished { opacity: 0.6; }
    .fz-match-row--finished:hover { opacity: 1; }

    .fz-match-team {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .fz-match-team--right {
        justify-content: flex-end;
        flex-direction: row-reverse;
    }

    .fz-team-init {
        width: 32px;
        height: 32px;
        border-radius: var(--fz-radius-sm);
        background: var(--fz-surface-2);
        border: 1px solid var(--fz-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 800;
        color: var(--fz-amber);
        flex-shrink: 0;
        letter-spacing: 0.5px;
    }

    .fz-team-name {
        font-size: 13px; 
        color: var(--fz-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .fz-match-row:active {
     transform: scale(0.98);
    background: var(--fz-surface-3);
    }

    .fz-match-score-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        flex-shrink: 0;
        min-width: 60px;
    }

    .fz-match-score {
      
        font-weight: 800;
        color: var(--fz-text);
        letter-spacing: 1px; /* was 2px */
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .fz-match-score--finished {
        font-size: 15px;
        color: var(--fz-muted);
    }

    .fz-score-sep {
        font-weight: 300;
        color: var(--fz-muted);
        font-size: 13px;
    }

    .fz-match-min {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .fz-match-status {
        font-size: 9px;
        font-weight: 700;
        color: var(--fz-muted);
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* ============================================
       EMPTY STATE
    ============================================ */
    .fz-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 28px 16px;
        color: var(--fz-muted);
        font-size: 12px;
        opacity: 0.7;
    }

    .fz-empty-state i {
         font-size: 20px;
        opacity: 0.6;
    }

    /* ============================================
       NEWS CARD
    ============================================ */
    .fz-dash-news {
       margin-bottom: 4px;
         opacity: 0.9;
    }

 
</style>
@endpush