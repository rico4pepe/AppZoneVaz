<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRenewalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'attempted_at',
        'success',
        'failure_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
