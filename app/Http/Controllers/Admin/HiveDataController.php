<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HiveDataController extends Controller
{   
    /* --------------------------------------------------------------TEMPERATURE--------------------------------------------------------------------*/


    //default view for the graphs page and temperature graphs page
    public function temperatureDefault($id)
    {

        //lets put this variable on a session.
        session(['hive_id' => $id]);

        // Get the hive data and also the related farm data.
        $hive = DB::table('hives')->where('id', $id)->first();
        $farm = DB::table('farms')->where('id', $hive->farm_id)->first();

        // Pass the hive id and the farm name to the view.
        return view('admin.hivegraphs.temperature', ['hive_id' => $id, 'farm_name' => $farm->name]);
    }
    
    public function getTemperatureData(Request $request, $hive)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        // Ensure the dates are in a valid format
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $tableName = $request->query('table');

        // Fetch the data from the database
        $temperatureData = DB::table($tableName)
            ->where('hive_id', $hive)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Process the data into the format expected by the chart
        $dates = [];
        $honeySection = [];
        $broodSection = [];
        $exterior = [];
        
        foreach ($temperatureData as $record) {
            // Assuming the temperature data is stored as 'exterior,brood,honey'
            list($honeyTemp, $broodTemp, $exteriorTemp) = explode('*', $record->record);
            
            // Turn the "2" values into null
            $honeyTemp = $honeyTemp == 2 ? null : $honeyTemp;
            $broodTemp = $broodTemp == 2 ? null : $broodTemp;
            $exteriorTemp = $exteriorTemp == 2 ? null : $exteriorTemp;
            
            $dates[] = $record->created_at; // add this line to collect the dates
            $honeySection[] = $honeyTemp;
            $broodSection[] = $broodTemp;
            $exterior[] = $exteriorTemp;
        }
        
        // Return the data as a JSON response
        return response()->json([
            'dates' => $dates, 
            'exterior' => $exterior,
            'broodSection' => $broodSection,
            'honeySection' => $honeySection,
        ]);
    }

    /* ----------------------------------------------------------------HUMIDITY-----------------------------------------------------------*/

     //default view for the  humidity graphs page
     public function humidityDefault($id)
     {
        // Get the hive data and also the related farm data.
        $hive = DB::table('hives')->where('id', $id)->first();
        $farm = DB::table('farms')->where('id', $hive->farm_id)->first();

        // Pass the hive id and the farm name to the view.
        return view('admin.hivegraphs.humidity', ['hive_id' => $id, 'farm_name' => $farm->name]);
     }


    public function getHumidityData(Request $request, $hive)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        // Ensure the dates are in a valid format
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $tableName = $request->query('table');

        // Fetch the data from the database
        $humidityData = DB::table($tableName)
            ->where('hive_id', $hive)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Process the data into the format expected by the chart
        $dates = [];
        $exterior = [];
        $broodSection = [];
        $honeySection = [];
        
        foreach ($humidityData as $record) {
            // Assuming the temperature data is stored as 'exterior,brood,honey'
            list($honeyHumid,  $broodHumid, $exteriorHumid) = explode('*', $record->record);
            
            // Turn the "2" values into null
            $exteriorHumid =  $exteriorHumid == 2 ? null :  $exteriorHumid;
            $broodHumid = $broodHumid == 2 ? null : $broodHumid;
            $honeyHumid = $honeyHumid == 2 ? null : $honeyHumid;
            
            $dates[] = $record->created_at; // add this line to collect the dates
            $exterior[] =  $exteriorHumid;
            $broodSection[] = $broodHumid;
            $honeySection[] = $honeyHumid;
        }
        
        // Return the data as a JSON response
        return response()->json([
            'dates' => $dates,
            'exterior' => $exterior,
            'broodSection' => $broodSection,
            'honeySection' => $honeySection,
        ]);
    }

    /* ----------------------------------------------------------------WEIGHT-----------------------------------------------------------*/
     //default view for the  weight graphs page
     public function weightDefault($id)
     {
        // Get the hive data and also the related farm data.
        $hive = DB::table('hives')->where('id', $id)->first();
        $farm = DB::table('farms')->where('id', $hive->farm_id)->first();

        // Pass the hive id and the farm name to the view.
        return view('admin.hivegraphs.weight', ['hive_id' => $id, 'farm_name' => $farm->name]);
     }

     public function getWeightData(Request $request, $hive)
     {
         $start = $request->query('start');
         $end = $request->query('end');
 
         // Ensure the dates are in a valid format
         $start = Carbon::parse($start);
         $end = Carbon::parse($end);
         $tableName = $request->query('table');
 
         // Fetch the data from the database
         $weightData = DB::table($tableName)
             ->where('hive_id', $hive)
             ->whereBetween('created_at', [$start, $end])
             ->get();
 
         // Process the data into the format expected by the chart
         $dates = [];
         $overall = [];

         
         foreach ($weightData as $record) {
             //Store the weight value as overallWeight'
             $overallWeight =  $record->record;
           
             // Turn the "2" values into null
             $overallWeight =  $overallWeight == 2 ? null :  $overallWeight;

             
             $dates[] = $record->created_at; // add this line to collect the dates
             $overall[] =  $overallWeight;
 
         }
         
         // Return the data as a JSON response
         return response()->json([
             'dates' => $dates,
             'overall' => $overall,

         ]);
     }
    /* ----------------------------------------------------------------TEMPERATURE VERSUS HUMIDITY-----------------------------------------------------------*/

     public function tempHumidity_default($id){
                // Get the hive data and also the related farm data.
                $hive = DB::table('hives')->where('id', $id)->first();
                $farm = DB::table('farms')->where('id', $hive->farm_id)->first();
        
                // Pass the hive id and the farm name to the view.
                return view('admin.hivegraphs.tempHumidity', ['hive_id' => $id, 'farm_name' => $farm->name]);
     }

     public function getTempHumidity(Request $request, $hive ){
        $start = $request->query('start');
        $end = $request->query('end');

        $tables = request('tables');
    
        // Split the table string and assign the name to variables
        list($humidityTable, $temperatureTable) = explode(',', $tables); 
        
         // Fetch the data from the database

        $humidityData = DB::table($humidityTable)
             ->where('hive_id', $hive)
             ->whereBetween('created_at', [$start, $end])
             ->get();

        $tempData = DB::table($temperatureTable)
            ->where('hive_id', $hive)
            ->whereBetween('created_at', [$start, $end])
            ->get();
        // Process the data into the format expected by the chart
        $dates = [];
        $humidExt= [];
        $tempExt = [];
        $humidBrood= [];
        $tempBrood= [];
        $humidHoney  = [];
        $tempHoney= [];


// For humidity data
        foreach ($humidityData as $record) {
            // Assuming the humidity data is stored as 'honey, brood, exterior'
            list($honeyHumid, $broodHumid,$exteriorHumid ) = explode('*', $record->record);
           
            // Turn the "2" values into null
            $exteriorHumid =  $exteriorHumid == 2 ? null :  $exteriorHumid;
            $broodHumid = $broodHumid == 2 ? null : $broodHumid;
            $honeyHumid = $honeyHumid == 2 ? null : $honeyHumid;
            
            $dates[] = $record->created_at; // add this line to collect the dates
            $humidExt[] =  $exteriorHumid;
            $humidBrood[] = $broodHumid;
            $humidHoney[] = $honeyHumid;
        }

// Temperature data
        foreach ($tempData as $record) {
            // Assuming the temperature data is stored as 'exterior,brood,honey'
            list($honeyTemp, $broodTemp,$exteriorTemp ) = explode('*', $record->record);
           
            // Turn the "2" values into null
            $exteriorTemp =  $exteriorTemp == 2 ? null :  $exteriorTemp;
            $broodTemp = $broodTemp == 2 ? null : $broodTemp;
            $honeyTemp = $honeyTemp == 2 ? null : $honeyTemp;
            
            $dates[] = $record->created_at; // add this line to collect the dates
            $tempExt[] =  $exteriorTemp;
            $tempBrood[] = $broodTemp;
            $tempHoney[] = $honeyTemp;
        }

                    // Return the data as a JSON response
        return response()->json([
            'dates' => $dates,
            'humidExteriorSection' => $humidExt,
            'humidBroodSection' => $humidBrood,
            'humidHoneySection' => $humidHoney,
            'tempExteriorSection' => $tempExt,
            'tempBroodSection' => $tempBrood,
            'tempHoneySection' => $tempHoney,
        ]);
     }

    /* ----------------------------------------------------------------CARBONDIOXIDE-----------------------------------------------------------*/
    public function carbondioxide_default($id){
        // Get the hive data and also the related farm data.
        $hive = DB::table('hives')->where('id', $id)->first();
        $farm = DB::table('farms')->where('id', $hive->farm_id)->first();

        // Pass the hive id and the farm name to the view.
        return view('admin.hivegraphs.carbondioxide', ['hive_id' => $id, 'farm_name' => $farm->name]);
    }

    public function getCarbondioxide_data(Request $request, $hive){
        $start = $request->query('start');
        $end = $request->query('end');

        // Ensure the dates are in a valid format
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $tableName = $request->query('table');

        // Fetch the data from the database
        $carbonData = DB::table($tableName)
            ->where('hive_id', $hive)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Process the data into the format expected by the chart
        $dates = [];
        $carbonLevels = [];

        
        foreach ($carbonData as $record) {
            //Store the weight value as overallWeight'
            $carbon =  $record->record;
          
            // Turn the "2" values into null
            $carbon =  $carbon == 2 ? null :  $carbon;

            
            $dates[] = $record->created_at; // add this line to collect the dates
            $carbonLevels[] =  $carbon;

        }
        
        // Return the data as a JSON response
        return response()->json([
            'dates' => $dates,
            'carbonLevels' => $carbonLevels,

        ]);
    }
    public function getHiveVibration_data($id){
        $hive=DB::table('hives')->find($id);
        $farm = DB::table('farms')->find($hive->farm_id);
        return view('admin.hivegraphs.vibrations',['hive_id' => $id, 'farm_name' => $farm->name]);
    }
   
}
