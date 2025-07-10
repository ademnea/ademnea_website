<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\HiveWeight;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HiveWeightExport;

class HiveWeightController extends Controller
{
     /**
     * Display a listing of the hive videos
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {        
        //$perPage = 30;
       
        //$weights = HiveWeight::latest()->paginate($perPage);

        $hiveId = $request->query('hive_id');
        // return $hiveId;
      //  $weights = HiveWeight::where('hive_id', $hiveId)->get();

        $weights = HiveWeight::where('hive_id', $hiveId)
        ->latest() // This orders the records by the created_at column in descending order (latest first).
        ->limit(100) // This limits the result to the latest 100 entries.
        ->get();


        return view('admin.hivedata.weights', compact('weights'));
    }

    public function export(Request $request)
    {
        $hiveId = $request->query('hive_id');
        return Excel::download(new HiveWeightExport($hiveId), 'weights.xlsx');
    }
   
}
