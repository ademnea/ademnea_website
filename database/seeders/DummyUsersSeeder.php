<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DummyUsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Alice Nakamya', 'email' => 'alice@ademnea.org'],
            ['name' => 'Brian Ochieng', 'email' => 'brian@ademnea.org'],
            ['name' => 'Carol Atim', 'email' => 'carol@ademnea.org'],
            ['name' => 'David Ssemakula', 'email' => 'david@ademnea.org'],
            ['name' => 'Eva Namukasa', 'email' => 'eva@ademnea.org'],
            ['name' => 'Frank Okello', 'email' => 'frank@ademnea.org'],
            ['name' => 'Grace Apio', 'email' => 'grace@ademnea.org'],
            ['name' => 'Henry Mugisha', 'email' => 'henry@ademnea.org'],
            ['name' => 'Irene Akello', 'email' => 'irene@ademnea.org'],
            ['name' => 'James Byaruhanga', 'email' => 'james@ademnea.org'],
            ['name' => 'Karen Nabukenya', 'email' => 'karen@ademnea.org'],
            ['name' => 'Liam Tumwesigye', 'email' => 'liam@ademnea.org'],
            ['name' => 'Mary Achan', 'email' => 'mary@ademnea.org'],
            ['name' => 'Noah Kiggundu', 'email' => 'noah@ademnea.org'],
            ['name' => 'Olivia Tendo', 'email' => 'olivia@ademnea.org'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                ['name' => $user['name'], 'password' => Hash::make('password123')]
            );
        }
    }
}
