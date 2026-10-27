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
            'base_daily'   => $baseline ? $this->round($baseline->base_daily) : null,
        ]);
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
