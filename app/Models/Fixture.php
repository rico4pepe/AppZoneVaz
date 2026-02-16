<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'provider_match_id',
        'league_id',
        'home_team_id',
        'away_team_id',
        'kickoff_at',
        'status',
        'home_score',
        'away_score',
    ];

    protected $casts = [
        'kickoff_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}
