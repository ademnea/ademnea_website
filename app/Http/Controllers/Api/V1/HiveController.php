<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Farm;
use App\Models\Hive;
use App\Models\HiveTemperature;
use App\Models\HiveHumidity;
use App\Models\HiveCarbondioxide;
use App\Models\HiveWeight;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\BeehiveInspection;

use Illuminate\Support\Facades\Validator;

class HiveController extends Controller
{
    /**
     * Display a listing of the hives of a specific farm.
     *
     * @param  int  $farm_id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $farm_id)
    {
        $farm = Farm::find($farm_id);

        if (!$farm) {
            return response()->json(['error' => 'Farm not found'], 404);
        }

        // Get the currently authenticated user
        $user = $request->user();

        // Get the farmer associated with the user
        if (!$user->farmer) {
            return response()->json(['error' => 'User is not associated with a farmer'], 403);
        }
        
        $farmer = $user->farmer;

        // Check if the farmer is the owner of the farm
        if ($farmer->id !== $farm->ownerId) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $hives = $farm->hives;

        foreach ($hives as $hive) {
            $hiveState = $this->getCurrentHiveState($request, $hive->id);
            if ($hiveState instanceof Response) {
                continue;
            }
            $hive->state = $hiveState->original;
        }

        return response()->json($hives);
    }

    /**
     * Display the specified hive.
     *
     * @param  int  $farm_id
     * @param  int  $hive_id
     * @return \Illuminate\Http\Response
     */
    private function checkHiveOwnership(Request $request, $hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }

        $user = $request->user();
        
        if (!$user->farmer) {
            return response()->json(['error' => 'User is not associated with a farmer'], 403);
        }
        
        $farmer = $user->farmer;

        if ($farmer->id !== $hive->farm->ownerId) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return $hive;
    }


    /**
     * Get the honey percentage of a hive.
     * 
     * @param float $weight
     * @return float
     * 
     */
    public function getHiveHoneyPercentage($hiveWeight)
    {
        $emptyHiveWeight = 18.0;
        $hiveWithColonyWeight = 30.0;
        $maxHiveWeight = 50.0;

        if ($hiveWeight < $hiveWithColonyWeight) {
            return 0.0;
        }

        $maxHoneyWeight = $maxHiveWeight - $hiveWithColonyWeight;
        $currentHoneyWeight = $hiveWeight - $hiveWithColonyWeight;
        $honeyPercentage = ($currentHoneyWeight / $maxHoneyWeight) * 100;

        return min($honeyPercentage, 100.0);
    }



    /**
     * Get the most recent weight of a hive.
     *
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestWeight($hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }

        $latestWeight = HiveWeight::where('hive_id', $hive_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestWeight) {
            if ($latestWeight->record == 2) {
                $latestWeight->record = null;
            }
            $honeyPercentage = $this->getHiveHoneyPercentage($latestWeight->record);
            $latestWeight->honey_percentage = $honeyPercentage;
            $latestWeight->date_collected = $latestWeight->created_at->format('Y-m-d H:i:s');
        }

        return response()->json([
            'record' => $latestWeight->record ?? null,
            'honey_percentage' => $latestWeight->honey_percentage ?? null,
            'date_collected' => $latestWeight->date_collected ?? null,
        ]);
    }

    /**
     * Get the most recent temperature of a hive.
     *
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestTemperature($hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }

        $latestTemperature = HiveTemperature::where('hive_id', $hive_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestTemperature) {
            if (substr_count($latestTemperature->record, '*') == 2) {
                list($interiorTemp, $broodTemp, $exteriorTemp) = explode('*', $latestTemperature->record);
                $interiorTemp = $interiorTemp == 2 ? null : (float) $interiorTemp;
                $exteriorTemp = $exteriorTemp == 2 ? null : (float) $exteriorTemp;

                $latestTemperature->interior_temperature = $interiorTemp;
                $latestTemperature->exterior_temperature = $exteriorTemp;
            }

            $latestTemperature->date_collected = $latestTemperature->created_at->format('Y-m-d H:i:s');
        }

        return response()->json([
            'interior_temperature' => $latestTemperature->interior_temperature ?? null,
            'exterior_temperature' => $latestTemperature->exterior_temperature ?? null,
            'date_collected' => $latestTemperature->date_collected ?? null,
        ]);
    }

    /**
     * Get the most recent humidity of a hive.
     *
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestHumidity($hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }

        $latestHumidity = HiveHumidity::where('hive_id', $hive_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestHumidity) {
            if (substr_count($latestHumidity->record, '*') == 2) {
                list($interiorHumidity, $broodHumidity, $exteriorHumidity) = explode('*', $latestHumidity->record);
                $interiorHumidity = $interiorHumidity == 2 ? null : (float) $interiorHumidity;
                $exteriorHumidity = $exteriorHumidity == 2 ? null : (float) $exteriorHumidity;

                $latestHumidity->interior_humidity = $interiorHumidity;
                $latestHumidity->exterior_humidity = $exteriorHumidity;
            }

            $latestHumidity->date_collected = $latestHumidity->created_at->format('Y-m-d H:i:s');
        }

        return response()->json([
            'interior_humidity' => $latestHumidity->interior_humidity ?? null,
            'exterior_humidity' => $latestHumidity->exterior_humidity ?? null,
            'date_collected' => $latestHumidity->date_collected ?? null,
        ]);
    }

    /**
     * Get the most recent carbon dioxide level of a hive.
     *
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestCarbonDioxide($hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }

        $latestCarbonDioxide = HiveCarbonDioxide::where('hive_id', $hive_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestCarbonDioxide) {
            $latestCarbonDioxide->date_collected = $latestCarbonDioxide->created_at->format('Y-m-d H:i:s');
        }

        return response()->json([
            'record' => $latestCarbonDioxide->record ?? null,
            'date_collected' => $latestCarbonDioxide->date_collected ?? null,
        ]);
    }

    /**
     * Get the connection status of a hive.
     *
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHiveConnectionStatus($hive_id)
    {
        $hive = Hive::find($hive_id);

        if (!$hive) {
            return response()->json(['error' => 'Hive not found'], 404);
        }
        $connectionStatus = $hive->connected;
       

        return response()->json(['Connected' => $connectionStatus]);
    }

    /**
     * Get the colonization status of a hive.
     *
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
public function getHiveColonizationStatus($hive_id)
{
    $hive = Hive::find($hive_id);

    if (!$hive) {
        return response()->json(['error' => 'Hive not found'], 404);
    }

    return response()->json(['Colonized' => $hive->colonized]);
}


    /**
     * Get the current state of a hive.
     *
     * @param  Request  $request
     * @param  int  $hive_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentHiveState(Request $request, $hive_id)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);

        if ($hive instanceof Response) {
            return $hive;
        }

        $latestWeight = $this->getLatestWeight($hive_id);
        $latestTemperature = $this->getLatestTemperature($hive_id);
        $latestHumidity = $this->getLatestHumidity($hive_id);
        $latestCarbonDioxide = $this->getLatestCarbonDioxide($hive_id);
        $connectionStatus = $this->getHiveConnectionStatus($hive_id);
        $colonizationStatus = $this->getHiveColonizationStatus($hive_id);

        $currentStatus = [
            'weight' => $latestWeight->original,
            'temperature' => $latestTemperature->original,
            'humidity' => $latestHumidity->original,
            'carbon_dioxide' => $latestCarbonDioxide->original,
            'connection_status' => $connectionStatus->original,
            'colonization_status' => $colonizationStatus->original,
        ];

        return response()->json($currentStatus);
    }

    // add hive, delete hive, update hive, hive management functions
public function addHive(Request $request, $farm_id)
{
    $farm = Farm::find($farm_id);

    if (!$farm) {
        return response()->json(['error' => 'Farm not found'], 404);
    }

    $user = $request->user();
    $farmer = $user->farmer;

    if (!$farmer) {
        return response()->json(['error' => 'User is not associated with a farmer'], 403);
    }

    if ($farmer->id !== $farm->ownerId) {
        return response()->json(['error' => 'Access denied'], 403);
    }

    $validated = $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'connected' => 'sometimes|boolean',
        'colonized' => 'sometimes|boolean',
    ]);

    $hive = Hive::create([
        'farm_id' => $farm->id,
        'latitude' => $validated['latitude'],
        'longitude' => $validated['longitude'],
        'connected' => $validated['connected'] ?? true,     // default true
        'colonized' => $validated['colonized'] ?? true,     // default true
    ]);

    return response()->json(['message' => 'Hive added successfully', 'hive' => $hive], 201);
}

public function updateHive(Request $request, $hive_id)
{
    $hive = $this->checkHiveOwnership($request, $hive_id);

    if ($hive instanceof Response) {
        return $hive;
    }

    $validated = $request->validate([
        'latitude' => 'sometimes|numeric',
        'longitude' => 'sometimes|numeric',
        'connected' => 'sometimes|boolean',
        'colonized' => 'sometimes|boolean',
    ]);

    $hive->update($validated);

    return response()->json(['message' => 'Hive updated successfully', 'hive' => $hive], 200);
}


    public function deleteHive(Request $request, $hive_id)
    {
        $hive = $this->checkHiveOwnership($request, $hive_id);

        if ($hive instanceof Response) {
            return $hive;
        }

        // Delete the hive
        $hive->delete();

        return response()->json(['message' => 'Hive deleted successfully'], 200);
    }





public function storeInspection(Request $request)
{
    $validator = Validator::make($request->all(), [
        'hiveId' => 'required|string',
        'inspection_date' => 'required|date',
        'inspector_name' => 'required|string',
        'weather_conditions' => 'nullable|string',

        'hive_type' => 'nullable|string',
        'hive_condition' => 'nullable|string',
        'queen_presence' => 'nullable|string',
        'queen_cells' => 'nullable|string',
        'brood_pattern' => 'nullable|string',
        'eggs_larvae' => 'nullable|string',
        'honey_stores' => 'nullable|string',
        'pollen_stores' => 'nullable|string',

        'bee_population' => 'nullable|string',
        'aggressiveness' => 'nullable|string',
        'diseases_observed' => 'nullable|string',
        'diseases_specify' => 'nullable|string',
        'pests_present' => 'nullable|string',

        'frames_checked' => 'nullable|string',
        'frames_replaced' => 'nullable|string',
        'hive_cleaned' => 'nullable|string',
        'supers_changed' => 'nullable|string',
        'other_actions' => 'nullable|string',

        'comments' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        $inspection = BeehiveInspection::create($validator->validated());

        return response()->json([
            'message' => 'Inspection record created successfully.',
            'data' => $inspection
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong while saving inspection.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



}