<?php

namespace App\Console\Commands;

use App\Models\Farm;
use App\Services\Analysis\CountAnalysisService;
use Illuminate\Console\Command;

class AnalyseHiveCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bee:analyse
                            {--farm= : limit to one farm id}
                            {--from= : start date, YYYY-MM-DD}
                            {--to= : end date, YYYY-MM-DD}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build daily features, classifications and insights from bee counts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new CountAnalysisService();

        $farms = $this->option('farm')
            ? Farm::where('id', $this->option('farm'))->get()
            : Farm::all();

        if ($farms->isEmpty()) {
            $this->warn('No farms found.');
            return 0;
        }

        foreach ($farms as $farm) {
            $this->line("Farm {$farm->id} - {$farm->name}");

            $result = $service->runForFarm(
                $farm->id,
                $this->option('from'),
                $this->option('to')
            );

            $this->info(sprintf(
                '  %d hives, %d daily rows, %d insights',
                $result['hives'],
                $result['features'],
                $result['insights']
            ));
        }

        return 0;
    }
}
