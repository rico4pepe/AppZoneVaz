<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'purchase_code',
        'previous_expires_at',
        'new_expires_at'
    ];

     // Define the inverse relationship if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
