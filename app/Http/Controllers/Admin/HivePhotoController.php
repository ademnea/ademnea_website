<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HivePhoto;

class HivePhotoController extends Controller
{
    /**
     * Display a listing of the hive photos.
     */
    public function index(Request $request)
    {
        // Validate hive_id
        $request->validate([
            'hive_id' => 'required|exists:hives,id'
        ]);

        $hiveId = $request->query('hive_id');

        // Get photos for the selected hive
        $photos = HivePhoto::where('hive_id', $hiveId)
            ->latest()
            ->paginate(8);

        return view('admin.hivedata.photos', compact('photos', 'hiveId'));
    }

    /**
     * Delete duplicate photos for cleanup.
     */
    public function cleanupDuplicates()
    {
        // Find duplicate groups
        $duplicates = DB::table('hive_photos')
            ->select('path', 'hive_id', DB::raw('COUNT(*) as count'))
            ->groupBy('path', 'hive_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            return redirect()->back()->with('info', 'No duplicate photos found.');
        }

        $removedCount = 0;

        foreach ($duplicates as $duplicate) {
            // Get all photos with this path and hive_id
            $photos = HivePhoto::where('path', $duplicate->path)
                ->where('hive_id', $duplicate->hive_id)
                ->orderBy('id')
                ->get();

            $keep = $photos->shift();

            foreach ($photos as $photo) {
                // Delete the duplicate photo
                $photo->delete();
                $removedCount++;
            }
        }

        return redirect()->back()->with('success', "Cleaned up $removedCount duplicate photos.");
    }
}
