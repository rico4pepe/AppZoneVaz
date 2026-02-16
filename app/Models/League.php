<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sportmonks_id'

    ];


 

            public function teams()
        {
            return $this->hasMany(Team::class);
        }

        public function users()
        {
            return $this->belongsToMany(User::class, 'user_leagues');
        }

    public function contents()
{   
    return $this->hasMany(Content::class);
}

}
