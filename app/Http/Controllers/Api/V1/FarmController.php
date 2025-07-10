<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Farmer;
use App\Models\Farm;
use App\Models\HiveTemperature;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class FarmController extends Controller
{

    /**
     * Display a listing of the farms of the authenticated farmer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $farmer = $this->getAuthenticatedFarmer($request);

        if ($farmer instanceof \Illuminate\Http\JsonResponse) {
            return $farmer;
        }

        $farms = $farmer->farms;

        foreach ($farms as $farm) {
            $averageTemperature = $this->getFarmAverageTemperature($request, $farm->id);
            $averageWeight = $this->getFarmAverageWeight($farm->id);

            if ($averageTemperature instanceof \Illuminate\Http\JsonResponse) {
                $averageTemperature = $averageTemperature->getData()->average_temperature ?? null;
            }

            if ($averageWeight instanceof \Illuminate\Http\JsonResponse) {
                $averageWeightData = $averageWeight->getData();
                $farm->average_weight = $averageWeightData->average_weight ?? null;
                $farm->average_honey_percentage = $averageWeightData->average_honey_percentage ?? null;
            }

            $farm->average_temperature = $averageTemperature;
        }

        return response()->json($farms);
    }


    /**
     * Get the authenticated farmer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Farmer
     */
    private function getAuthenticatedFarmer(Request $request)
    {
        $user = $request->user();
        $farmer = Farmer::where('user_id', $user->id)->first();

        if (!$farmer) {
            return response()->json(['error' => 'Farmer not found'], 404);
        }

        return $farmer;
    }

    /**
     * Display the current average temperature of all hives in a farm
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFarmAverageTemperature(Request $request, $farm_id)
    {
        $farm = Farm::find($farm_id);

        if (!$farm) {
            return response()->json(['error' => 'Farm not found'], 404);
        }

        // Get the currently authenticated user
        $user = $request->user();

        // Get the farmer associated with the user
        $farmer = $user->farmer;

        // Check if the farmer is the owner of the farm
        if ($farmer->id !== $farm->ownerId) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $hives = $farm->hives;

        $totalTemperature = 0;
        $hiveCount = 0;

        foreach ($hives as $hive) {
            $latestTemperatureRecord = HiveTemperature::where('hive_id', $hive->id)->orderBy('created_at', 'desc')->first();

            if (!$latestTemperatureRecord) {
                continue;
            }

            $tempData = explode('*', $latestTemperatureRecord->record);
            if (count($tempData) !== 3) {
                continue; // Skip if the record format is incorrect
            }

            list($interiorTemp, $broodTemp, $exteriorTemp) = $tempData;
            $interiorTemp = $interiorTemp == 2 ? null : (float) $interiorTemp;
            $exteriorTemp = $exteriorTemp == 2 ? null : (float) $exteriorTemp;

            if ($interiorTemp !== null && $exteriorTemp !== null) {
                $averageTemperature = ($interiorTemp + $exteriorTemp) / 2;
                $totalTemperature += $averageTemperature;
                $hiveCount++;
            }
        }

        if ($hiveCount === 0) {
            return response()->json(['error' => 'No temperature data available'], 404);
        }

        $averageFarmTemperature = $totalTemperature / $hiveCount;

        return response()->json(['average_temperature' => $averageFarmTemperature]);
    }


    /**
     * Get the current average weight of a farm
     *
     * @param  int  $farm_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFarmAverageWeight($farm_id)
    {
        $farm = Farm::find($farm_id);

        if (!$farm) {
            return response()->json(['error' => 'Farm not found'], 404);
        }

        $hives = $farm->hives;

        $totalWeight = 0;
        $totalHoneyPercentage = 0;
        $hiveCount = 0;

        foreach ($hives as $hive) {
            $latestWeight = (new HiveController)->getLatestWeight($hive->id)->original;

            if ($latestWeight['record'] !== null && $latestWeight['honey_percentage'] !== null) {
                $totalWeight += $latestWeight['record'];
                $totalHoneyPercentage += $latestWeight['honey_percentage'];
                $hiveCount++;
            }
        }

        if ($hiveCount === 0) {
            return response()->json(['error' => 'No weight data available'], 404);
        }

        $averageWeight = $totalWeight / $hiveCount;
        $averageHoneyPercentage = (new HiveController)->getHiveHoneyPercentage($averageWeight);

        return response()->json([
            'average_weight' => $averageWeight,
            'average_honey_percentage' => $averageHoneyPercentage,
        ]);
    }

    /**
     * Get the most productive farm currently for a farmer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMostProductiveFarm(Request $request)
    {
        $user = $request->user();

        $farmer = $user->farmer;

        $farms = $farmer->farms;

        $maxAverageWeight = 0;
        $mostProductiveFarm = null;

        foreach ($farms as $farm) {
            $averageWeightResponse = $this->getFarmAverageWeight($farm->id);
            $averageWeightData = json_decode($averageWeightResponse->getContent(), true);

            if (!isset($averageWeightData['average_weight'])) {
                continue;
            }

            $averageWeight = $averageWeightData['average_weight'];

            if ($averageWeight > $maxAverageWeight) {
                $maxAverageWeight = $averageWeight;
                $mostProductiveFarm = $farm;
            }
        }

        if (!$mostProductiveFarm) {
            return response()->json(['error' => 'No productive farm found'], 404);
        }

        $averageHoneyPercentage = (new HiveController)->getHiveHoneyPercentage($maxAverageWeight);

        return response()->json([
            'most_productive_farm' => $mostProductiveFarm,
            'average_weight' => $maxAverageWeight,
            'average_honey_percentage' => $averageHoneyPercentage,
        ]);
    }

    /**
     * Display the total number of farms owned by the authenticated farmer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function totalFarmsAndHives(Request $request)
    {
        $farmer = $this->getAuthenticatedFarmer($request);

        if ($farmer instanceof \Illuminate\Http\JsonResponse) {
            return $farmer;
        }

        $totalFarms = $farmer->farms->count();

        $totalHives = 0;
        foreach ($farmer->farms as $farm) {
            $totalHives += $farm->hives->count();
        }

        return response()->json(['total_farms' => $totalFarms, 'total_hives' => $totalHives]);
    }

    /**
     * Check if the authenticated farmer owns the farm.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $farm_id
     * @return \App\Models\Farm|\Illuminate\Http\JsonResponse
     */
    private function checkFarmOwnership(Request $request, $farm_id)
    {
        $farmer = $this->getAuthenticatedFarmer($request);

        if ($farmer instanceof Response) {
            return $farmer;
        }

        $farm = Farm::where('id', $farm_id)->where('ownerId', $farmer->id)->first();

        if (!$farm) {
            return response()->json(['error' => 'Farm not found or not owned by the authenticated farmer'], 404);
        }

        return $farm;
    }


    /**
     * Calculate the months until the start of the harvest season.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function timeUntilHarvestSeason(Request $request)
    {
        $farmer = $this->getAuthenticatedFarmer($request);

        if ($farmer instanceof \Illuminate\Http\JsonResponse) {
            return $farmer;
        }

        // Define the start and end dates of the harvest seasons
        $harvestSeasons = [
            ['start' => '01-01', 'end' => '02-28'], // January to February
            ['start' => '06-01', 'end' => '08-31'], // June to August
            ['start' => '12-01', 'end' => '12-31']  // December
        ];

        // Get the current date
        $currentDate = new \DateTime();
        $currentYear = (int) $currentDate->format('Y');

        // Initialize the time until the start of the next harvest season
        $timeUntilHarvest = null;

        foreach ($harvestSeasons as $season) {
            $startOfSeason = \DateTime::createFromFormat('Y-m-d', $currentYear . '-' . $season['start']);
            $endOfSeason = \DateTime::createFromFormat('Y-m-d', $currentYear . '-' . $season['end']);

            // Check if the current date is within the harvest season
            if ($currentDate >= $startOfSeason && $currentDate <= $endOfSeason) {
                return response()->json([
                    'time_until_harvest' => [
                        'months' => 0,
                        'days' => 0,
                        'percentage_time_left' => 100,
                    ]
                ]);
            }

            // Adjust the start of the season to the next year if it has already passed this year
            if ($startOfSeason < $currentDate) {
                $startOfSeason->modify('+1 year');
            }

            // Calculate the interval until the start of the next harvest season
            $interval = $currentDate->diff($startOfSeason);
            $totalDaysInInterval = (int) $currentDate->diff($startOfSeason)->format("%a");

            // If this is the first interval calculated or if it's shorter than the previously calculated interval, update the timeUntilHarvest
            if ($timeUntilHarvest === null || $interval->days < $timeUntilHarvest->days) {
                $timeUntilHarvest = $interval;
            }
        }

        // Calculate the total duration of the next harvest season interval
        $startOfNextSeason = \DateTime::createFromFormat('Y-m-d', $currentYear . '-' . $harvestSeasons[0]['start']);
        if ($startOfNextSeason < $currentDate) {
            $startOfNextSeason->modify('+1 year');
        }

        $totalDaysInInterval = (int) $currentDate->diff($startOfNextSeason)->format("%a");

        // Calculate the percentage of time left
        $percentageTimeLeft = ($timeUntilHarvest->days / $totalDaysInInterval) * 100;

        return response()->json([
            'time_until_harvest' => [
                'months' => $timeUntilHarvest->m,
                'days' => $timeUntilHarvest->d,
                'percentage_time_left' => round($percentageTimeLeft, 2), // Round to 2 decimal places
            ]
        ]);
    }


    /**
     * Display the total number of hives of a farm.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $farm_id
     * @return \Illuminate\Http\Response
     */
    public function totalHives(Request $request, $farm_id)
    {
        $farmer = $this->getAuthenticatedFarmer($request);

        if ($farmer instanceof \Illuminate\Http\JsonResponse) {
            return $farmer;
        }

        $farm = $farmer->farms()->find($farm_id);

        if (!$farm) {
            return response()->json(['error' => 'Farm not found'], 404);
        }

        $totalHives = $farm->hives->count();

        return response()->json(['total_hives' => $totalHives]);
    }

    /**
     * Display temperature stats of a farm given the start date and end date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $farm_id
     * @param  string  $from_date
     * @param  string  $to_date
     * @return \Illuminate\Http\Response
     */
    public function getFarmTemperatureStats(Request $request, $farm_id)
    {
        $farm = $this->checkFarmOwnership($request, $farm_id);

        if ($farm instanceof Response) {
            return $farm;
        }

        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $from_date = Carbon::parse($request->query('from_date'));
        $to_date = Carbon::parse($request->query('to_date'));

        $interiorTemps = [];
        $exteriorTemps = [];

        if ($farm instanceof JsonResponse) {
            return $farm;
        }

        foreach ($farm->hives as $hive) {
            $temperatureData = HiveTemperature::where('hive_id', $hive->id)
                ->whereBetween('created_at', [$from_date, $to_date])
                ->select('record', 'created_at')
                ->get();

            if ($temperatureData->isEmpty()) {
                continue; // Skip hives with no data
            }

            foreach ($temperatureData as $record) {
                $tempData = explode('*', $record->record);

                if (count($tempData) !== 3) {
                    continue; // Skip if the record format is incorrect
                }

                list($interiorTemp, $broodTemp, $exteriorTemp) = $tempData;
                $interiorTemp = $interiorTemp == 2 ? null : (float) $interiorTemp;
                $exteriorTemp = $exteriorTemp == 2 ? null : (float) $exteriorTemp;

                if ($interiorTemp !== null) {
                    $interiorTemps[] = $interiorTemp;
                }

                if ($exteriorTemp !== null) {
                    $exteriorTemps[] = $exteriorTemp;
                }
            }
        }

        // Calculate the highest, lowest, and average temperatures
        $interiorTempStats = !empty($interiorTemps) ? [
            'highest' => max($interiorTemps),
            'lowest' => min($interiorTemps),
            'average' => round(array_sum($interiorTemps) / count($interiorTemps), 1),
        ] : ['error' => 'No interior temperature records found for the given date range'];

        $exteriorTempStats = !empty($exteriorTemps) ? [
            'highest' => max($exteriorTemps),
            'lowest' => min($exteriorTemps),
            'average' => round(array_sum($exteriorTemps) / count($exteriorTemps), 1),
        ] : ['error' => 'No exterior temperature records found for the given date range'];

        // Return the data as a JSON response
        return response()->json([
            'interiorTemperatureStats' => $interiorTempStats,
            'exteriorTemperatureStats' => $exteriorTempStats,
        ]);
    }

    /**
     * Display farms that require supplementary feeding.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFarmsRequiringSupplementaryFeeding(Request $request)
    {
        // Get the authenticated user from the request
        $user = $request->user();

        // Retrieve the farmer associated with the user
        $farmer = $user->farmer;

        if (!$farmer) {
            return response()->json(['error' => 'Farmer not found'], 404);
        }

        // Retrieve only the farms associated with that farmer
        $farms = $farmer->farms;
        $farmsRequiringSupplementaryFeeding = [];

        foreach ($farms as $farm) {
            $request->merge([
                'from_date' => Carbon::now()->subDays(7)->toDateString(),
                'to_date' => Carbon::now()->addDay()->toDateString(),
            ]);

            $temperatureStats = $this->getFarmTemperatureStats($request, $farm->id);

            if ($temperatureStats->getStatusCode() !== 200) {
                continue; // Skip if there was an error getting the temperature stats
            }

            $temperatureStatsData = json_decode($temperatureStats->getContent(), true);

            if (!isset($temperatureStatsData['exteriorTemperatureStats']['average'])) {
                continue; // Skip if there was no average exterior temperature
            }

            $averageExteriorTemperature = $temperatureStatsData['exteriorTemperatureStats']['average'];

            if ($averageExteriorTemperature >= 30) {
                $farm->setAttribute('averageTemperatureLast7Days', $averageExteriorTemperature);
                $farmsRequiringSupplementaryFeeding[] = $farm;
            }
        }

        return response()->json($farmsRequiringSupplementaryFeeding);
    }
    /**
     * Display humidity stats of a farm given the start date and end date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $farm_id
     * @param  string  $from_date
     * @param  string  $to_date
     * @return \Illuminate\Http\Response
     */
    public function getHumidityStatsForFarm(Request $request, $farm_id, $from_date, $to_date)
    {

    }




}
