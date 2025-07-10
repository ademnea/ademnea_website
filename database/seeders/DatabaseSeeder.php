<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            // 'firstName' => 'Allan',
            'email' => 'admin@admin.com',
            //'photo' => 'images/avatar/1.png',
            // 'phoneNumber' => '0755555555',
            'password'=>bcrypt("admin..123")
        ]);

        $this->call([
            FarmerSeeder::class,
            FarmSeeder::class,
            HiveSeeder::class,
            HiveTemperatureSeeder::class,
            TeamSeeder::class,
        ]);
    }
}
