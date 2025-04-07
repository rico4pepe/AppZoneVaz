<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'content_id', 'content_option_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function contentOption()
    {
        return $this->belongsTo(ContentOption::class);
    }
}
