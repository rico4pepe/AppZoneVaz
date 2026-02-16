<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\League;
use App\Models\Standing;
use App\Models\Fixture;

class LeagueController extends Controller
{
    public function dashboard($leagueId)
    {
        $league = League::findOrFail($leagueId);

        $standings = Standing::with('team')
            ->where('league_id', $leagueId)
            ->orderBy('position')
            ->get();

        $recentMatches = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $leagueId)
            ->where('status', 'FT')
            ->orderByDesc('kickoff_at')
            ->limit(5)
            ->get();

        $upcomingMatches = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $leagueId)
            ->where('status', 'NS')
            ->orderBy('kickoff_at')
            ->limit(5)
            ->get();

        return response()->json([
            'league' => $league,
            'standings' => $standings,
            'recent_matches' => $recentMatches,
            'upcoming_matches' => $upcomingMatches,
        ]);
    }
}
