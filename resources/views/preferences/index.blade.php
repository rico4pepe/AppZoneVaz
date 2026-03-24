@extends('layouts.front.layout_auth')

@section('title', 'FanZone — Set Your Preferences')

@section('content-width', '720px')

@section('hero')
    <h1 class="fz-auth-hero-title">
        Set Up Your <span>Fan Profile</span>
    </h1>
    <p class="fz-auth-hero-sub">
        Pick your leagues and teams. This drives your chat rooms,
        content feed and match priorities.
    </p>
@endsection

@section('header-right')
    <span>Already set up?</span>
    <a href="{{ route('dashboard') }}"
       style="color:var(--fz-green);margin-left:6px;font-weight:600;">
        Go to Dashboard
    </a>
@endsection

@section('content')

{{-- Step indicator --}}
<div class="fz-steps">
    <div class="fz-step">
        <div class="fz-step-dot done">
            <i class="fas fa-check" style="font-size:10px"></i>
        </div>
        <span class="fz-step-label">Subscribed</span>
    </div>
    <div class="fz-step-line done"></div>
    <div class="fz-step">
        <div class="fz-step-dot done">
            <i class="fas fa-check" style="font-size:10px"></i>
        </div>
        <span class="fz-step-label">Logged In</span>
    </div>
    <div class="fz-step-line done"></div>
    <div class="fz-step">
        <div class="fz-step-dot active">3</div>
        <span class="fz-step-label active">Preferences</span>
    </div>
    <div class="fz-step-line"></div>
    <div class="fz-step">
        <div class="fz-step-dot">4</div>
        <span class="fz-step-label">Dashboard</span>
    </div>
</div>

{{-- Main form card --}}
<form method="POST" action="{{ route('preferences.store') }}" id="fz-pref-form">
    @csrf

    {{-- ================================
         STEP 1 — LEAGUES
    ================================ --}}
    <div class="fz-auth-card fz-pref-section" id="fz-step-leagues">

        <div class="fz-auth-card-header">
            <div class="fz-auth-card-header-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="fz-auth-card-title">Step 1 — Select Your Leagues</div>
                <div style="font-size:11px;color:var(--fz-muted);margin-top:1px;">
                    Pick one or more leagues to follow
                </div>
            </div>
        </div>

        <div class="fz-auth-card-body">
            <div class="fz-league-grid">
                @foreach($leagues as $league)
                    <label class="fz-league-card" for="league_{{ $league->id }}">
                        <input
                            type="checkbox"
                            class="fz-league-cb"
                            name="leagues[]"
                            value="{{ $league->id }}"
                            id="league_{{ $league->id }}">
                        <div class="fz-league-card-inner">
                            <div class="fz-league-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <span class="fz-league-name">{{ $league->name }}</span>
                            <div class="fz-league-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ================================
         STEP 2 — TEAMS
         Hidden until a league is selected
    ================================ --}}
    <div class="fz-auth-card fz-pref-section" id="fz-step-teams" style="display:none;">

        <div class="fz-auth-card-header">
            <div class="fz-auth-card-header-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <div class="fz-auth-card-title">Step 2 — Select Your Teams</div>
                <div style="font-size:11px;color:var(--fz-muted);margin-top:1px;">
                    Choose at least one team from your selected leagues
                </div>
            </div>
        </div>

        <div class="fz-auth-card-body">
            <div id="fz-teams-container"></div>
        </div>

    </div>

    {{-- ================================
         SUBMIT
    ================================ --}}
    <div class="fz-pref-actions">
        <a href="{{ route('dashboard') }}"
           class="fz-btn fz-btn-ghost">
            Skip for now
        </a>
        <button
            type="submit"
            class="fz-btn fz-btn-primary"
            id="fz-pref-submit"
            style="width:auto;padding:11px 32px;">
            <i class="fas fa-check"></i>
            Save & Go to Dashboard
        </button>
    </div>

</form>

@endsection

@push('auth-styles')
<style>
    /* ============================================
       PREFERENCE SECTIONS
    ============================================ */
    .fz-pref-section {
        margin-bottom: 16px;
    }

    /* ============================================
       LEAGUE GRID
    ============================================ */
    .fz-league-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    @media (max-width: 600px) {
        .fz-league-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 380px) {
        .fz-league-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ============================================
       LEAGUE CARD — checkbox as a styled card
    ============================================ */

    /* Hide the native checkbox */
    .fz-league-cb {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .fz-league-card {
        display: block;
        cursor: pointer;
        position: relative;
    }

    .fz-league-card-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 16px 12px;
        background: var(--fz-surface-2);
        border: 1.5px solid var(--fz-border);
        border-radius: var(--fz-radius-md);
        transition: border-color 0.15s, background 0.15s;
        text-align: center;
        position: relative;
         padding: 18px 14px; /* was 16px 12px */
    }

    .fz-league-teams-block {
    animation: fzFadeInUp 0.25s ease;
}

@keyframes fzFadeInUp {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    .fz-league-card-inner:hover {
        border-color: var(--fz-border-md);
        background: var(--fz-surface-3);
    }

    /* Selected state */
    .fz-league-cb:checked + .fz-league-card-inner {
        border-color: var(--fz-green);
        background: var(--fz-green-dim);
    }

    .fz-league-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--fz-surface-3);
        border: 1px solid var(--fz-border-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: var(--fz-amber);
        transition: background 0.15s, border-color 0.15s;
    }

    .fz-league-cb:checked + .fz-league-card-inner .fz-league-icon {
        background: var(--fz-green-dim);
        border-color: var(--fz-green-border);
        color: var(--fz-green);
    }

    .fz-league-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--fz-text);
        line-height: 1.3;
    }

    /* Checkmark — hidden until selected */
    .fz-league-check {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--fz-green);
        color: #121212;
        font-size: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0.5);
        transition: opacity 0.15s, transform 0.15s;
    }

    .fz-league-cb:checked + .fz-league-card-inner .fz-league-check {
        opacity: 1;
        transform: scale(1);
    }

    .fz-league-card-inner:active,
    .fz-team-card-inner:active {
        transform: scale(0.96);
    }

    /* ============================================
       TEAMS SECTION — rendered by JS
    ============================================ */
    .fz-league-teams-block {
        margin-bottom: 16px;
    }

    .fz-league-teams-block:last-child {
        margin-bottom: 0;
    }

    .fz-league-teams-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--fz-muted);
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--fz-border);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .fz-league-teams-label i {
        color: var(--fz-amber);
    }

    .fz-teams-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    @media (max-width: 600px) {
        .fz-teams-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Team checkbox card */
    .fz-team-cb {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }

    .fz-team-card {
        display: block;
        cursor: pointer;
        position: relative;
    }

    .fz-team-card-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background: var(--fz-surface-2);
        border: 1.5px solid var(--fz-border);
        border-radius: var(--fz-radius-sm);
        transition: border-color 0.15s, background 0.15s;
        position: relative;
    }

    .fz-team-card-inner:hover {
        border-color: var(--fz-border-md);
        background: var(--fz-surface-3);
    }

    .fz-team-cb:checked + .fz-team-card-inner {
        border-color: var(--fz-green);
        background: var(--fz-green-dim);
    }

    .fz-league-cb:checked + .fz-league-card-inner {
    border-color: var(--fz-green);
    background: var(--fz-green-dim);
    box-shadow: 0 0 0 1px var(--fz-green-border);
}

.fz-team-cb:checked + .fz-team-card-inner {
    border-color: var(--fz-green);
    background: var(--fz-green-dim);
    box-shadow: 0 0 0 1px var(--fz-green-border);
}

    .fz-team-init-badge {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: var(--fz-surface-3);
        border: 1px solid var(--fz-border-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        font-weight: 800;
        color: var(--fz-amber);
        flex-shrink: 0;
        letter-spacing: 0.5px;
        transition: background 0.15s;
    }

    .fz-team-cb:checked + .fz-team-card-inner .fz-team-init-badge {
        background: var(--fz-green-dim);
        color: var(--fz-green);
        border-color: var(--fz-green-border);
    }

    .fz-team-name-label {
        font-size: 12px;
        color: var(--fz-text);
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fz-team-check-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: var(--fz-green);
        color: #121212;
        font-size: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0.4);
        transition: opacity 0.15s, transform 0.15s;
        flex-shrink: 0;
    }

    .fz-team-cb:checked + .fz-team-card-inner .fz-team-check-dot {
        opacity: 1;
        transform: scale(1);
    }

    #fz-pref-submit {
    box-shadow: 0 6px 16px rgba(0, 200, 83, 0.25);
}

    /* ============================================
       ACTIONS ROW
    ============================================ */
    .fz-pref-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        gap: 12px;
    }

    @media (max-width: 480px) {
        .fz-pref-actions {
            flex-direction: column-reverse;
        }

        .fz-pref-actions .fz-btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {

    const teams   = @json($teams);
    const leagues = @json($leagues);

    const teamsStep     = document.getElementById('fz-step-teams');
    const teamsContainer = document.getElementById('fz-teams-container');

    /**
     * Listen to all league checkboxes
     */
    document.querySelectorAll('.fz-league-cb').forEach(function (cb) {
        cb.addEventListener('change', renderTeams);
    });

    /**
     * Render team cards for all selected leagues
     */
    function renderTeams() {
        const selectedLeagueIds = Array.from(
            document.querySelectorAll('.fz-league-cb:checked')
        ).map(function (cb) { return cb.value; });

        teamsContainer.innerHTML = '';

        if (selectedLeagueIds.length === 0) {
            teamsStep.style.display = 'none';
            return;
        }

        teamsStep.style.display = 'block';

        selectedLeagueIds.forEach(function (leagueId) {
            const leagueTeams = teams.filter(function (team) {
                return team.league_id.toString() === leagueId;
            });

           // if (leagueTeams.length === 0) return;
            if (leagueTeams.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'fz-empty-state';
            empty.innerHTML = 'No teams available';
            teamsContainer.appendChild(empty);
            return;
        }

            const league = leagues.find(function (l) {
                return l.id.toString() === leagueId;
            });

            if (!league) return;

            // Build team cards
            const teamCards = leagueTeams.map(function (team) {
                const initials = team.name
                    .split(' ')
                    .map(function (w) { return w[0]; })
                    .join('')
                    .substring(0, 3)
                    .toUpperCase();

                return `
                    <label class="fz-team-card" for="team_${team.id}">
                        <input
                            type="checkbox"
                            class="fz-team-cb"
                            name="teams[]"
                            value="${team.id}"
                            id="team_${team.id}">
                        <div class="fz-team-card-inner">
                            <div class="fz-team-init-badge">${initials}</div>
                            <span class="fz-team-name-label">${team.name}</span>
                            <div class="fz-team-check-dot">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </label>
                `;
            }).join('');

            // Build league block
            const block = document.createElement('div');
            block.className = 'fz-league-teams-block';
            block.innerHTML = `
                <div class="fz-league-teams-label">
                    <i class="fas fa-trophy"></i>
                    ${league.name}
                </div>
                <div class="fz-teams-grid">
                    ${teamCards}
                </div>
            `;

            teamsContainer.appendChild(block);
        });

        // Smooth scroll to teams section
        teamsStep.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
    }

})();
</script>
@endpush