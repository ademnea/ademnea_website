<?php

namespace App\Services\Analysis;

use App\Models\Hive;
use App\Models\HiveBaseline;
use App\Models\HiveClassification;
use App\Models\HiveDailyFeature;
use App\Models\HiveInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Turns raw bee counts into daily features, classifications and insights.
 *
 * Runs in five passes, each depending only on the one before it:
 *
 *   1. aggregate   bee_counts -> one row per hive per day
 *   2. baselines   what "normal" is for each hive
 *   3. compare     deviation from own baseline, and from apiary peers
 *   4. detect      level shifts, slow declines, isolated spikes
 *   5. conclude    the reconciliation ladder, then classification
 *
 * Counts only. No weather source and no in-hive sensors are used, so the
 * conditions factor sits at 1.0 and the apiary peers carry the whole job of
 * separating site-wide causes from hive-specific ones. Where a farm has fewer
 * than three reporting hives that separation is not possible, and the mode is
 * recorded on every row so the interface can say so.
 */
class CountAnalysisService
{
    /** @var array */
    protected $cfg;

    public function __construct()
    {
        $this->cfg = config('beeanalysis');
    }

    /* ===================================================================
     |  Entry point
     * =================================================================== */

    /**
     * Run the full analysis for one farm over a date range.
     *
     * @param  int          $farmId
     * @param  string|null  $from
     * @param  string|null  $to
     * @return array  counts of what was written
     */
    public function runForFarm($farmId, $from = null, $to = null)
    {
        $to   = $to ? Carbon::parse($to)->startOfDay() : Carbon::today();
        $from = $from
            ? Carbon::parse($from)->startOfDay()
            : (clone $to)->subDays($this->cfg['rolling_window_days'] * 2);

        $hives = Hive::where('farm_id', $farmId)->get();
        if ($hives->isEmpty()) {
            return ['hives' => 0, 'features' => 0, 'insights' => 0];
        }

        // 1 - aggregate
        $written = 0;
        foreach ($hives as $hive) {
            $written += $this->buildDailyRows($hive, $from, $to);
        }

        // 2 - baselines
        foreach ($hives as $hive) {
            $this->buildBaselines($hive, $to);
        }

        // 3 - compare (own baseline, then apiary peers)
        foreach ($hives as $hive) {
            $this->applyBaseline($hive, $from, $to);
        }
        $this->applyApiaryComparison($farmId, $from, $to);

        // 4 - detect
        foreach ($hives as $hive) {
            $this->applyDetectors($hive, $from, $to);
        }

        // 5 - conclude
        $insights = 0;
        foreach ($hives as $hive) {
            $insights += $this->buildInsights($hive, $farmId, $from, $to);
            $this->classify($hive, $farmId, $to);
        }

        return ['hives' => $hives->count(), 'features' => $written, 'insights' => $insights];
    }

    /* ===================================================================
     |  1 - aggregate bee_counts into one row per hive per day
     * =================================================================== */

    /**
     * bee_counts carries no hive_id of its own - it reaches a hive only
     * through hive_videos - so this joins through that table.
     *
     * The clip's own capture time is in the filename. hive_videos.created_at
     * is the insert time, which lags it, so the filename is preferred and
     * created_at is only a fallback. Every hour-of-day feature depends on
     * getting this right.
     */
    protected function buildDailyRows(Hive $hive, Carbon $from, Carbon $to)
    {
        $rows = DB::table('bee_counts as bc')
            ->join('hive_videos as hv', 'hv.id', '=', 'bc.hive_video_id')
            ->where('hv.hive_id', $hive->id)
            ->where('bc.processing_status', 'done')
            ->whereBetween('hv.created_at', [
                (clone $from)->subDay()->startOfDay(),
                (clone $to)->addDay()->endOfDay(),
            ])
            ->select(
                'bc.mean_count',
                'bc.max_count',
                'bc.activity_fraction',
                'bc.frames_analyzed',
                'bc.model_version',
                'hv.path',
                'hv.created_at'
            )
            ->orderBy('hv.created_at')
            ->get();

        if ($rows->isEmpty()) {
            return 0;
        }

        $byDay = [];
        foreach ($rows as $r) {
            $ts = $this->captureTime($r->path, $r->created_at);
            $day = $ts->toDateString();

            if (!isset($byDay[$day])) {
                $byDay[$day] = [
                    'valid' => [],
                    'hours' => [],
                    'low' => 0,
                    'none' => 0,
                    'total_rows' => 0,
                    'model' => $r->model_version,
                ];
            }

            $byDay[$day]['total_rows']++;

            if ($r->mean_count === null) {
                $byDay[$day]['none']++;
                continue;
            }
            if ((int) $r->frames_analyzed < $this->cfg['min_frames']) {
                $byDay[$day]['low']++;
                continue;
            }

            $byDay[$day]['valid'][] = [
                'mean'     => (float) $r->mean_count,
                'max'      => (float) $r->max_count,
                'fraction' => $r->activity_fraction === null ? null : (float) $r->activity_fraction,
                'hour'     => (int) $ts->format('G'),
            ];
            $byDay[$day]['hours'][] = (int) $ts->format('G');
        }

        // How many captures a full day looks like for this hive. There is no
        // declared schedule, so it is taken from the busiest day in range.
        $expected = 1;
        foreach ($byDay as $d) {
            $expected = max($expected, count($d['valid']) + $d['low'] + $d['none']);
        }

        $written = 0;
        foreach ($byDay as $day => $d) {
            $means = array_column($d['valid'], 'mean');
            $n     = count($means);

            $peakHour = null;
            $first = $last = null;
            if ($n > 0) {
                $idx = array_keys($means, max($means));
                $peakHour = $d['valid'][$idx[0]]['hour'];
                $first = min($d['hours']);
                $last  = max($d['hours']);
            }

            HiveDailyFeature::updateOrCreate(
                ['hive_id' => $hive->id, 'date' => $day],
                [
                    'farm_id'                => $hive->farm_id,
                    'colony_epoch'           => $hive->colony_epoch ?: 1,
                    'total'                  => $n ? array_sum($means) : null,
                    'peak'                   => $n ? max($means) : null,
                    'peak_hour'              => $peakHour,
                    'mean_of_means'          => $n ? array_sum($means) / $n : null,
                    'max_of_maxes'           => $n ? max(array_column($d['valid'], 'max')) : null,
                    'mean_activity_fraction' => $this->meanOf(array_filter(
                        array_column($d['valid'], 'fraction'),
                        function ($v) {
                            return $v !== null;
                        }
                    )),
                    'first_active_hour'      => $first,
                    'last_active_hour'       => $last,
                    'within_day_sd'          => $this->sd($means),
                    'n_usable'               => $n,
                    'n_expected'             => $expected,
                    'completeness'           => $expected ? round($n / $expected, 4) : 0,
                    'n_low_confidence'       => $d['low'],
                    'n_no_observation'       => $d['none'],
                    'conditions_factor'      => 1.0,
                    'analysis_mode'          => 'solo',       // revised in pass 3
                    'conditions_source'      => 'none',
                    'model_version'          => $d['model'],
                    'threshold_version'      => $this->cfg['threshold_version'],
                ]
            );
            $written++;
        }

        return $written;
    }

    /**
     * Capture time from the clip filename, e.g. 9_2026-05-17_232640.mp4.
     * Falls back to the row's created_at when the name does not match.
     */
    protected function captureTime($path, $createdAt)
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2})[_\- ](\d{2})(\d{2})(\d{2})/', (string) $path, $m)) {
            try {
                return Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $m[1] . ' ' . $m[2] . ':' . $m[3] . ':' . $m[4]
                );
            } catch (\Exception $e) {
                // fall through
            }
        }

        return Carbon::parse($createdAt);
    }

    /* ===================================================================
     |  2 - baselines
     * =================================================================== */

    /**
     * Normal is a curve, not a number. Two baselines are kept per hive:
     * 'initial' is the first week of a colony epoch and stays frozen, so a
     * hive can be compared against how it began; 'rolling' is a trailing
     * window recomputed each run, which absorbs seasonal drift and is what
     * the detectors use.
     */
    protected function buildBaselines(Hive $hive, Carbon $to)
    {
        $epoch = $hive->colony_epoch ?: 1;

        $usable = HiveDailyFeature::where('hive_id', $hive->id)
            ->where('colony_epoch', $epoch)
            ->where('completeness', '>=', $this->cfg['min_completeness'])
            ->whereNotNull('total')
            ->orderBy('date')
            ->get();

        if ($usable->count() < $this->cfg['min_baseline_days']) {
            return;
        }

        // initial - first N valid days, written once then left alone
        $existing = HiveBaseline::where('hive_id', $hive->id)
            ->where('colony_epoch', $epoch)
            ->where('baseline_type', 'initial')
            ->first();

        if (!$existing) {
            $initial = $usable->take($this->cfg['initial_window_days']);
            if ($initial->count() >= $this->cfg['min_baseline_days']) {
                $this->writeBaseline($hive, $epoch, 'initial', $initial);
            }
        }

        // rolling - trailing window
        $cut = (clone $to)->subDays($this->cfg['rolling_window_days']);
        $rolling = $usable->filter(function ($r) use ($cut) {
            return Carbon::parse($r->date)->greaterThanOrEqualTo($cut);
        });

        if ($rolling->count() >= $this->cfg['min_baseline_days']) {
            $this->writeBaseline($hive, $epoch, 'rolling', $rolling);
        }
    }

    protected function writeBaseline(Hive $hive, $epoch, $type, $rows)
    {
        $totals = $rows->pluck('total')->filter()->values()->all();
        $base   = $this->median($totals);

        // spread of log(observed / expected) - the scale every z is measured in
        $logs = [];
        foreach ($totals as $t) {
            if ($base > 0 && $t > 0) {
                $logs[] = log($t / $base);
            }
        }
        $residualSd = $this->sd($logs);
        if (!$residualSd || $residualSd < 0.05) {
            $residualSd = 0.05;   // floor, so a freakishly steady week cannot make every day an outlier
        }

        $shares = $rows->pluck('share')->filter()->values()->all();

        HiveBaseline::updateOrCreate(
            ['hive_id' => $hive->id, 'colony_epoch' => $epoch, 'baseline_type' => $type],
            [
                'window_start'   => $rows->first()->date,
                'window_end'     => $rows->last()->date,
                'days_used'      => $rows->count(),
                'base_daily'     => $base,
                'base_daily_sd'  => $this->sd($totals),
                'base_share'     => count($shares) ? $this->median($shares) : null,
                'residual_sd'    => $residualSd,
                'hourly_profile' => $this->hourlyProfile($hive, $rows),
                'model_version'  => $rows->last()->model_version,
            ]
        );
    }

    /**
     * Median, 25th and 75th percentile of mean_count at each capture hour.
     * Three bees at 06:00 is not three bees at 13:00, so a single threshold
     * on raw counts would be wrong by construction.
     */
    protected function hourlyProfile(Hive $hive, $rows)
    {
        $start = $rows->first()->date;
        $end   = $rows->last()->date;

        $raw = DB::table('bee_counts as bc')
            ->join('hive_videos as hv', 'hv.id', '=', 'bc.hive_video_id')
            ->where('hv.hive_id', $hive->id)
            ->where('bc.processing_status', 'done')
            ->where('bc.frames_analyzed', '>=', $this->cfg['min_frames'])
            ->whereNotNull('bc.mean_count')
            ->whereBetween('hv.created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ])
            ->select('bc.mean_count', 'hv.path', 'hv.created_at')
            ->get();

        $buckets = [];
        foreach ($raw as $r) {
            $h = (int) $this->captureTime($r->path, $r->created_at)->format('G');
            $buckets[$h][] = (float) $r->mean_count;
        }

        $profile = [];
        foreach ($buckets as $h => $vals) {
            sort($vals);
            $profile[(string) $h] = [
                'median' => round($this->median($vals), 3),
                'q25'    => round($this->percentile($vals, 0.25), 3),
                'q75'    => round($this->percentile($vals, 0.75), 3),
                'n'      => count($vals),
            ];
        }
        ksort($profile);

        return $profile;
    }

    /* ===================================================================
     |  3 - compare
     * =================================================================== */

    /**
     * Deviation from the hive's own rolling baseline.
     *
     * conditions_factor is 1.0 throughout: with no weather source and no
     * exterior sensor reading, there is nothing to scale the expectation by.
     * When one arrives, only this method changes.
     */
    protected function applyBaseline(Hive $hive, Carbon $from, Carbon $to)
    {
        $baseline = HiveBaseline::where('hive_id', $hive->id)
            ->where('colony_epoch', $hive->colony_epoch ?: 1)
            ->rolling()
            ->first();

        if (!$baseline || !$baseline->base_daily) {
            return;
        }

        $rows = HiveDailyFeature::where('hive_id', $hive->id)
            ->betweenDates($from->toDateString(), $to->toDateString())
            ->get();

        foreach ($rows as $row) {
            $factor   = 1.0;
            $expected = $baseline->base_daily * $factor;

            $ratio = ($expected > 0 && $row->total !== null) ? $row->total / $expected : null;
            $z = null;
            if ($ratio !== null && $baseline->residual_sd > 0) {
                $z = log(max($ratio, 0.01)) / $baseline->residual_sd;
            }

            $row->conditions_factor = $factor;
            $row->conditions_source = 'none';
            $row->expected_total    = $expected;
            $row->ratio             = $ratio;
            $row->z                 = $z;
            $row->save();
        }
    }

    /**
     * Compare each hive to its neighbours on the same day.
     *
     * Hives at one site share weather, forage and season, so a hive diverging
     * from its peers is diverging for a hive-specific reason. This is the only
     * control available without conditions data, which is why the mode is
     * recorded: below three reporting hives there are no usable peers and a
     * site-wide cause cannot be ruled out.
     */
    protected function applyApiaryComparison($farmId, Carbon $from, Carbon $to)
    {
        $days = HiveDailyFeature::where('farm_id', $farmId)
            ->betweenDates($from->toDateString(), $to->toDateString())
            ->conclusive($this->cfg['min_completeness'])
            ->whereNotNull('total')
            ->get()
            ->groupBy(function ($r) {
                return Carbon::parse($r->date)->toDateString();
            });

        foreach ($days as $date => $rows) {
            $totals = $rows->pluck('total')->filter()->values()->all();
            if (!count($totals)) {
                continue;
            }

            $sum    = array_sum($totals);
            $median = $this->median($totals);
            $count  = count($totals);
            $mode   = $count >= $this->cfg['min_peers_for_mode_b'] ? 'peer_compared' : 'solo';

            foreach ($rows as $row) {
                $row->apiary_total      = $sum;
                $row->apiary_median     = $median;
                $row->apiary_hive_count = $count;
                $row->share             = $sum > 0 ? $row->total / $sum : null;
                $row->vs_median         = $median > 0 ? $row->total / $median : null;
                $row->analysis_mode     = $mode;
                $row->save();
            }
        }
    }

    /* ===================================================================
     |  4 - detect
     * =================================================================== */

    /**
     * Three shapes of change, three tests.
     *
     * A level shift and a slow decline both eventually push z below the
     * threshold. They are separated by dz, the day-over-day change: a shift
     * jumps several units in a day, a drift creeps. Without that test a
     * gradual decline is mislabelled as an abrupt one and the farmer is told
     * the wrong thing about urgency.
     */
    protected function applyDetectors(Hive $hive, Carbon $from, Carbon $to)
    {
        $rows = HiveDailyFeature::where('hive_id', $hive->id)
            ->betweenDates(
                (clone $from)->subDays($this->cfg['slope_window_days'])->toDateString(),
                $to->toDateString()
            )
            ->orderBy('date')
            ->get()
            ->values();

        if ($rows->isEmpty()) {
            return;
        }

        // rolling slope of log(ratio) - fractional change per day
        $win = $this->cfg['slope_window_days'];
        foreach ($rows as $i => $row) {
            $slice = $rows->slice(max(0, $i - $win + 1), min($i + 1, $win))
                ->filter(function ($r) {
                    return $r->ratio !== null && $r->ratio > 0;
                })
                ->values();

            $row->slope10 = $slice->count() >= $win - 2
                ? $this->slope(
                    $slice->map(function ($r) {
                        return Carbon::parse($r->date)->timestamp / 86400;
                    })->all(),
                    $slice->map(function ($r) {
                        return log($r->ratio);
                    })->all()
                )
                : null;
            $row->save();
        }

        // level shift - a run of low days that began with a step
        $run = 0;
        $onset = null;
        $prevZ = null;
        foreach ($rows as $row) {
            $z = $row->z;
            $dz = ($z !== null && $prevZ !== null) ? $z - $prevZ : null;

            if ($z !== null && $z <= $this->cfg['z_sudden']) {
                $run++;
            } else {
                $run = 0;
            }

            if (
                $onset === null
                && $run >= $this->cfg['persist_days']
                && $dz !== null && $dz <= $this->cfg['step_dz']
            ) {
                $onset = $row->date;
            }
            // a step one or two days before the run also qualifies
            if (
                $onset === null && $run >= $this->cfg['persist_days']
                && $prevZ !== null && $prevZ <= $this->cfg['z_sudden'] * 1.5
            ) {
                $onset = $row->date;
            }

            $prevZ = $z;
        }

        $this->sudden[$hive->id] = $onset;
    }

    /** @var array  hive_id => date the level shift began, or null */
    protected $sudden = [];

    /* ===================================================================
     |  5 - conclude
     * =================================================================== */

    /**
     * The reconciliation ladder. Evaluated in order, first match wins.
     *
     *   1. is the data valid and complete?   no  -> monitor_offline / insufficient_data
     *   2. are all the hives moving together? yes -> apiary_wide
     *   3. hive-specific and unexplained      -> an alert
     *
     * The weather step that would normally sit between 2 and 3 is absent,
     * because no conditions data is available. That is the single biggest
     * gap in this version: with fewer than three reporting hives, a wet day
     * and a sick colony look the same.
     */
    protected function buildInsights(Hive $hive, $farmId, Carbon $from, Carbon $to)
    {
        $rows = HiveDailyFeature::where('hive_id', $hive->id)
            ->betweenDates($from->toDateString(), $to->toDateString())
            ->orderBy('date')
            ->get();

        $onset = isset($this->sudden[$hive->id]) ? $this->sudden[$hive->id] : null;
        $written = 0;

        foreach ($rows as $row) {
            $date = Carbon::parse($row->date)->toDateString();

            $peers = HiveDailyFeature::where('farm_id', $farmId)
                ->where('hive_id', '!=', $hive->id)
                ->whereDate('date', $date)
                ->conclusive($this->cfg['min_completeness'])
                ->get();

            list($verdict, $evidence, $escalate) =
                $this->reconcile($row, $peers, $onset, $date);

            $meta = $this->verdictMeta($verdict, $row);
            if ($meta === null) {
                continue;   // 'normal' - nothing to report
            }

            HiveInsight::updateOrCreate(
                ['hive_id' => $hive->id, 'date' => $date, 'verdict' => $verdict],
                [
                    'farm_id'           => $farmId,
                    'confidence'        => $meta['confidence'],
                    'message'           => $meta['message'],
                    'action'            => $meta['action'],
                    'evidence'          => $evidence,
                    'escalate'          => $escalate,
                    'threshold_version' => $this->cfg['threshold_version'],
                ]
            );
            $written++;
        }

        return $written;
    }

    protected function reconcile($row, $peers, $onset, $date)
    {
        // 1 - guards
        if ($row->n_usable == 0) {
            return ['monitor_offline', 'no usable captures arrived for this day', false];
        }
        if ($row->completeness < $this->cfg['min_completeness']) {
            return [
                'insufficient_data',
                "only {$row->n_usable} of {$row->n_expected} captures usable",
                false
            ];
        }
        if ($row->z === null) {
            return ['insufficient_data', 'hive is not yet characterised', false];
        }

        // 2 - is the whole site moving together?
        $lowPeers = $peers->filter(function ($p) {
            return $p->z !== null && $p->z <= $this->cfg['z_sudden'];
        });
        $peerCount = $peers->count();

        if (
            $peerCount >= $this->cfg['min_peers_for_mode_b'] - 1
            && $peerCount > 0
            && $lowPeers->count() === $peerCount
            && $row->z <= $this->cfg['z_sudden']
        ) {
            return ['apiary_wide', 'every reporting hive at this site is low together', false];
        }

        $somePeerFine = $peers->first(function ($p) {
            return $p->z !== null && $p->z > $this->cfg['z_sudden'];
        }) !== null;

        $modeB   = $row->analysis_mode === 'peer_compared';
        $complete = $row->completeness >= $this->cfg['complete_day'];

        // 3 - hive-specific
        if ($onset && $date >= Carbon::parse($onset)->toDateString() && $row->z <= -2.0) {
            return [
                'hive_anomaly_sudden',
                sprintf('abrupt sustained drop (z=%.1f)', $row->z),
                $modeB && $complete && $somePeerFine
            ];
        }

        if ($row->slope10 !== null && $row->slope10 <= $this->cfg['trend_pct_per_day']) {
            return [
                'hive_trend_decline',
                sprintf('falling %.1f%% per day', abs($row->slope10) * 100),
                $modeB && $complete && $somePeerFine
            ];
        }

        if ($row->z >= $this->cfg['z_spike']) {
            return [
                'hive_spike',
                sprintf('isolated high reading (z=%.1f)', $row->z),
                false
            ];
        }

        return ['normal', 'within the expected range for this hive', false];
    }

    /**
     * Wording. Each message states what was observed and each action what to
     * do about it. None of them names a cause: counts cannot tell absconding
     * from theft from colony death, and claiming otherwise is how a system
     * loses a farmer's trust.
     */
    protected function verdictMeta($verdict, $row)
    {
        $solo = $row->analysis_mode !== 'peer_compared';

        switch ($verdict) {
            case 'monitor_offline':
                return [
                    'confidence' => 'high',
                    'message'    => 'No usable video arrived from this hive. This says nothing about the colony.',
                    'action'     => 'Check the monitor at this hive',
                ];

            case 'insufficient_data':
                return [
                    'confidence' => 'low',
                    'message'    => 'Too few usable captures to draw a conclusion for this day.',
                    'action'     => 'No conclusion drawn',
                ];

            case 'hive_anomaly_sudden':
                return [
                    'confidence' => $solo ? 'medium' : 'high',
                    'message'    => 'Activity dropped sharply and has stayed low.'
                        . ($solo
                            ? ' Too few other hives are reporting here to rule out a site-wide cause.'
                            : ' The other hives at this site are unaffected.'),
                    'action'     => 'Inspect this hive within 48 hours',
                ];

            case 'hive_trend_decline':
                return [
                    'confidence' => $solo ? 'low' : 'medium',
                    'message'    => 'Activity has been falling steadily for over a week.'
                        . ($solo ? ' No peer hives are available for comparison.' : ''),
                    'action'     => 'Schedule an inspection this week',
                ];

            case 'hive_spike':
                return [
                    'confidence' => 'low',
                    'message'    => 'An unusually high reading was recorded. Often orientation flights, '
                        . 'which are normal and commonly follow a spell of poor weather.',
                    'action'     => 'No action, note only',
                ];

            case 'apiary_wide':
                return [
                    'confidence' => 'high',
                    'message'    => 'All reporting hives at this site are low together, which points to '
                        . 'conditions at the site rather than to any one colony.',
                    'action'     => 'No action',
                ];
        }

        return null;   // 'normal'
    }

    /* ===================================================================
     |  Classification
     * =================================================================== */

    /**
     * Two independent axes.
     *
     * Size is a level, taken from the baseline relative to apiary peers.
     * Condition is a trajectory, taken from the recent trend. Keeping them
     * apart matters: a small hive can be perfectly healthy and a large one
     * can be failing, and conflating them produces the wrong action.
     */
    protected function classify(Hive $hive, $farmId, Carbon $to)
    {
        $epoch = $hive->colony_epoch ?: 1;

        $baseline = HiveBaseline::where('hive_id', $hive->id)
            ->where('colony_epoch', $epoch)->rolling()->first();

        $window = HiveDailyFeature::where('hive_id', $hive->id)
            ->betweenDates(
                (clone $to)->subDays($this->cfg['condition_window_days'])->toDateString(),
                $to->toDateString()
            )
            ->conclusive($this->cfg['min_completeness'])
            ->orderBy('date')
            ->get();

        // ---- size -------------------------------------------------------
        $sizeClass = 'unclassified';
        $sizeRatio = null;

        $peerBases = HiveBaseline::whereIn(
            'hive_id',
            Hive::where('farm_id', $farmId)->pluck('id')
        )
            ->rolling()
            ->whereNotNull('base_daily')
            ->pluck('base_daily')
            ->all();

        if (
            $baseline && $baseline->base_daily
            && count($peerBases) >= $this->cfg['min_peers_for_mode_b']
        ) {
            $apiaryMedian = $this->median($peerBases);
            if ($apiaryMedian > 0) {
                $sizeRatio = $baseline->base_daily / $apiaryMedian;
                if ($sizeRatio >= $this->cfg['size_strong'])      $sizeClass = 'strong';
                elseif ($sizeRatio >= $this->cfg['size_average']) $sizeClass = 'average';
                elseif ($sizeRatio >= $this->cfg['size_weak'])    $sizeClass = 'weak';
                else                                              $sizeClass = 'very_weak';
            }
        }

        // ---- condition --------------------------------------------------
        $condition = 'unknown';
        $trend = null;
        $pctOfBase = null;

        if ($window->count() >= $this->cfg['min_baseline_days'] && $baseline && $baseline->base_daily) {
            $recent = $window->slice(-5)->pluck('total')->filter()->all();
            $pctOfBase = count($recent)
                ? $this->median($recent) / $baseline->base_daily
                : null;

            $trend = $window->last()->slope10;

            $zeroDays = $window->slice(-$this->cfg['silent_days'])
                ->filter(function ($r) {
                    return (float) $r->total === 0.0;
                })->count();

            $criticalDays = $window->slice(-$this->cfg['critical_days'])
                ->filter(function ($r) use ($baseline) {
                    return $baseline->base_daily > 0
                        && ($r->total / $baseline->base_daily) <= $this->cfg['critical_pct'];
                })->count();

            if ($zeroDays >= $this->cfg['silent_days']) {
                $condition = 'silent';
            } elseif ($criticalDays >= $this->cfg['critical_days']) {
                $condition = 'critical';
            } elseif ($trend !== null && $trend <= $this->cfg['trend_pct_per_day']) {
                $condition = 'declining';
            } elseif ($trend !== null && $trend >= $this->cfg['establishing_pct']) {
                $condition = 'establishing';
            } else {
                $condition = 'stable';
            }
        }

        // ---- write, only when something changed -------------------------
        $current = HiveClassification::where('hive_id', $hive->id)->current()->first();

        if (
            $current
            && $current->size_class === $sizeClass
            && $current->condition_class === $condition
        ) {
            $current->size_ratio        = $sizeRatio;
            $current->trend_pct_per_day = $trend;
            $current->pct_of_baseline   = $pctOfBase;
            $current->save();
            return;
        }

        if ($current) {
            $current->effective_to = (clone $to)->subDay()->toDateString();
            $current->save();
        }

        HiveClassification::create([
            'hive_id'           => $hive->id,
            'colony_epoch'      => $epoch,
            'size_class'        => $sizeClass,
            'condition_class'   => $condition,
            'size_ratio'        => $sizeRatio,
            'trend_pct_per_day' => $trend,
            'pct_of_baseline'   => $pctOfBase,
            'effective_from'    => $to->toDateString(),
            'evidence'          => sprintf(
                'baseline %.1f, apiary ratio %s, trend %s',
                $baseline && $baseline->base_daily ? $baseline->base_daily : 0,
                $sizeRatio === null ? 'n/a' : round($sizeRatio, 2),
                $trend === null ? 'n/a' : round($trend * 100, 2) . '%/day'
            ),
            'threshold_version' => $this->cfg['threshold_version'],
        ]);
    }

    /* ===================================================================
     |  Small statistics helpers
     * =================================================================== */

    protected function median(array $v)
    {
        $v = array_values(array_filter($v, function ($x) {
            return $x !== null;
        }));
        if (!count($v)) return 0.0;
        sort($v);
        $n = count($v);
        $m = intdiv($n, 2);
        return $n % 2 ? (float) $v[$m] : ($v[$m - 1] + $v[$m]) / 2;
    }

    protected function percentile(array $v, $p)
    {
        if (!count($v)) return 0.0;
        sort($v);
        $i = ($this->countOf($v) - 1) * $p;
        $lo = (int) floor($i);
        $hi = (int) ceil($i);
        return $lo === $hi ? (float) $v[$lo] : $v[$lo] + ($i - $lo) * ($v[$hi] - $v[$lo]);
    }

    protected function countOf(array $v)
    {
        return count($v);
    }

    protected function meanOf($v)
    {
        $v = array_values((array) $v);
        return count($v) ? array_sum($v) / count($v) : null;
    }

    protected function sd(array $v)
    {
        $n = count($v);
        if ($n < 2) return null;
        $mean = array_sum($v) / $n;
        $sum = 0.0;
        foreach ($v as $x) {
            $sum += pow($x - $mean, 2);
        }
        return sqrt($sum / ($n - 1));
    }

    /** Ordinary least squares slope of y on x. */
    protected function slope(array $x, array $y)
    {
        $n = count($x);
        if ($n < 2) return null;
        $mx = array_sum($x) / $n;
        $my = array_sum($y) / $n;
        $num = $den = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $num += ($x[$i] - $mx) * ($y[$i] - $my);
            $den += pow($x[$i] - $mx, 2);
        }
        return $den > 0 ? $num / $den : null;
    }
}
