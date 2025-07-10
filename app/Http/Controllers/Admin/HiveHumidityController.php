<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\HiveHumidity;
use Illuminate\Http\Request;
use App\Exports\HiveHumidityExport;
use Maatwebsite\Excel\Facades\Excel;


class HiveHumidityController extends Controller
{
     /**
     * Display a listing of the hive videos
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {        
       // $perPage = 30;
       
       // $humidity = HiveHumidity::latest()->paginate($perPage);

       $hiveId = $request->query('hive_id');
       // return $hiveId;
       //$humidity = HiveHumidity::where('hive_id', $hiveId)->get();

        $humidity = HiveHumidity::where('hive_id', $hiveId)
        ->latest() // This orders the records by the created_at column in descending order (latest first).
        ->limit(100) // This limits the result to the latest 100 entries.
        ->get();

       

        return view('admin.hivedata.humidity', compact('humidity'));
    }

    public function export(Request $request)
    {
        $hiveId = $request->query('hive_id');
        return Excel::download(new HiveHumidityExport($hiveId), 'hive_humidity.xlsx');
    }
}
   

