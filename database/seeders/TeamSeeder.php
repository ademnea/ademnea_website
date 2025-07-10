<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    public function run()
    {
        // Remove all existing records before inserting new ones
        DB::table('teams')->truncate();
        DB::table('teams')->insert([
            [
                'name' => 'Dr. Jane Doe',
                'title' => 'Lead Researcher',
                'description' => 'Expert in pollinator ecology and project lead.',
                'image_path' => 'images/team/jane_doe.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Prof. John Smith',
                'title' => 'Senior Researcher',
                'description' => 'Specialist in climate data analysis.',
                'image_path' => 'images/team/john_smith.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mary Wanjiku',
                'title' => 'Research Intern',
                'description' => 'Assists with field data collection and analysis.',
                'image_path' => 'images/team/mary_wanjiku.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Samuel Otieno',
                'title' => 'Intern',
                'description' => 'Supports lab work and reporting.',
                'image_path' => 'images/team/samuel_otieno.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Amina Yusuf',
                'title' => 'Intern',
                'description' => 'Focuses on data entry and preliminary analysis.',
                'image_path' => 'images/team/amina_yusuf.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Peter Kimani',
                'title' => 'Research Intern',
                'description' => 'Assists with field surveys and reporting.',
                'image_path' => 'images/team/peter_kimani.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lucy Njeri',
                'title' => 'Intern',
                'description' => 'Supports lab sample processing and documentation.',
                'image_path' => 'images/team/lucy_njeri.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Brian Oduor',
                'title' => 'Research Intern',
                'description' => 'Works on digital data management and archiving.',
                'image_path' => 'images/team/brian_oduor.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
