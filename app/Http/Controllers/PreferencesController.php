<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Models\Team;

class PreferencesController extends Controller
{
    //
    public function index()
    {
        $targetLeagueIds = [39, 140, 135, 78, 61]; // EPL, La Liga, Serie A, Bundesliga, Ligue 1

        $leagues = League::whereIn('sportmonks_id', $targetLeagueIds)
            ->orderBy('name')
            ->get();

        $teams = Team::whereIn('league_id', $leagues->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('preferences.index', [
            'leagues' => $leagues,
            'teams'   => $teams,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'leagues' => 'nullable|array',
            'leagues.*' => 'exists:leagues,id',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
        ]);

        if (
            empty($request->leagues) &&
            empty($request->teams)
        ) {
            return back()->withErrors([
                'preferences' => 'Please select at least one league or team.'
            ]);
        }

        $user = auth()->user();

        // Sync leagues
        if ($request->filled('leagues')) {
            $user->leagues()->sync($request->leagues);
        } else {
            $user->leagues()->detach();
        }

        // Sync teams
        if ($request->filled('teams')) {
            $user->teams()->sync($request->teams);
        } else {
            $user->teams()->detach();
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Preferences saved successfully.');
    }

}
