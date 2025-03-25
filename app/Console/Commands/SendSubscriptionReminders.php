<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\SubscriptionReminderSms;

class SendSubscriptionReminders extends Command
{
    protected $signature = 'subscriptions:remind';
    protected $description = 'Send SMS reminders to users whose subscriptions are about to expire';

    public function handle()
    {
        // Get users whose subscriptions expire in 3 days
        $users = User::whereNotNull('expires_at')
            ->whereDate('expires_at', '=', Carbon::now()->addDays(3)->toDateString())
            ->get();

        foreach ($users as $user) {
            $user->notify(new SubscriptionReminderSms($user));
            Log::info("Subscription SMS reminder sent to {$user->phone_number}");
        }

        $this->info('Subscription SMS reminders sent successfully.');
    }
}
