<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContentStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'content_id', 'answered_correctly', 'selected_options', 'attempted_at', 'poll_votes'
    ];
    
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
