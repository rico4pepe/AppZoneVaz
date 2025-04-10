<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBan extends Model
{
    use HasFactory;
       // Define the fillable attributes
       protected $fillable = [
        'user_id',
        'reason',
        'banned_until',
        'issued_by',
    ];

    // Define the relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
