<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'difficulty',
        'league_id', 'team_id', 'published_at', 'is_featured', 'is_active'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function options()
    {
        return $this->hasMany(ContentOption::class);
    }

    public function league()
    {
        return $this->belongsTo(League::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
