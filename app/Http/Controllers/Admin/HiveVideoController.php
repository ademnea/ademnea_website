<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\HiveVideo;
use Illuminate\Http\Request;

class HiveVideoController extends Controller
{
     /**
     * Display a listing of the hive videos
     *
     * @return \Illuminate\View\View
     */
            public function index(Request $request)
            {
                $hiveId = $request->query('hive_id');

                $videos = HiveVideo::with('latestBeeCount')
                    ->where('hive_id', $hiveId)
                    ->latest()
                    ->paginate(8);

                return view('admin.hivedata.videos', compact('videos'));
            }

        /**
             * Clean up duplicate videos for a specific hive
             *
             * @param int $hiveId
             * @return int Number of deleted duplicates
             */
            public function cleanupDuplicates($hiveId)
            {
                // Find the duplicates
                $duplicates = DB::table('hive_videos')
                    ->select('path', 'hive_id', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as count'))
                    ->where('hive_id', $hiveId)
                    ->groupBy('path', 'hive_id')
                    ->having('count', '>', 1)
                    ->get();

                $deletedCount = 0;

                // Delete the duplicates, keeping the one with the lowest ID
                foreach ($duplicates as $dup) {
                    $deleted = DB::table('hive_videos')
                        ->where('path', $dup->path)
                        ->where('hive_id', $dup->hive_id)
                        ->where('id', '!=', $dup->keep_id)
                        ->delete();

                    $deletedCount += $deleted;
                }

                return $deletedCount;
            }
    }
