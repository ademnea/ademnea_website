<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Models\HiveTemperature;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\HiveTemperatureExport;
use Maatwebsite\Excel\Facades\Excel;

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

        //ajax to be used by the graphs. we need one more condition, 
        //to see that the temperture data page sent the request instead
        
        if($request->ajax())
        {

            $data = HiveTemperature::select('*');
            if($request->filled('from_date') && $request->filled('to_date'))
            {
                $data = $data->whereBetween('created_at', [$request->from_date, $request->to_date]);
                $temperatures = $data->whereBetween('created_at', [$request->from_date, $request->to_date]);
            }

            return DataTables::of($data)->addIndexColumn()->make(true);
        }

    else{

    $temperatures = HiveTemperature::where('hive_id', $hiveId)
    ->latest() // This orders the records by the created_at column in descending order (latest first).
    ->limit(100) // This limits the result to the latest 100 entries.
    ->get();

    }

        return view('admin.hivedata.temperatures', compact('temperatures'));
    }

    public function export(Request $request)
    {
        $hiveId = $request->query('hive_id');
        return Excel::download(new HiveTemperatureExport($hiveId), 'temperatures.xlsx');
    }

   
}
