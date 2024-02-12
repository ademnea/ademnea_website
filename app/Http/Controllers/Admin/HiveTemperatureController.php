<?php

namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\HiveTemperature;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HiveTemperatureController extends Controller
{
     /**
     * Display a listing of the hive videos
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {      

        $hiveId = $request->query('hive_id');
        $start = $request->query('startDate');
        $end = $request->query('endDate');

    //   dd($start);
        // // Ensure the dates are in a valid format
        // $start = Carbon::parse($start);
        // $end = Carbon::parse($end);
        // $tableName = $request->query('table');

        // // Fetch the data from the database
        // $temperatures = DB::table($tableName)
        //     ->where('hive_id', $hiveId)
        //     ->whereBetween('created_at', [$start, $end])
        //     ->get();
 

    $temperatures = HiveTemperature::where('hive_id', $hiveId)
    ->latest() // This orders the records by the created_at column in descending order (latest first).
    ->limit(100) // This limits the result to the latest 100 entries.
    ->get();



        return view('admin.hivedata.temperatures', compact('temperatures'));


   
}
}