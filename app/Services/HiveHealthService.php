<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HiveHealthService
{
    const STALE_THRESHOLD_HOURS = 48;

    const DATA_TYPES = [
        'temperature' => 'hive_temperatures',
        'humidity' => 'hive_humidity',
        'carbondioxide' => 'hive_carbondioxide',
        'weight' => 'hive_weights',
        'voc' => 'hive_vocs',
        'photos' => 'hive_photos',
        'videos' => 'hive_videos',
        'audios' => 'hive_audios',
    ];

    const DATA_TYPE_LABELS = [
        'temperature' => 'Temp',
        'humidity' => 'Humidity',
        'carbondioxide' => 'CO2',
        'weight' => 'Weight',
        'voc' => 'VOC',
        'photos' => 'Photos',
        'videos' => 'Videos',
        'audios' => 'Audio',
    ];

    const STATUS_LABELS = [
        'all' => 'All Hives',
        'normal' => 'Reporting Normally',
        'attention' => 'Needing Attention',
        'nodata' => 'No Data At All',
    ];

    /**
     * For every hive, work out how long ago each data type last reported,
     * flag anything older than the staleness threshold, and attach the farm
     * and farmer the hive belongs to.
     *
     * Each row's 'status' is one of: normal, attention, nodata.
     */
    public function build()
    {
        $latestByType = [];
        foreach (self::DATA_TYPES as $key => $table) {
            $latestByType[$key] = DB::table($table)
                ->select('hive_id', DB::raw('MAX(created_at) as latest'))
                ->groupBy('hive_id')
                ->pluck('latest', 'hive_id');
        }

        $hives = DB::table('hives')
            ->leftJoin('farms', 'hives.farm_id', '=', 'farms.id')
            ->leftJoin('farmers', 'farms.ownerId', '=', 'farmers.id')
            ->leftJoin('users', 'farmers.user_id', '=', 'users.id')
            ->select(
                'hives.id as hive_id',
                'farms.id as farm_id',
                'farms.name as farm_name',
                'farms.district as farm_district',
                'farmers.id as farmer_id',
                'farmers.fname as farmer_fname',
                'farmers.lname as farmer_lname',
                'farmers.telephone as farmer_telephone',
                'users.email as farmer_email'
            )
            ->orderBy('hives.id')
            ->get();

        $now = now();
        $rows = [];
        $counts = ['all' => 0, 'normal' => 0, 'attention' => 0, 'nodata' => 0];

        foreach ($hives as $hive) {
            $farmerName = trim(($hive->farmer_fname ?? '') . ' ' . ($hive->farmer_lname ?? ''));

            $row = [
                'hive_id' => $hive->hive_id,
                'farm_id' => $hive->farm_id,
                'farm_name' => $hive->farm_name ?? '—',
                'farm_district' => $hive->farm_district ?? '—',
                'farmer_id' => $hive->farmer_id,
                'farmer_name' => $farmerName !== '' ? $farmerName : '—',
                'farmer_telephone' => $hive->farmer_telephone ?? '—',
                'farmer_email' => $hive->farmer_email ?? '—',
                'types' => [],
                'stale_types' => [],
            ];

            $hasAnyData = false;

            foreach (self::DATA_TYPES as $key => $table) {
                $latest = $latestByType[$key][$hive->hive_id] ?? null;

                if (!$latest) {
                    $status = 'none';
                    $ago = null;
                } else {
                    $hasAnyData = true;
                    $latestAt = Carbon::parse($latest);
                    $status = $latestAt->diffInHours($now) > self::STALE_THRESHOLD_HOURS ? 'stale' : 'fresh';
                    $ago = $latestAt->diffForHumans();

                    if ($status === 'stale') {
                        $row['stale_types'][] = ['label' => self::DATA_TYPE_LABELS[$key], 'ago' => $ago];
                    }
                }

                $row['types'][$key] = ['status' => $status, 'ago' => $ago];
            }

            if (!$hasAnyData) {
                $row['status'] = 'nodata';
            } elseif (!empty($row['stale_types'])) {
                $row['status'] = 'attention';
            } else {
                $row['status'] = 'normal';
            }

            $counts['all']++;
            $counts[$row['status']]++;
            $rows[] = $row;
        }

        return ['rows' => $rows, 'counts' => $counts];
    }
}
