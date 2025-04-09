<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id', 'views', 'attempts', 'correct_answers','poll_votes', 'option_counts', 'user_id'
    ];
    
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
   
    
}
