<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Hive;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Four weeks of bee counts for one apiary of three hives, for demonstrating
 * the analysis before the live pipeline has accumulated history.
 *
 * The data is synthetic and deliberately shaped: three different kinds of
 * change are planted so each detector has something to find.
 *
 *   HIVE A  normal throughout, with one benign afternoon spike on day 20
 *           (orientation flights - a healthy event that looks alarming)
 *   HIVE B  abrupt drop to a third of normal on day 16, sustained
 *   HIVE C  slow decline of 3% a day from day 12
 *   all     low together on days 4, 9, 10 and 23 (poor flying conditions)
 *
 * The site-wide dips are the important ones for the demonstration: with no
 * weather data, the only thing that stops them becoming three false alarms
 * is that every hive moves together, which is what the apiary comparison
 * is there to catch.
 *
 * Edge cases are planted too - a day with no rows at all, a day whose rows
 * carry no observation, some clips with too few frames, and a detector
 * version change - so the validity layer can be seen working.
 *
 * Remove with:  php artisan db:seed --class=DemoBeeCountSeeder  (it is
 * idempotent - it clears its own rows first)
 */
class DemoBeeCountSeeder extends Seeder
{
    /** Capture hours, 06:00 to 18:00. */
    protected $hours = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];

    protected $days = 28;

    const DET_OLD = 'yolo26x-20260912-a1b2c3d';
    const DET_NEW = 'yolo26x-20261001-9f8e7d6';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $farm = Farm::first();

        if (!$farm) {
            $this->command->error(
                'No farms exist. Run `php artisan db:seed` first so FarmerSeeder '
                    . 'and FarmSeeder create one, then re-run this seeder.'
            );
            return;
        }

        $this->command->info("Seeding demo counts into farm {$farm->id} - {$farm->name}");

        $hives = $this->ensureHives($farm);
        $this->clearPrevious($hives);

        // Day 1 is 27 days ago, so day 28 is today and the default 30-day
        // range on the dashboard lands squarely on the data.
        $day1 = Carbon::today()->subDays($this->days - 1);

        $specs = [
            0 => ['scale' => 9.5, 'label' => 'A'],
            1 => ['scale' => 7.5, 'label' => 'B'],
            2 => ['scale' => 8.0, 'label' => 'C'],
        ];

        $videoRows = [];
        $countRows = [];

        foreach ($hives as $i => $hive) {
            $spec = $specs[$i];

            for ($d = 1; $d <= $this->days; $d++) {
                // Hive A's camera is offline for a whole day: no rows at all,
                // which must read as "monitor offline", never as "no bees".
                if ($spec['label'] === 'A' && $d === 13) {
                    continue;
                }

                $date = (clone $day1)->addDays($d - 1);

                foreach ($this->hours as $h) {
                    // a couple of percent of clips never arrive
                    if (mt_rand(1, 100) <= 2) {
                        continue;
                    }

                    $ts = (clone $date)->setTime($h, 0, 0);
                    $filename = $hive->id . '_' . $ts->format('Y-m-d_His') . '.mp4';

                    $videoRows[] = [
                        'path'       => 'hivevideo/' . $filename,
                        'hive_id'    => $hive->id,
                        'created_at' => $ts->toDateTimeString(),
                        'updated_at' => $ts->toDateTimeString(),
                    ];

                    $countRows[] = $this->buildCount($spec, $d, $h, $ts, $filename);
                }
            }
        }

        // hive_videos first, then match each count to the id it was given
        foreach (array_chunk($videoRows, 300) as $chunk) {
            DB::table('hive_videos')->insert($chunk);
        }

        $ids = DB::table('hive_videos')
            ->whereIn('path', array_column($videoRows, 'path'))
            ->pluck('id', 'path');

        foreach ($countRows as $k => $row) {
            $path = 'hivevideo/' . $row['video_filename'];
            if (!isset($ids[$path])) {
                unset($countRows[$k]);
                continue;
            }
            $countRows[$k]['hive_video_id'] = $ids[$path];
        }

        foreach (array_chunk(array_values($countRows), 300) as $chunk) {
            DB::table('bee_counts')->insert($chunk);
        }

        $this->command->info(sprintf(
            '  %d videos, %d counts across %d hives',
            count($videoRows),
            count($countRows),
            count($hives)
        ));
        $this->command->line('  Next: php artisan bee:analyse --farm=' . $farm->id);
    }

    /**
     * One row of bee_counts, already in the 'done' state the dispatcher
     * leaves behind.
     */
    protected function buildCount($spec, $d, $h, Carbon $ts, $filename)
    {
        $detector = $d < 24 ? self::DET_OLD : self::DET_NEW;
        $frames   = 50;

        $mean = $spec['scale']
            * $this->diurnal($h)
            * $this->conditions($d)
            * $this->event($spec['label'], $d, $h)
            * (1 + $this->jitter(0.12));

        $mean = max($mean, 0.0);

        // Hive C: short clips early on day 7 - kept, but too thin to trust
        if ($spec['label'] === 'C' && $d === 7 && $h <= 9) {
            $frames = mt_rand(3, 7);
            $mean  *= (1 + $this->jitter(0.45));
        }

        // Hive B: day 21 uploads fail. Rows exist, observations do not.
        if ($spec['label'] === 'B' && $d === 21) {
            return [
                'timestamp'         => $ts->toDateTimeString(),
                'video_filename'    => $filename,
                'bee_count'         => 0,
                'mean_count'        => null,
                'max_count'         => null,
                'activity_fraction' => null,
                'frames_analyzed'   => 0,
                'model_version'     => $detector,
                'processing_status' => 'done',
                'attempts'          => 1,
                'processed_at'      => $ts->toDateTimeString(),
                'created_at'        => $ts->toDateTimeString(),
                'updated_at'        => $ts->toDateTimeString(),
            ];
        }

        $max = (int) round($mean * (1.65 + (mt_rand(0, 40) / 100)));
        $fraction = round(min(1.0, max(0.0, 1 - exp(-1.25 * $mean))), 3);

        return [
            'timestamp'         => $ts->toDateTimeString(),
            'video_filename'    => $filename,
            'bee_count'         => $max,           // mirrors max_count, as the pipeline does
            'mean_count'        => round($mean, 2),
            'max_count'         => $max,
            'activity_fraction' => $fraction,
            'frames_analyzed'   => $frames,
            'model_version'     => $detector,
            'processing_status' => 'done',
            'attempts'          => 1,
            'processed_at'      => $ts->toDateTimeString(),
            'created_at'        => $ts->toDateTimeString(),
            'updated_at'        => $ts->toDateTimeString(),
        ];
    }

    /**
     * Activity through the day: low at dawn, peaking around midday.
     */
    protected function diurnal($hour)
    {
        return exp(-pow($hour - 12.3, 2) / (2 * pow(2.6, 2)));
    }

    /**
     * Site-wide flying conditions. Affects every hive equally, which is what
     * makes the apiary comparison able to recognise it.
     */
    protected function conditions($d)
    {
        if ($d == 4)  return 0.52;
        if ($d == 9)  return 0.38;
        if ($d == 10) return 0.30;
        if ($d == 23) return 0.58;
        return 1.0;
    }

    /**
     * The hive-specific changes the detectors are meant to find.
     */
    protected function event($label, $d, $h)
    {
        if ($label === 'A') {
            // orientation flights: a short, sharp afternoon burst
            return ($d == 20 && $h >= 14) ? 2.3 : 1.0;
        }
        if ($label === 'B') {
            return $d < 16 ? 1.0 : 0.33;        // abrupt level shift
        }
        if ($label === 'C') {
            return $d < 12 ? 1.0 : pow(0.970, $d - 11);   // slow decline
        }
        return 1.0;
    }

    protected function jitter($sd)
    {
        // Box-Muller, so the noise is normal rather than uniform
        $u1 = max(mt_rand(1, 10000) / 10000, 0.0001);
        $u2 = mt_rand(1, 10000) / 10000;
        return $sd * sqrt(-2 * log($u1)) * cos(2 * M_PI * $u2);
    }

    /**
     * Make sure the farm has at least three hives, since the apiary
     * comparison needs peers to work at all.
     */
    protected function ensureHives(Farm $farm)
    {
        $hives = Hive::where('farm_id', $farm->id)->orderBy('id')->take(3)->get();

        $orientations = ['East', 'North', 'East'];
        $types        = ['Kenya Top Bar', 'Kenya Top Bar', 'Langstroth'];

        while ($hives->count() < 3) {
            $i = $hives->count();
            $hives->push(Hive::create([
                'farm_id'              => $farm->id,
                'latitude'             => (string) (0.3476 + $i * 0.0002),
                'longitude'            => (string) (32.5825 + $i * 0.0002),
                'connected'            => true,
                'colonized'            => true,
                'entrance_orientation' => $orientations[$i],
                'hive_type'            => $types[$i],
                'colony_epoch'         => 1,
            ]));
        }

        // backfill the analysis metadata on hives that already existed
        foreach ($hives as $i => $hive) {
            $hive->entrance_orientation = $hive->entrance_orientation ?: $orientations[$i];
            $hive->hive_type            = $hive->hive_type ?: $types[$i];
            $hive->colony_epoch         = $hive->colony_epoch ?: 1;
            $hive->save();
        }

        return $hives->values();
    }

    /**
     * Drop anything a previous run of this seeder left behind, so it can be
     * run repeatedly without stacking duplicates.
     */
    protected function clearPrevious($hives)
    {
        $hiveIds = $hives->pluck('id');

        $videoIds = DB::table('hive_videos')
            ->whereIn('hive_id', $hiveIds)
            ->where('path', 'like', 'hivevideo/%')
            ->pluck('id');

        if ($videoIds->count()) {
            DB::table('bee_counts')->whereIn('hive_video_id', $videoIds)->delete();
            DB::table('hive_videos')->whereIn('id', $videoIds)->delete();
        }

        DB::table('hive_daily_features')->whereIn('hive_id', $hiveIds)->delete();
        DB::table('hive_baselines')->whereIn('hive_id', $hiveIds)->delete();
        DB::table('hive_insights')->whereIn('hive_id', $hiveIds)->delete();
        DB::table('hive_classifications')->whereIn('hive_id', $hiveIds)->delete();
    }
}
