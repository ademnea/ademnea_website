<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Thingspeak;
use App\Models\Battery;
use App\Models\Hive;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class ThingSpeakController extends Controller
{
    //retrieve and display the data onto the view
    public function showMonitoring($hive_id)
{
    $client = new Client();
    $response = $client->get('https://api.thingspeak.com/channels/2715993/feeds.json', [
        'query' => [
            'api_key' => 'RYCJH8H1B9CNX8UV',
            'results' => 12
        ]
    ]);

    $thingspeakData = json_decode($response->getBody(), true);
    
    $battery_readings = collect($thingspeakData['feeds'])->map(function ($feed) {
        return [
            'reading_time' => $feed['created_at'],
            'voltage' => $feed['field1'],
            'battery_percentage' => $feed['field2']
        ];
    });

    $hive = Hive::findOrFail($hive_id);

    return view('admin.Thingspeak.index', [
        'hive' => $hive,
        'battery_readings' => $battery_readings,
        'hive_id' => $hive_id,
    ]);
}

//     //For fetching data from thingspeak and store it in the database.
//     public function fetchData($hive_id)
//     {
//         $thingspeak = Thingspeak::where('hive_id', $hive_id)->first();

//         if (!$thingspeak) {
//             return response()->json([
//                 'error' => 'ThingSpeak configuration not found for this hive'
//             ], 404);
//         }

//         $client = new Client();
//         $channelId = $thingspeak->channel_id;
//         $readApiKey = $thingspeak->read_api_key;

//         try {
//             $response = $client->get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
//                 'query' => [
//                     'api_key' => $readApiKey,
//                     'results' => 12
//                 ]
//             ]);

//             $data = json_decode($response->getBody(), true);

//             $this->storeReadings($hive_id, $data['feeds']);

//             return response()->json([
//                 'feeds' => $data['feeds'],
//                 'channel' => $data['channel']
//             ]);
//         } catch(\Exception $e) {
//             return response()->json([
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     //this function is used for storing readings to the database.
//     private function storeReadings($hive_id, $data)
//     {
//         foreach ($data as $reading) {

//             if (!is_null($reading['field1']) && !is_null($reading['field2'])) {
//                 Battery::create([
//                     'hive_id' => $hive_id,
//                     'voltage' => $reading['field1'],
//                     'battery_percentage' => $reading['field2'],
//                     'measured_at' => Carbon::parse($reading['created_at']),
//                 ]);
//             }
            
//         }
//     }

//     //This function is designed to fetch and store data for all hives that have ThingSpeak configurations set up.
//     public function fetchAndStoreAll()
//     {
//         $thingspeaks = Thingspeak::with('hive')->get();
//         $results = [];

//         foreach ($thingspeaks as $thing) {
//             try {
//                 $this->fetchData($thing->hive_id);
//                 $results[$thing->hive_id] = 'success';
//             } catch(\Exception $e) {
//                 $results[$thing->hive_id] = 'error: ' . $e->getMessage();
//             }
//         }

//         return response()->json([
//             'results' => $results
//         ]);
//     }


//     //This function is used to retrieve the latest battery reading for a specific hive.
//     public function getHiveData($hive_id)
//     {
//         $battery_reading = Battery::where('hive_id', $hive_id)->first();


//         $hive = Hive::findOrFail($hive_id);

//         return response()->json([
//             'battery' => $battery_reading,
//             'hive' => $hive
//         ]);
//     }
  }
