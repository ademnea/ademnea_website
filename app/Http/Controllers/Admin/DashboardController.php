<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HiveHealthService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HiveHealthService $hiveHealthService)
    {
        //
        $health = $hiveHealthService->build();

        $totalHives = $health['counts']['all'];
        $hiveHealth = $health['rows'];
        $dataTypeKeys = array_keys(HiveHealthService::DATA_TYPES);
        $staleThresholdHours = HiveHealthService::STALE_THRESHOLD_HOURS;
        $staleHiveCount = $health['counts']['attention'];
        $noDataHiveCount = $health['counts']['nodata'];
        $normalHiveCount = $health['counts']['normal'];

        [$beeActivity, $beeActivityDays] = $this->buildBeeActivity();

        return view('admin.dashboard.index', compact(
            'totalHives',
            'hiveHealth', 'dataTypeKeys', 'staleThresholdHours', 'staleHiveCount', 'noDataHiveCount', 'normalHiveCount',
            'beeActivity', 'beeActivityDays'
        ));
    }

    /**
     * The hives behind one of the dashboard cards, with the farm and farmer
     * each one belongs to. ?status= all | normal | attention | nodata
     */
    public function hives(Request $request, HiveHealthService $hiveHealthService)
    {
        $status = $request->query('status', 'all');
        if (!array_key_exists($status, HiveHealthService::STATUS_LABELS)) {
            $status = 'all';
        }

        $health = $hiveHealthService->build();
        $counts = $health['counts'];
        $hives = array_values(array_filter($health['rows'], function ($row) use ($status) {
            return $status === 'all' || $row['status'] === $status;
        }));

        $statusLabels = HiveHealthService::STATUS_LABELS;
        $staleThresholdHours = HiveHealthService::STALE_THRESHOLD_HOURS;

        return view('admin.dashboard.hives', compact('hives', 'status', 'statusLabels', 'counts', 'staleThresholdHours'));
    }

    /**
     * Per-hive bee activity rolled up over the last N days, from videos the
     * detection pipeline has actually finished analysing. See
     * DASHBOARD-INTEGRATION.md §7 "Daily activity per hive" for the
     * underlying query this is based on - grouped per hive here rather than
     * per hive+day, for a dashboard-sized summary.
     */
    private function buildBeeActivity()
    {
        $days = 7;

        $rows = DB::table('hive_videos as hv')
            ->join('bee_counts as bc', function ($join) {
                $join->on('bc.hive_video_id', '=', 'hv.id')
                    ->where('bc.processing_status', '=', 'done');
            })
            ->where('bc.processed_at', '>=', now()->subDays($days))
            ->select(
                'hv.hive_id',
                DB::raw('COUNT(*) as clips_analysed'),
                DB::raw('ROUND(AVG(bc.mean_count), 2) as avg_bees'),
                DB::raw('MAX(bc.max_count) as peak_bees'),
                DB::raw('ROUND(AVG(bc.activity_fraction) * 100) as avg_activity_pct')
            )
            ->groupBy('hv.hive_id')
            ->get()
            ->keyBy('hive_id');

        return [$rows, $days];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
