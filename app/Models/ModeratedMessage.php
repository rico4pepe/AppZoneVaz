<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeratedMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_message_id',
        'user_id',
        'message',
        'categories',
        'severity',
    ];

    protected $casts = [
        'categories' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class); // add this
    }
}
