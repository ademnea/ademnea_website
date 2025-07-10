<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Response;


class PowerMonitoringController extends Controller
{

    public function getBatteryData(Request $request, $hive_id)
    {
        // Get start and end dates from request
        $start = $request->query('start');
        $end = $request->query('end');

        // Ensure the dates are in a valid format
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $tableName = $request->query('table');

        // Fetch the data from the database
        $batteryData = DB::table($tableName)
            ->where('hive_id', $hive_id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Process the data into the format expected by the chart
        $dates = [];
        $percentage = [];
        $voltage = [];



        foreach ($batteryData as $record) {
            //Store the percentage and voltage in an array.'
            $batteryPercentage =  $record->percent;
            $batteryVoltage =  $record->voltage;


            // Turn the "2" values into null
            $batteryPercentage =  $batteryPercentage == 2 ? null :  $batteryPercentage;


            $dates[] = $record->created_at; // add this line to collect the dates
            $percentage[] =  $batteryPercentage;
            $voltage[] =  $batteryVoltage;
        }

        // Return the data as a JSON response
        return response()->json([
            'dates' => $dates,
            'percentage' => $percentage,
            'voltage' => $voltage,

        ]);
    }



    public function batteryDefault($id)
    {

        //lets put this variable on a session.
        session(['hive_id' => $id]);


        // Get the hive battery data and also the related farm data.
        $hive_battery = DB::table('hive_battery')->where('hive_id', $id)->get();

        // Get the current time in the database format (assuming the 'created_at' column is of type 'datetime')
        $currentDateTime = Carbon::today()->toDateTimeString();

        // Retrieve today's data for a certain hive
        $battery_percentage = DB::table('hive_battery')
            ->where('hive_id', $id)
            ->whereDate('created_at', $currentDateTime)
            ->orderByDesc('created_at')
            ->first();

        // If 'percent' column exists and it's part of the $hive_battery object, get the value
        $percentValue=isset($battery_percentage->percent) ? $battery_percentage->percent : null;


        // Pass the hive id and the farm name to the view.
        return view('admin.power-monitoring.index', ['percentValue'=>$percentValue]);
    }

 
}
