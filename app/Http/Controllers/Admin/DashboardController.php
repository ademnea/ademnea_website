<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hive;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $totalHives = Hive::count();

        [$hiveHealth, $dataTypeKeys, $staleThresholdHours, $staleHiveCount, $noDataHiveCount, $normalHiveCount] = $this->buildHiveHealth();

        return view('admin.dashboard.index', compact(
            'totalHives',
            'hiveHealth', 'dataTypeKeys', 'staleThresholdHours', 'staleHiveCount', 'noDataHiveCount', 'normalHiveCount'
        ));
    }

    /**
     * For every hive, work out how long ago each data type last reported
     * and flag anything older than the staleness threshold.
     */
    private function buildHiveHealth()
    {
        $dataTypes = [
            'temperature' => 'hive_temperatures',
            'humidity' => 'hive_humidity',
            'carbondioxide' => 'hive_carbondioxide',
            'weight' => 'hive_weights',
            'voc' => 'hive_vocs',
            'photos' => 'hive_photos',
            'videos' => 'hive_videos',
            'audios' => 'hive_audios',
        ];

        $staleThresholdHours = 48;

        $latestByType = [];
        foreach ($dataTypes as $key => $table) {
            $latestByType[$key] = DB::table($table)
                ->select('hive_id', DB::raw('MAX(created_at) as latest'))
                ->groupBy('hive_id')
                ->pluck('latest', 'hive_id');
        }

        $hives = DB::table('hives')
            ->leftJoin('farms', 'hives.farm_id', '=', 'farms.id')
            ->select('hives.id as hive_id', 'farms.name as farm_name')
            ->orderBy('hives.id')
            ->get();

        $now = now();
        $hiveHealth = [];
        $staleHiveCount = 0;
        $noDataHiveCount = 0;
        $normalHiveCount = 0;

        foreach ($hives as $hive) {
            $row = [
                'hive_id' => $hive->hive_id,
                'farm_name' => $hive->farm_name ?? '—',
                'types' => [],
            ];

            $hasStale = false;
            $hasAnyData = false;

            foreach ($dataTypes as $key => $table) {
                $latest = $latestByType[$key][$hive->hive_id] ?? null;

                if (!$latest) {
                    $status = 'none';
                    $ago = null;
                } else {
                    $hasAnyData = true;
                    $latestAt = Carbon::parse($latest);
                    $status = $latestAt->diffInHours($now) > $staleThresholdHours ? 'stale' : 'fresh';
                    $ago = $latestAt->diffForHumans();

                    if ($status === 'stale') {
                        $hasStale = true;
                    }
                }

                $row['types'][$key] = ['status' => $status, 'ago' => $ago];
            }

            if (!$hasAnyData) {
                $noDataHiveCount++;
            } elseif ($hasStale) {
                $staleHiveCount++;
            } else {
                $normalHiveCount++;
            }

            $hiveHealth[] = $row;
        }

        return [$hiveHealth, array_keys($dataTypes), $staleThresholdHours, $staleHiveCount, $noDataHiveCount, $normalHiveCount];
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
