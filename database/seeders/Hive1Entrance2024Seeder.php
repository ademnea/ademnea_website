<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Farmer;
use App\Models\Hive;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Real bee counts for one hive: hive 1's entrance camera, 14 Sep to 18 Dec
 * 2024, run through the deployed yolo26x-poc model on 2 Nov 2026.
 *
 * Source: bee-eval-2024-09-to-12-hive1.zip, exported from the live database
 * by bee-detection-poc/ademnea/export_eval.py. A copy of that export lives
 * next to this seeder in data/hive1_entrance_2024/ (results.csv is what is
 * loaded; spotcheck.csv and summary.md are kept for reference).
 *
 * Unlike DemoBeeCountSeeder nothing here is synthetic. The point is to see
 * how the analysis behaves on real counts, which are much lower and much
 * sparser than the demo data (median peak 2 bees per clip, 4 captures a
 * day, 35% of clips failed because the video file is missing).
 *
 * What is written
 *
 *   farms         one farm, FARM_NAME, owned by the first farmer
 *   hives         one hive in that farm
 *   hive_videos   one row per results.csv row, keeping the ORIGINAL
 *                 hive_video_id from the live database so a row can be traced
 *                 back; path and created_at exactly as exported
 *   bee_counts    one row per video, columns laid out the way the dispatcher
 *                 writes them (bee_count mirrors max_count, timestamp mirrors
 *                 hive_videos.created_at, failed rows keep their error)
 *
 * Assumptions, where the CSV does not say
 *
 *   - farm location: Makerere University, Kampala (the hive's real
 *     coordinates are not in the export)
 *   - entrance_orientation, hive_type, site_group: unknown, left null
 *   - bee_counts.attempts: 1 for every row (the export has no attempts
 *     column; the dispatcher would have retried the 404s up to 3 times)
 *   - bee_counts.created_at / updated_at: processed_at for done rows, null
 *     for failed rows (no time is recorded for a failure)
 *   - the hive keeps the filename prefix "1_" from the live database even
 *     though it gets a new hive id here; the analysis reads only the
 *     date-time part of the name
 *
 * Idempotent: re-running clears the farm's videos, counts and analysis
 * output first.
 *
 *   php artisan db:seed --class=Hive1Entrance2024Seeder
 *   php artisan bee:analyse --farm=<id> --from=2024-09-14 --to=2024-12-19
 */
class Hive1Entrance2024Seeder extends Seeder
{
    const FARM_NAME = 'Hive 1 entrance evaluation (Sep-Dec 2024)';

    const DATA_FILE = __DIR__ . '/data/hive1_entrance_2024/results.csv';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!is_readable(self::DATA_FILE)) {
            $this->command->error('Missing ' . self::DATA_FILE);
            return;
        }

        $rows = $this->readCsv(self::DATA_FILE);
        if (empty($rows)) {
            $this->command->error('results.csv has no rows');
            return;
        }

        $farmer = Farmer::orderBy('id')->first();
        if (!$farmer) {
            $this->command->error(
                'No farmers exist. Run `php artisan db:seed` first so FarmerSeeder '
                    . 'creates one, then re-run this seeder.'
            );
            return;
        }

        $farm = $this->ensureFarm($farmer);
        $hive = $this->ensureHive($farm);

        // The export's hive_video_id values are kept as primary keys. Refuse
        // to run if any of them already belongs to a different hive.
        $ids = array_column($rows, 'hive_video_id');
        $clash = DB::table('hive_videos')
            ->whereIn('id', $ids)
            ->where('hive_id', '!=', $hive->id)
            ->count();
        if ($clash) {
            $this->command->error(
                "$clash hive_videos ids from the export already belong to other hives; aborting."
            );
            return;
        }

        $this->command->info("Seeding 2024 hive 1 counts into farm {$farm->id} - {$farm->name}, hive {$hive->id}");

        $this->clearPrevious($hive);

        $videoRows = [];
        $countRows = [];
        foreach ($rows as $r) {
            $videoRows[] = $this->videoRow($r, $hive);
            $countRows[] = $this->countRow($r);
        }

        DB::transaction(function () use ($videoRows, $countRows) {
            foreach (array_chunk($videoRows, 300) as $chunk) {
                DB::table('hive_videos')->insert($chunk);
            }
            foreach (array_chunk($countRows, 300) as $chunk) {
                DB::table('bee_counts')->insert($chunk);
            }
        });

        $status = array_count_values(array_column($rows, 'status'));
        $this->command->info(sprintf(
            '  %d videos, %d counts (%s)',
            count($videoRows),
            count($countRows),
            implode(', ', array_map(
                function ($k, $v) { return "$v $k"; },
                array_keys($status),
                $status
            ))
        ));
        $this->command->line(
            '  Next: php artisan bee:analyse --farm=' . $farm->id
                . ' --from=' . substr($rows[0]['path'], 2, 10)
                . ' --to=' . substr(end($rows)['path'], 2, 10)
        );
    }

    /**
     * results.csv as a list of associative rows, in capture order. Empty
     * strings become null so numeric columns of failed rows stay null.
     */
    protected function readCsv($file)
    {
        $fh = fopen($file, 'r');
        $header = fgetcsv($fh);
        $rows = [];

        while (($line = fgetcsv($fh)) !== false) {
            if (count($line) !== count($header)) {
                continue;
            }
            $row = array_combine($header, $line);
            foreach ($row as $k => $v) {
                $row[$k] = ($v === '' ? null : $v);
            }
            $rows[] = $row;
        }
        fclose($fh);

        // capture time is in the filename: 1_2024-09-14_060006.mp4
        usort($rows, function ($a, $b) {
            return strcmp(substr($a['path'], 2), substr($b['path'], 2));
        });

        return $rows;
    }

    protected function videoRow(array $r, Hive $hive)
    {
        return [
            'id'         => (int) $r['hive_video_id'],
            'path'       => $r['path'],
            'hive_id'    => $hive->id,
            'created_at' => $r['created_at'],
            'updated_at' => $r['created_at'],
        ];
    }

    /**
     * One bee_counts row, shaped the way ademnea/dispatcher.py leaves it.
     */
    protected function countRow(array $r)
    {
        $done = $r['status'] === 'done';

        return [
            'timestamp'         => $r['created_at'],
            'hive_video_id'     => (int) $r['hive_video_id'],
            'video_filename'    => $r['path'],
            'bee_count'         => $done ? (int) $r['max_count'] : 0,
            'mean_count'        => $r['mean_count'] === null ? null : (float) $r['mean_count'],
            'max_count'         => $r['max_count'] === null ? null : (int) $r['max_count'],
            'activity_fraction' => $r['activity_fraction'] === null ? null : (float) $r['activity_fraction'],
            'frames_analyzed'   => $r['frames_analyzed'] === null ? null : (int) $r['frames_analyzed'],
            'model_version'     => $r['model_version'],
            'processing_status' => $r['status'],
            'error_message'     => $r['error'],
            'attempts'          => 1,
            'processed_at'      => $r['processed_at'],
            'created_at'        => $r['processed_at'],
            'updated_at'        => $r['processed_at'],
        ];
    }

    protected function ensureFarm(Farmer $farmer)
    {
        return Farm::firstOrCreate(
            ['name' => self::FARM_NAME],
            [
                'ownerId'     => $farmer->id,
                'district'    => 'Kampala',
                'address'     => 'Makerere University, Kampala, Uganda',
                'latitude'    => 0.3353,
                'longitude'   => 32.5686,
                'description' => 'Real counts from hive 1\'s entrance camera, 14 Sep to 18 Dec 2024, '
                    . 'processed by yolo26x-poc. Loaded by Hive1Entrance2024Seeder from '
                    . 'bee-eval-2024-09-to-12-hive1.zip. Location is assumed, not recorded.',
            ]
        );
    }

    protected function ensureHive(Farm $farm)
    {
        $hive = Hive::where('farm_id', $farm->id)->orderBy('id')->first();
        if ($hive) {
            return $hive;
        }

        return Hive::create([
            'farm_id'      => $farm->id,
            'latitude'     => (string) $farm->latitude,
            'longitude'    => (string) $farm->longitude,
            'connected'    => true,
            'colonized'    => true,
            'colony_epoch' => 1,
        ]);
    }

    /**
     * Drop everything a previous run wrote for this hive.
     */
    protected function clearPrevious(Hive $hive)
    {
        $videoIds = DB::table('hive_videos')->where('hive_id', $hive->id)->pluck('id');

        if ($videoIds->count()) {
            DB::table('bee_counts')->whereIn('hive_video_id', $videoIds)->delete();
            DB::table('hive_videos')->whereIn('id', $videoIds)->delete();
        }

        DB::table('hive_daily_features')->where('hive_id', $hive->id)->delete();
        DB::table('hive_baselines')->where('hive_id', $hive->id)->delete();
        DB::table('hive_insights')->where('hive_id', $hive->id)->delete();
        DB::table('hive_classifications')->where('hive_id', $hive->id)->delete();
    }
}
