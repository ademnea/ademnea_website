<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Hive;
use App\Models\HiveAudio;
use App\Models\HivePhoto;
use App\Models\HiveVideo;

class HiveMediaDataController extends Controller
{
    /**
     * Get images for a specific date range.
     *
     * @param  Request  $request
     * @param  int  $hive_id
     * @param  string  $from_date
     * @param  string  $to_date
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get images for a specific date range.
     *
     * @param  Request  $request
     * @param  int  $hive_id
     * @param  string  $from_date
     * @param  string  $to_date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImagesForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);

        if ($hive instanceof Response) {
            return $hive;
        }

        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();

        $imageData = HivePhoto::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('path', 'created_at')
            ->paginate(10);

        $formattedData = $this->formatMediaData($imageData, 'hiveimage');

        return $formattedData;
    }

    /**
     * Get videos for a specific date range.
     *
     * @param  Request  $request
     * @param  int  $hive_id
     * @param  string  $from_date
     * @param  string  $to_date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVideosForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);

        if ($hive instanceof Response) {
            return $hive;
        }

        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();

        $videoData = HiveVideo::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('path', 'created_at')
            ->paginate(10);

        $formattedData = $this->formatMediaData($videoData, 'hivevideo');

        return $formattedData;
    }

    /**
     * Get audios for a specific date range.
     *
     * @param  Request  $request
     * @param  int  $hive_id
     * @param  string  $from_date
     * @param  string  $to_date
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAudiosForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);

        if ($hive instanceof Response) {
            return $hive;
        }

        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();

        $audioData = HiveAudio::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('path', 'created_at')
            ->paginate(10);

        $formattedData = $this->formatMediaData($audioData, 'hiveaudio');

        return $formattedData;
    }


    /**
     * Check if the authenticated user owns the specified hive.
     *
     * @param  Request  $request
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse|Hive
     */
    private function checkHiveOwnership(Request $request, $hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }

        $user = $request->user();
        $farmer = $user->farmer;

        if ($farmer->id !== $hive->farm->ownerId) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return $hive;
    }

    /**
     * Format the media data for the response.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator  $data
     * @param  string  $folder
     * @return \Illuminate\Http\JsonResponse
     */
    private function formatMediaData($data, $folder)
    {
        foreach ($data as $record) {
            $record->path = 'public/' . $folder . '/' . $record->path;
        }

        return response()->json([
            'data' => $data->map(function ($item) {
                return [
                    'date' => $item->created_at,
                    'path' => $item->path,
                ];
            }),
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'next_page_url' => $data->nextPageUrl(),
                'prev_page_url' => $data->previousPageUrl(),
            ],
        ]);
    }
}