<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'event_id',
        'message',
        'is_hidden',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function reports()
        {
            return $this->hasMany(Report::class, 'message_id');
        }

        protected $attributes = [
            'status' => 'open',
        ];
}
