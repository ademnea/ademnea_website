<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateSampleTeamMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'team:generate-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample team members for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating sample team members...');
        
        // Categories and sample data
        $categories = [
            'Leadership' => [
                ['name' => 'Dr. John Smith', 'title' => 'Director'],
                ['name' => 'Dr. Sarah Johnson', 'title' => 'Deputy Director'],
            ],
            'Senior Researchers' => [
                ['name' => 'Dr. Michael Brown', 'title' => 'Senior Research Fellow'],
                ['name' => 'Dr. Emily Davis', 'title' => 'Senior Research Scientist'],
            ],
            'Researchers' => [
                ['name' => 'Robert Wilson', 'title' => 'Research Associate'],
                ['name' => 'Jennifer Lee', 'title' => 'Research Assistant'],
            ],
            'Interns' => [
                ['name' => 'Alex Martinez', 'title' => 'Research Intern'],
                ['name' => 'Taylor Kim', 'title' => 'Graduate Intern'],
            ],
            'Alumni' => [
                ['name' => 'Dr. James Wilson', 'title' => 'Former Postdoc'],
                ['name' => 'Dr. Lisa Wong', 'title' => 'Former Research Fellow'],
            ],
        ];
        
        // Ensure the team directory exists
        $teamPath = public_path('images/team');
        if (!File::exists($teamPath)) {
            File::makeDirectory($teamPath, 0755, true);
        }
        
        // Copy sample image
        $sampleImage = public_path('images/avatar-placeholder.png');
        if (!File::exists($sampleImage)) {
            // Create a blank image if placeholder doesn't exist
            $img = imagecreatetruecolor(400, 400);
            $bgColor = imagecolorallocate($img, 200, 200, 200);
            imagefill($img, 0, 0, $bgColor);
            imagepng($img, $sampleImage);
            imagedestroy($img);
        }
        
        // Clear existing team members
        Team::truncate();
        
        // Create sample team members
        foreach ($categories as $category => $members) {
            foreach ($members as $member) {
                $imageName = Str::slug($member['name']) . '.png';
                $destination = $teamPath . '/' . $imageName;
                
                // Copy the sample image with a new name
                copy($sampleImage, $destination);
                
                Team::create([
                    'name' => $member['name'],
                    'title' => $member['title'],
                    'description' => 'This is a sample description for ' . $member['name'] . 
                                    '. This section can include their research interests, background, and other relevant information.',
                    'category' => $category,
                    'image_path' => 'team/' . $imageName,
                ]);
                
                $this->info('Created: ' . $member['name'] . ' (' . $category . ')');
            }
        }
        
        $this->info('Sample team members generated successfully!');
        return 0;
    }
}
