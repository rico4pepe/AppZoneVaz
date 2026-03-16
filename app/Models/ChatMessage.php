<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => 'open',
    ];

    protected $fillable = [
        'user_id',
        'match_id',
        'event_id',
        'message',
         'type',
        'is_hidden',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fixture()
    {
        return $this->belongsTo(Fixture::class, 'match_id');
    }

    public function mentions()
    {
        return $this->hasMany(Mention::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'message_id');
    }

    public function moderatedMessage()
    {
        return $this->hasOne(ModeratedMessage::class);
    }
}
