<?php

namespace App\Services;

use App\Models\User;

class FeatureGate
{
    public static function allows(User $user, string $feature): bool
    {
        $state = new SubscriptionState();

        // Normalize plan
       $plan = trim(strtolower($user->plan ?? 'daily'));

        // 🔒 Expired users fall back to daily (free behavior)
        if (!$state->isActive($user)) {
            $plan = 'daily';
        }

        return match ($feature) {

            // ✅ Everyone can send
            'chat.send' => true,

            // ⚡ Unlimited chat (no rate limit)
            'chat.unlimited' => in_array($plan, ['weekly', 'monthly']),

            // ⭐ VIP perks
            'chat.vip' => $plan === 'monthly',

            // 🎯 Quiz always available
            'quiz.play' => true,

            // 🏆 Leaderboard boost
            'leaderboard.boost' => $plan === 'monthly',

            default => false,
        };
    }
}