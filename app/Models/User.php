<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'subscribed_at',
        'expires_at',
        'phone_number',
        'token',
        'auto_renew',
        'team_id'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relationship: A user has many subscription renewal logs.
     */
    public function subscriptionRenewalLogs()
    {
        return $this->hasMany(SubscriptionRenewal::class);
    }
}
