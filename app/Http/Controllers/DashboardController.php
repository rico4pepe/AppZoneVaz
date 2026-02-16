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

        return view('dashboard', compact(
            'liveMatches',
            'recentMatches'
        ));
    }
}
