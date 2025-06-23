<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user if it doesn't exist
        if (!User::where('email', 'admin@admin.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin..123'),
                'email_verified_at' => now(),
            ]);
        }

        // Run seeders
        $this->call([
            TeamSeeder::class,
            // Comment out or remove the following seeders if not needed
            // FarmerSeeder::class,
            // FarmSeeder::class,
            // HiveSeeder::class,
            // HiveTemperatureSeeder::class,
        ]);
    }
}
