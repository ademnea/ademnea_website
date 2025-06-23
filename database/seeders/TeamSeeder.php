<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing team members
        Team::truncate();
        
        // Ensure the team directory exists
        $teamPath = public_path('images/team');
        if (!File::exists($teamPath)) {
            File::makeDirectory($teamPath, 0755, true);
        }
        
        // Sample data
        $teamMembers = [
            // Leadership
            [
                'name' => 'Dr. John Smith',
                'title' => 'Director',
                'description' => 'Dr. John Smith is a distinguished researcher with over 20 years of experience in the field. His work focuses on advanced data analysis and machine learning applications.',
                'category' => 'Leadership',
                'image_path' => 'team/dr-john-smith.png',
            ],
            [
                'name' => 'Dr. Sarah Johnson',
                'title' => 'Deputy Director',
                'description' => 'Dr. Sarah Johnson specializes in computational biology and has published numerous papers in top-tier journals. She leads our research initiatives in biomedical applications.',
                'category' => 'Leadership',
                'image_path' => 'team/dr-sarah-johnson.png',
            ],
            
            // Senior Researchers
            [
                'name' => 'Dr. Michael Brown',
                'title' => 'Senior Research Fellow',
                'description' => 'Dr. Brown\'s research focuses on artificial intelligence and its applications in healthcare. He has received multiple awards for his innovative work.',
                'category' => 'Senior Researchers',
                'image_path' => 'team/dr-michael-brown.png',
            ],
            [
                'name' => 'Dr. Emily Davis',
                'title' => 'Senior Research Scientist',
                'description' => 'Dr. Davis is an expert in data visualization and human-computer interaction. Her work helps make complex data more accessible to researchers and the public.',
                'category' => 'Senior Researchers',
                'image_path' => 'team/dr-emily-davis.png',
            ],
            
            // Researchers
            [
                'name' => 'Robert Wilson',
                'title' => 'Research Associate',
                'description' => 'Robert works on developing new algorithms for data processing and analysis. He has a strong background in computer science and mathematics.',
                'category' => 'Researchers',
                'image_path' => 'team/robert-wilson.png',
            ],
            [
                'name' => 'Jennifer Lee',
                'title' => 'Research Assistant',
                'description' => 'Jennifer is currently pursuing her PhD while working on machine learning applications in environmental science. She brings a fresh perspective to our research team.',
                'category' => 'Researchers',
                'image_path' => 'team/jennifer-lee.png',
            ],
            
            // Interns
            [
                'name' => 'Alex Martinez',
                'title' => 'Research Intern',
                'description' => 'Alex is an undergraduate student majoring in Computer Science. They are working on developing web applications to support our research projects.',
                'category' => 'Interns',
                'image_path' => 'team/alex-martinez.png',
            ],
            [
                'name' => 'Taylor Kim',
                'title' => 'Graduate Intern',
                'description' => 'Taylor is a Master\'s student in Data Science, focusing on natural language processing. They are assisting with text analysis in our research projects.',
                'category' => 'Interns',
                'image_path' => 'team/taylor-kim.png',
            ],
            
            // Alumni
            [
                'name' => 'Dr. James Wilson',
                'title' => 'Former Postdoc',
                'description' => 'Dr. Wilson was a postdoctoral researcher in our lab from 2018-2020. He is now an Assistant Professor at a leading research university.',
                'category' => 'Alumni',
                'image_path' => 'team/dr-james-wilson.png',
            ],
            [
                'name' => 'Dr. Lisa Wong',
                'title' => 'Former Research Fellow',
                'description' => 'Dr. Wong was a Research Fellow in our lab from 2017-2021. She is now a Senior Data Scientist at a major tech company.',
                'category' => 'Alumni',
                'image_path' => 'team/dr-lisa-wong.png',
            ],
        ];
        
        // Create placeholder images and team members
        foreach ($teamMembers as $member) {
            $imagePath = public_path('images/' . $member['image_path']);
            $imageDir = dirname($imagePath);
            
            // Create directory if it doesn't exist
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }
            
            // Create a placeholder image if it doesn't exist
            if (!File::exists($imagePath)) {
                $this->createPlaceholderImage($imagePath, $member['name']);
            }
            
            // Create team member
            Team::create($member);
        }
        
        $this->command->info('Team members seeded successfully!');
    }
    
    /**
     * Create a placeholder image with the person's initials
     *
     * @param string $path
     * @param string $name
     * @return void
     */
    private function createPlaceholderImage($path, $name)
    {
        $size = 400;
        $img = imagecreatetruecolor($size, $size);
        
        // Background color (light gray)
        $bgColor = imagecolorallocate($img, 240, 240, 240);
        imagefill($img, 0, 0, $bgColor);
        
        // Text color (dark gray)
        $textColor = imagecolorallocate($img, 120, 120, 120);
        
        // Get initials
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            if (preg_match('/^[A-Z]/', $word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // Use first 2-3 initials
        $initials = substr($initials, 0, 3);
        
        // Choose font
        $font = 5; // Default font (1-5)
        $fontWidth = imagefontwidth($font) * strlen($initials);
        $fontHeight = imagefontheight($font);
        
        // Calculate position to center the text
        $x = ($size - $fontWidth) / 2;
        $y = ($size - $fontHeight) / 2;
        
        // Add text
        imagestring($img, $font, $x, $y, $initials, $textColor);
        
        // Save image
        imagepng($img, $path);
        imagedestroy($img);
    }
}
