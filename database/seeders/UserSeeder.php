<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // First, clear existing users and reset auto-increment
        $this->truncateUsers();
        
        $plans = ['Daily', 'Weekly', 'Monthly'];
        $monthlyCount = 0;
        
        foreach (range(1, 10) as $i) {
            // Ensure at least 5 monthly users
            if ($monthlyCount < 5) {
                $plan = 'Monthly';
                $monthlyCount++;
                $wifi = 0; // Monthly users get no token
            } else {
                // Random plan for the rest
                $plan = $plans[array_rand($plans)];
                $wifi = ($plan === 'Monthly') ? 0 : rand(0, 1); // Monthly never gets token
            }

            $expiresAt = match($plan) {
                'Daily' => Carbon::now()->addDay(),
                'Weekly' => Carbon::now()->addWeek(),
                'Monthly' => Carbon::now()->addMonth(),
            };

            // Generate token only if wifi is enabled AND not monthly
            $token = ($wifi && $plan !== 'Monthly') ? bin2hex(random_bytes(5)) : null;

            // Format phone number to be 11 digits
            $phoneNumber = '080' . str_pad($i, 8, '0', STR_PAD_LEFT);

            User::create([
                'name' => "Test User $i",
                'phone_number' => $phoneNumber,
                'plan' => $plan,
                'password' => bcrypt('password123'), // Consistent password for testing
                'email' => "user$i@test.com",
                'subscribed_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'token' => $token,
            ]);
        }
        
        $this->command->info("✅ Seeded 10 users:");
        $this->command->info("   - Monthly users: " . User::where('plan', 'Monthly')->count() . " (at least 5 guaranteed)");
        $this->command->info("   - Users with tokens: " . User::whereNotNull('token')->count());
        $this->command->info("   - Monthly users with tokens: " . User::where('plan', 'Monthly')->whereNotNull('token')->count() . " (should be 0)");
    }
    
    /**
     * Truncate users table and reset auto-increment
     */
    private function truncateUsers(): void
    {
        // Use DELETE instead of TRUNCATE to avoid foreign key issues
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('users')->delete();
        \DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Also clear user_preferences if it exists
        if (\Schema::hasTable('user_preferences')) {
            \DB::table('user_preferences')->delete();
            \DB::statement('ALTER TABLE user_preferences AUTO_INCREMENT = 1');
        }
    }
}