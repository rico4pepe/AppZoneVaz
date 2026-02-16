@extends('layouts.front.layout_dashboard')

@section('content')
<div class="container py-5">

    <div class="text-center mb-5">
        <h2 class="fw-bold">Choose Your Football Preferences</h2>
        <p class="text-muted">
            Select leagues and teams you want to follow.
            This helps us personalize your matches and chatrooms.
        </p>
    </div>

    <form method="POST" action="{{ route('preferences.store') }}">
        @csrf

        {{-- LEAGUES --}}
        <div class="mb-5">
            <h4 class="mb-3">Step 1: Select Leagues</h4>

            <div class="row g-3">
                @foreach($leagues as $league)
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="form-check">
                                    <input
                                        class="form-check-input league-checkbox"
                                        type="checkbox"
                                        name="leagues[]"
                                        value="{{ $league->id }}"
                                        id="league_{{ $league->id }}"
                                    >
                                    <label class="form-check-label fw-bold"
                                           for="league_{{ $league->id }}">
                                        {{ $league->name }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TEAMS --}}
        <div id="teams-wrapper" class="mb-5" style="display:none;">
            <h4 class="mb-3">Step 2: Select Teams</h4>
            <p class="text-muted">
                Choose at least one team from your selected leagues.
            </p>

            <div id="teams-container"></div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary px-5">
                Save Preferences
            </button>
        </div>

    </form>
</div>

{{-- JS --}}
<script>
const teams = @json($teams);
const leagues = @json($leagues);

document.querySelectorAll('.league-checkbox').forEach(cb => {
    cb.addEventListener('change', renderTeams);
});

function renderTeams() {
    const selectedLeagues = Array.from(
        document.querySelectorAll('.league-checkbox:checked')
    ).map(cb => cb.value);

    const wrapper = document.getElementById('teams-wrapper');
    const container = document.getElementById('teams-container');

    container.innerHTML = '';

    if (selectedLeagues.length === 0) {
        wrapper.style.display = 'none';
        return;
    }

    wrapper.style.display = 'block';

    selectedLeagues.forEach(leagueId => {

        const leagueTeams = teams.filter(
            team => team.league_id.toString() === leagueId
        );

        if (leagueTeams.length === 0) return;

        const league = leagues.find(
            l => l.id.toString() === leagueId
        );

        let section = `
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">
                    ${league.name}
                </div>
                <div class="card-body">
                    <div class="row g-2">
        `;

        leagueTeams.forEach(team => {
            section += `
                <div class="col-md-4">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="teams[]"
                            value="${team.id}"
                            id="team_${team.id}"
                        >
                        <label class="form-check-label"
                               for="team_${team.id}">
                            ${team.name}
                        </label>
                    </div>
                </div>
            `;
        });

        section += `
                    </div>
                </div>
            </div>
        `;

        container.innerHTML += section;
    });
}
</script>
@endsection
