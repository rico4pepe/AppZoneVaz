<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\League;
use App\Models\Country;

class SportMonksController extends Controller
{
    //

    public function getTeamsByCountry($country_id)
    {
        $apiKey = env('SPORTMONKS_API_KEY');
        $response = Http::get("https://api.sportmonks.com/v3/football/teams/countries/{$country_id}?api_token={$apiKey}");
    
        if ($response->successful()) {
            return response()->json($response->json()['data']);
        }
    
        return response()->json(['message' => 'Failed to fetch teams'], 500);
    }

    public function getLeagues()
    {
        $leagues = League::all(); // Fetch leagues from database
        return response()->json($leagues);
    }


    public function getCountries()
    {
        return response()->json(Country::all());
    }
}
