<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SubscriptionRenewal;
use App\Models\SubscriptionRenewalLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoRenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:auto-renew';
    protected $description = 'Automatically renew subscriptions for users who enabled auto-renewal';

    public function handle()
    {
        // Get users whose subscription has expired and have auto-renew enabled
        $users = User::where('auto_renew', true)
            ->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', Carbon::now())
            ->get();

        foreach ($users as $user) {
            $failureReason = null;
            $success = false;

            try {
                // Check if the user's plan is valid
                if (!in_array($user->plan, ['Daily', 'Weekly', 'Monthly'])) {
                    $failureReason = "Invalid plan type: {$user->plan}";
                } else {
                    // Store previous expiry before renewal
                    $previousExpiresAt = $user->expires_at;

                    // Extend subscription based on plan
                    $subscriptionDuration = [
                        'Daily' => Carbon::now()->addDay(),
                        'Weekly' => Carbon::now()->addWeek(),
                        'Monthly' => Carbon::now()->addMonth(),
                    ];

                    $user->subscribed_at = Carbon::now();
                    $user->expires_at = $subscriptionDuration[$user->plan];
                    $user->save();

                    // Log the renewal in the subscription_renewals table
                    SubscriptionRenewal::create([
                        'user_id' => $user->id,
                        'purchase_code' => $user->plan,
                        'previous_expires_at' => $previousExpiresAt,
                        'new_expires_at' => $user->expires_at
                    ]);

                    $success = true;
                    Log::info("Auto-renewed subscription for {$user->phone_number}");
                }
            } catch (\Exception $e) {
                $failureReason = $e->getMessage();
                Log::error("Auto-renewal failed for {$user->phone_number}: {$failureReason}");
            }

            // Log the attempt
            SubscriptionRenewalLog::create([
                'user_id' => $user->id,
                'plan' => $user->plan,
                'success' => $success,
                'failure_reason' => $failureReason
            ]);
        }

        $this->info('Auto-renewal process completed.');
    }
}
