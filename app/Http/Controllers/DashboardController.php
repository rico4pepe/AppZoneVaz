<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $leagueIds = $user->leagues()->pluck('leagues.id');
        $teamIds   = $user->teams()->pluck('teams.id');

        if ($leagueIds->isEmpty() && $teamIds->isEmpty()) {
            return redirect()->route('preferences.index');
        }

        $baseQuery = Fixture::with(['league', 'homeTeam', 'awayTeam']);

        // Filter by teams if exist
        if ($teamIds->isNotEmpty()) {
            $baseQuery->where(function ($q) use ($teamIds) {
                $q->whereIn('home_team_id', $teamIds)
                  ->orWhereIn('away_team_id', $teamIds);
            });
        }
        elseif ($leagueIds->isNotEmpty()) {
            $baseQuery->whereIn('league_id', $leagueIds);
        }

        $liveMatches = (clone $baseQuery)
            ->where('status', 'NS') // adjust when live data exists
            ->orderBy('kickoff_at')
            ->limit(5)
            ->get();

        $recentMatches = (clone $baseQuery)
            ->where('status', 'FT')
            ->orderByDesc('kickoff_at')
            ->limit(5)
            ->get();

        // Find default chat room match — live first, then upcoming
        $defaultMatch = (clone $baseQuery)
            ->whereIn('status', ['NS', 'LIVE', '1H', '2H', 'HT'])
            ->orderByRaw("FIELD(status, 'LIVE', '1H', '2H', 'HT', 'NS')") // live first
            ->orderBy('kickoff_at')
            ->first();

        $defaultMatchId = $defaultMatch?->id;

        return view('dashboard', compact(
            'liveMatches',
            'recentMatches',
            'defaultMatchId'
        ));
    }
}
