<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
   
         // Run subscription reminders daily at 8 AM
         $schedule->command('subscriptions:remind')->dailyAt('08:00');

          // Run auto-renewal daily at 12:01 AM
         $schedule->command('subscriptions:auto-renew')->dailyAt('00:01'); // Runs at 12:01 AM daily

         //Fetch leagues from SportMonks every Daily
            $schedule->command('fetch:leagues')->daily();

        // Fetch countries from SportMonks every Sunday at 8 AM
        $schedule->command('fetch:countries')->weeklyOn(0, '08:00');
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
