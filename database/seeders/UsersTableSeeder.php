<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Jasper Rock',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password123'),
            'phone_number' => '08143456789',
            'plan' => 'Monthly',
            'subscribed_at' => now(),
            'expires_at' => now()->addMonth(),
            'club' => null, // Ensures modal appears
        ]);

        User::create([
            'name' => 'lasper Doe',
            'email' => 'janedoe@example.com',
            'password' => Hash::make('password123'),
            'phone_number' => '08098765432',
            'plan' => 'Weekly',
            'subscribed_at' => now(),
            'expires_at' => now()->addWeek(),
            'club' => 'Chelsea', // Already selected a club
        ]);
    }
}
