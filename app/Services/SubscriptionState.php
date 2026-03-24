<?php
namespace App\Services;
use App\Models\User;
class SubscriptionState
{
    public function isActive(User $user): bool
    {
        return $user->expires_at && now()->lt($user->expires_at);
    }

    public function isExpired(User $user): bool
    {
        return $user->expires_at && now()->gte($user->expires_at);
    }

    public function daysRemaining(User $user): int
    {
        if (!$this->isActive($user)) {
            return 0;
        }

        return now()->diffInDays($user->expires_at);
    }
}