<?php

namespace App\Http\Controllers\Admin;

use App\Models\BatteryReading;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThingSpeakController2 extends Controller
{
    public function fetchAndStoreData()
{
    try {
        dd("test");
        $client = new Client();
        $response = $client->get('https://api.thingspeak.com/channels/2715993/feeds.json', [
            'query' => [
                'api_key' => 'RYCJH8H1B9CNX8UV',
                'results' => 12
            ]
        ]);

        // Decode JSON response
        $data = json_decode($response->getBody(), true);

        // Debugging: Dump and die to check the fetched data
        dd($data);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}

