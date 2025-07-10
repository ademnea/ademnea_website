<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\HiveTemperature;
use Carbon\Carbon;
use App\Models\Hive;
use App\Models\HiveHumidity;
use App\Models\HiveWeight;
use App\Models\HiveCarbondioxide;

class HiveParameterDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return HiveTemperature::all();
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return HiveTemperature::find($id);
    }

    public function getTemperatureForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);
    
        if ($hive instanceof Response) {
            return $hive;
        }
    
        // Ensure the dates are in a valid format
        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();
    
        $temperatureData = HiveTemperature::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('record', 'created_at')
            ->get();
    
        $data = [];
        $interiorTemps = [];
        $exteriorTemps = [];
    
        foreach ($temperatureData as $record) {
            // Assuming the temperature data is stored as 'exterior,brood,honey'
            list($interiorTemp, $broodTemp, $exteriorTemp) = explode('*', $record->record);
    
            // Turn the "2" values into null
            $interiorTemp = $interiorTemp == 2 ? null : $interiorTemp;
            $exteriorTemp = $exteriorTemp == 2 ? null : $exteriorTemp;
    
            $data[] = [
                'date' => $record->created_at,
                'interiorTemperature' => $interiorTemp,
                'exteriorTemperature' => $exteriorTemp,
            ];
    
            if ($interiorTemp !== null) {
                $interiorTemps[] = $interiorTemp;
            }
    
            if ($exteriorTemp !== null) {
                $exteriorTemps[] = $exteriorTemp;
            }
        }
    
        // Calculate the highest, lowest, and average temperatures
        $interiorTempStats = [
            'highest' => count($interiorTemps) > 0 ? max($interiorTemps) : null,
            'lowest' => count($interiorTemps) > 0 ? min($interiorTemps) : null,
            'average' => count($interiorTemps) > 0 ? round(array_sum($interiorTemps) / count($interiorTemps), 1) : null,
        ];
    
        $exteriorTempStats = [
            'highest' => count($exteriorTemps) > 0 ? max($exteriorTemps) : null,
            'lowest' => count($exteriorTemps) > 0 ? min($exteriorTemps) : null,
            'average' => count($exteriorTemps) > 0 ? round(array_sum($exteriorTemps) / count($exteriorTemps), 1) : null,
        ];
    
        // Return the data as a JSON response
        return response()->json([
            'data' => $data,
            'interiorTemperatureStats' => $interiorTempStats,
            'exteriorTemperatureStats' => $exteriorTempStats,
        ]);
    }


    public function getHumidityForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);
    
        if ($hive instanceof Response) {
            return $hive;
        }
    
        // Ensure the dates are in a valid format
        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();
    
        $humidityData = HiveHumidity::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('record', 'created_at')
            ->get();
    
        $data = [];
        $interiorHumidities = [];
        $exteriorHumidities = [];
    
        foreach ($humidityData as $record) {
            // Assuming the humidity data is stored as 'exterior,brood,honey'
            list($interiorHumidity, $broodHumidity, $exteriorHumidity) = explode('*', $record->record);
    
            // Turn the "2" values into null
            $interiorHumidity = $interiorHumidity == 2 ? null : $interiorHumidity;
            $exteriorHumidity = $exteriorHumidity == 2 ? null : $exteriorHumidity;
    
            $data[] = [
                'date' => $record->created_at,
                'interiorHumidity' => $interiorHumidity,
                'exteriorHumidity' => $exteriorHumidity,
            ];
    
            if ($interiorHumidity !== null) {
                $interiorHumidities[] = $interiorHumidity;
            }
    
            if ($exteriorHumidity !== null) {
                $exteriorHumidities[] = $exteriorHumidity;
            }
        }
    
        // Calculate the highest, lowest, and average humidities
        $interiorHumidityStats = [
            'highest' => count($interiorHumidities) > 0 ? max($interiorHumidities) : null,
            'lowest' => count($interiorHumidities) > 0 ? min($interiorHumidities) : null,
            'average' => count($interiorHumidities) > 0 ? round(array_sum($interiorHumidities) / count($interiorHumidities), 1) : null,
        ];
    
        $exteriorHumidityStats = [
            'highest' => count($exteriorHumidities) > 0 ? max($exteriorHumidities) : null,
            'lowest' => count($exteriorHumidities) > 0 ? min($exteriorHumidities) : null,
            'average' => count($exteriorHumidities) > 0 ? round(array_sum($exteriorHumidities) / count($exteriorHumidities), 1) : null,
        ];
    
        // Return the data as a JSON response
        return response()->json([
            'data' => $data,
            'interiorHumidityStats' => $interiorHumidityStats,
            'exteriorHumidityStats' => $exteriorHumidityStats,
        ]);
    }

    public function getWeightForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);
    
        if ($hive instanceof Response) {
            return $hive;
        }
    
        // Ensure the dates are in a valid format
        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();
    
        $weightData = HiveWeight::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('record', 'created_at')
            ->get();
    
        $data = [];
        $weights = [];
    
        foreach ($weightData as $record) {
            // Since weight is a single record, no need to split it
            $weight = $record->record;
    
            // Turn the "2" values into null
            $weight = $weight == 2 ? null : $weight;
    
            $data[] = [
                'date' => $record->created_at,
                'weight' => $weight,
            ];
    
            if ($weight !== null) {
                $weights[] = $weight;
            }
        }
    
        // Calculate the highest, lowest, and average weights
        $weightStats = [
            'highest' => count($weights) > 0 ? max($weights) : null,
            'lowest' => count($weights) > 0 ? min($weights) : null,
            'average' => count($weights) > 0 ? round(array_sum($weights) / count($weights), 1) : null,
        ];
    
        // Return the data as a JSON response
        return response()->json([
            'data' => $data,
            'weightStats' => $weightStats,
        ]);
    }

    public function getCarbondioxideForDateRange(Request $request, $hive_id, $from_date, $to_date)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);
    
        if ($hive instanceof Response) {
            return $hive;
        }
    
        // Ensure the dates are in a valid format
        $from_date = Carbon::parse($from_date);
        $to_date = Carbon::parse($to_date)->addDay();
    
        $carbondioxideData = HiveCarbondioxide::where('hive_id', $hive_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->select('record', 'created_at')
            ->get();
    
        $data = [];
        $carbondioxides = [];
    
        foreach ($carbondioxideData as $record) {
            // Since carbondioxide is a single record, no need to split it
            $carbondioxide = $record->record;
    
            // Turn the "2" values into null
            $carbondioxide = $carbondioxide == 2 ? null : $carbondioxide;
    
            $data[] = [
                'date' => $record->created_at,
                'carbondioxide' => $carbondioxide,
            ];
    
            if ($carbondioxide !== null) {
                $carbondioxides[] = $carbondioxide;
            }
        }
    
        // Calculate the highest, lowest, and average carbondioxides
        $carbondioxideStats = [
            'highest' => count($carbondioxides) > 0 ? max($carbondioxides) : null,
            'lowest' => count($carbondioxides) > 0 ? min($carbondioxides) : null,
            'average' => count($carbondioxides) > 0 ? round(array_sum($carbondioxides) / count($carbondioxides), 1) : null,
        ];
    
        // Return the data as a JSON response
        return response()->json([
            'data' => $data,
            'carbondioxideStats' => $carbondioxideStats,
        ]);
    }


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
 
}
