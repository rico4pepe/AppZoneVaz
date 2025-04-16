<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mention extends Model
{
    protected $fillable = ['chat_message_id', 'mentioned_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class);
    }
}

