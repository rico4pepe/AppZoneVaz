<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'team_id',
        'league_id',
        'season',
        'country',
        'code'
    ];

    public function league()
    {
        return $this->belongsTo(League::class);
    }
}
