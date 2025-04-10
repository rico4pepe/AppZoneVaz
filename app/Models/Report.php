<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'reporter_id',
        'reason',
        'status',
    ];

    // Define the relationships
    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
