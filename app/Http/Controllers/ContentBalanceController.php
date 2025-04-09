<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Models\Team;

class ContentBalanceController extends Controller
{
    //

    public function leagueBalance()
    {
        $leagues = League::withCount([
            'contents as total_contents',
            'contents as polls_count' => fn ($q) => $q->where('type', 'poll'),
            'contents as quizzes_count' => fn ($q) => $q->where('type', 'quiz'),
            'contents as trivia_count' => fn ($q) => $q->where('type', 'trivia'),
        ])->get();

        return response()->json($leagues);
    }

    public function teamBalance()
    {
        $teams = Team::withCount([
            'contents as total_contents',
            'contents as polls_count' => fn ($q) => $q->where('type', 'poll'),
            'contents as quizzes_count' => fn ($q) => $q->where('type', 'quiz'),
            'contents as trivia_count' => fn ($q) => $q->where('type', 'trivia'),
        ])->get();

        return response()->json($teams);
    }
}
