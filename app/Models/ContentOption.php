<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id', 'option_text', 'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
