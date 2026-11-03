<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Hive;
use App\Models\HiveBaseline;
use App\Models\HiveClassification;
use App\Models\HiveDailyFeature;
use App\Models\HiveInsight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * The analysis pages. Reads only from the tables the nightly command writes -
 * nothing is computed here, so a page load never triggers analysis.
 *
 * Follows the pattern of HiveDataController: a default() method returns the
 * view, and a matching data() method returns JSON for the ECharts widgets.
 */
class HiveAnalysisController extends Controller
{
    /**
     * Landing page - every farm with its hive count and open insights.
     */
    public function index()
    {
        $farms = Farm::withCount('hives')->orderBy('name')->get();

        $open = HiveInsight::reportable()->open()
            ->select('farm_id')
            ->selectRaw('COUNT(*) as n')
            ->groupBy('farm_id')
            ->pluck('n', 'farm_id');

        return view('admin.hiveanalysis.index', [
            'farms'    => $farms,
            'openById' => $open,
        ]);
    }

    /**
     * Apiary view - all hives in one farm side by side.
     */
    public function farm($farmId, Request $request)
    {
        $farm  = Farm::findOrFail($farmId);
        $asOf  = $request->query('as_of')
            ? Carbon::parse($request->query('as_of'))
            : Carbon::today();

        $hives = Hive::where('farm_id', $farmId)->orderBy('id')->get();

        $classes = HiveClassification::whereIn('hive_id', $hives->pluck('id'))
            ->current()->get()->keyBy('hive_id');

        $insights = HiveInsight::where('farm_id', $farmId)
            ->reportable()
            ->whereDate('date', '<=', $asOf->toDateString())
            ->whereDate('date', '>=', (clone $asOf)->subDays(7)->toDateString())
            ->orderByDesc('date')
            ->get();

        return view('admin.hiveanalysis.farm', [
            'farm'      => $farm,
            'farm_name' => $farm->name,
            'hives'     => $hives,
            'classes'   => $classes,
            'insights'  => $insights,
            'as_of'     => $asOf->toDateString(),
        ]);
    }

    /**
     * JSON for the apiary charts.
     */
    public function farmData($farmId, Request $request)
    {
        list($start, $end) = $this->range($request);

        $rows = HiveDailyFeature::where('farm_id', $farmId)
            ->betweenDates($start, $end)
            ->orderBy('date')
            ->get();

        $dates = $rows->pluck('date')
            ->map(function ($d) {
                return Carbon::parse($d)->toDateString();
            })
            ->unique()->values();

        $series = [];
        foreach ($rows->groupBy('hive_id') as $hiveId => $hiveRows) {
            $byDate = $hiveRows->keyBy(function ($r) {
                return Carbon::parse($r->date)->toDateString();
            });

            $series[] = [
                'hive_id'   => $hiveId,
                'total'     => $dates->map(function ($d) use ($byDate) {
                    return isset($byDate[$d]) ? $this->round($byDate[$d]->total) : null;
                }),
                'share'     => $dates->map(function ($d) use ($byDate) {
                    return isset($byDate[$d]) ? $this->round($byDate[$d]->share, 4) : null;
                }),
                'z'         => $dates->map(function ($d) use ($byDate) {
                    return isset($byDate[$d]) ? $this->round($byDate[$d]->z, 2) : null;
                }),
            ];
        }

        return response()->json([
            'dates'  => $dates,
            'series' => $series,
            'mode'   => optional($rows->last())->analysis_mode ?: 'solo',
        ]);
    }

    /**
     * Single-hive view.
     */
    public function hive($hiveId, Request $request)
    {
        $hive = Hive::findOrFail($hiveId);
        $farm = Farm::find($hive->farm_id);

        $asOf = $request->query('as_of')
            ? Carbon::parse($request->query('as_of'))
            : Carbon::today();

        $baselineRolling = HiveBaseline::where('hive_id', $hiveId)->rolling()->first();
        $baselineInitial = HiveBaseline::where('hive_id', $hiveId)->initial()->first();

        $class = HiveClassification::where('hive_id', $hiveId)->current()->first();

        $insights = HiveInsight::where('hive_id', $hiveId)
            ->reportable()
            ->whereDate('date', '<=', $asOf->toDateString())
            ->orderByDesc('date')
            ->limit(25)
            ->get();

        return view('admin.hiveanalysis.hive', [
            'hive'             => $hive,
            'hive_id'          => $hive->id,
            'farm_name'        => $farm ? $farm->name : '-',
            'baselineRolling'  => $baselineRolling,
            'baselineInitial'  => $baselineInitial,
            'class'            => $class,
            'insights'         => $insights,
            'as_of'            => $asOf->toDateString(),
        ]);
    }

    /**
     * JSON for the single-hive charts: daily series plus the hourly profile.
     */
    public function hiveData($hiveId, Request $request)
    {
        list($start, $end) = $this->range($request);

        $rows = HiveDailyFeature::where('hive_id', $hiveId)
            ->betweenDates($start, $end)
            ->orderBy('date')
            ->get();

        $baseline = HiveBaseline::where('hive_id', $hiveId)->rolling()->first();

        return response()->json([
            'dates'        => $rows->map(function ($r) {
                return Carbon::parse($r->date)->toDateString();
            }),
            'total'        => $rows->map(function ($r) {
                return $this->round($r->total);
            }),
            'expected'     => $rows->map(function ($r) {
                return $this->round($r->expected_total);
            }),
            'z'            => $rows->map(function ($r) {
                return $this->round($r->z, 2);
            }),
            'slope'        => $rows->map(function ($r) {
                return $this->round($r->slope10 === null ? null : $r->slope10 * 100, 2);
            }),
            'share'        => $rows->map(function ($r) {
                return $this->round($r->share, 4);
            }),
            'completeness' => $rows->map(function ($r) {
                return $this->round($r->completeness, 2);
            }),
            'profile'      => $baseline ? $baseline->hourly_profile : null,
            'profile_mean' => $this->hourlyMeans($hiveId, $start, $end),
            'base_daily'   => $baseline ? $this->round($baseline->base_daily) : null,
        ]);
    }

    /**
     * Mean bees-per-frame at each capture hour over the selected date range,
     * straight from bee_counts. Complements the baseline's median/quartile
     * profile, which is fixed to the rolling window.
     *
     * Same row filter and same capture-time rule as the analysis service:
     * done rows with enough frames, hour taken from the clip filename.
     */
    protected function hourlyMeans($hiveId, $start, $end)
    {
        $minFrames = config('beeanalysis.min_frames', 10);

        $rows = DB::table('bee_counts as bc')
            ->join('hive_videos as hv', 'hv.id', '=', 'bc.hive_video_id')
            ->where('hv.hive_id', $hiveId)
            ->where('bc.processing_status', 'done')
            ->where('bc.frames_analyzed', '>=', $minFrames)
            ->whereNotNull('bc.mean_count')
            ->whereBetween('hv.created_at', [
                Carbon::parse($start)->subDay()->startOfDay(),
                Carbon::parse($end)->addDay()->endOfDay(),
            ])
            ->select('bc.mean_count', 'bc.max_count', 'hv.path', 'hv.created_at')
            ->get();

        $buckets = [];
        foreach ($rows as $r) {
            $ts = $this->captureTime($r->path, $r->created_at);
            if ($ts->lt(Carbon::parse($start)->startOfDay()) || $ts->gt(Carbon::parse($end)->endOfDay())) {
                continue;
            }
            $h = (int) $ts->format('G');
            $buckets[$h]['mean'][] = (float) $r->mean_count;
            $buckets[$h]['max'][]  = (float) $r->max_count;
        }

        $out = [];
        foreach ($buckets as $h => $b) {
            $n = count($b['mean']);
            $out[(string) $h] = [
                'mean'     => round(array_sum($b['mean']) / $n, 3),
                'mean_max' => round(array_sum($b['max']) / $n, 3),
                'n'        => $n,
            ];
        }
        ksort($out, SORT_NUMERIC);

        return $out ?: null;
    }

    /**
     * Capture time from the clip filename, e.g. 9_2026-05-17_232640.mp4,
     * falling back to created_at. Mirrors CountAnalysisService::captureTime.
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

    /**
     * Resolve the requested date range, defaulting to the last 30 days.
     */
    protected function range(Request $request)
    {
        $end = $request->query('end')
            ? Carbon::parse($request->query('end'))->toDateString()
            : Carbon::today()->toDateString();

        $start = $request->query('start')
            ? Carbon::parse($request->query('start'))->toDateString()
            : Carbon::parse($end)->subDays(29)->toDateString();

        return [$start, $end];
    }

    protected function round($v, $dp = 2)
    {
        return $v === null ? null : round((float) $v, $dp);
    }
}
