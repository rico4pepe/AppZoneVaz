<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function upcoming(Request $request)
    {
        $matches = Fixture::with(['league', 'homeTeam', 'awayTeam'])
            ->where('status', 'NS')
            ->where('kickoff_at', '>=', now())
            ->orderBy('kickoff_at')
            ->paginate(20);

        return response()->json($matches);
    }

    public function finished(Request $request)
    {
        $matches = Fixture::with(['league', 'homeTeam', 'awayTeam'])
            ->where('status', 'FT')
            ->orderByDesc('kickoff_at')
            ->paginate(20);

        return response()->json($matches);
    }

    public function league($leagueId)
    {
        $matches = Fixture::with(['homeTeam', 'awayTeam'])
            ->where('league_id', $leagueId)
            ->orderByDesc('kickoff_at')
            ->paginate(20);

        return response()->json($matches);
    }
    public function team($teamId)
    {
        $matches = Fixture::with(['league', 'homeTeam', 'awayTeam'])
            ->where(function ($q) use ($teamId) {
                $q->where('home_team_id', $teamId)
                ->orWhere('away_team_id', $teamId);
            })
            ->orderByDesc('kickoff_at')
            ->paginate(20);

        return response()->json($matches);
    }

   public function show($id)
    {
        $fixture = \App\Models\Fixture::with(['league', 'homeTeam', 'awayTeam'])
            ->findOrFail($id);

        $season = 2022; // locked season for now

        // Get standings positions
        $homeStanding = \App\Models\Standing::where('league_id', $fixture->league_id)
            ->where('team_id', $fixture->home_team_id)
            ->where('season', $season)
            ->first();

        $awayStanding = \App\Models\Standing::where('league_id', $fixture->league_id)
            ->where('team_id', $fixture->away_team_id)
            ->where('season', $season)
            ->first();

        // Last 5 matches for each team
        $homeRecent = \App\Models\Fixture::where(function ($q) use ($fixture) {
                $q->where('home_team_id', $fixture->home_team_id)
                ->orWhere('away_team_id', $fixture->home_team_id);
            })
            ->where('status', 'FT')
            ->orderByDesc('kickoff_at')
            ->limit(5)
            ->get();

        $awayRecent = \App\Models\Fixture::where(function ($q) use ($fixture) {
                $q->where('home_team_id', $fixture->away_team_id)
                ->orWhere('away_team_id', $fixture->away_team_id);
            })
            ->where('status', 'FT')
            ->orderByDesc('kickoff_at')
            ->limit(5)
            ->get();

        return response()->json([
            'fixture' => $fixture,
            'home_standing' => $homeStanding,
            'away_standing' => $awayStanding,
            'home_recent_matches' => $homeRecent,
            'away_recent_matches' => $awayRecent,
        ]);
    }




}
