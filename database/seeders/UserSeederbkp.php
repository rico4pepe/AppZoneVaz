<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $plans = ['Daily', 'Weekly', 'Monthly'];

        foreach (range(1, 10) as $i) {
            $faker = \Faker\Factory::create();
            $plan = $plans[array_rand($plans)];

            $expiresAt = match($plan) {
                'Daily' => Carbon::now()->addDay(),
                'Weekly' => Carbon::now()->addWeek(),
                'Monthly' => Carbon::now()->addMonth(),
            };

            $wifi = rand(0, 1); // 50% chance
            $token = $wifi ? bin2hex(random_bytes(5)) : null;

            User::create([
                'name' => "Test User $i",
                'phone_number' => '080000000' . $i,
                'plan' => $plan,
                'password' => bcrypt($faker->password()),
                'email'=>$faker->unique()->safeEmail(),
                'subscribed_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'token' => $token,
            ]);
        }
    }
}
