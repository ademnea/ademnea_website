<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

use App\Http\Controllers\ThingSpeakController;
use App\Http\Controllers\Admin\BeehiveInspectionController;
use App\Http\Controllers\Api\V1\ImageController;
use App\Http\Controllers\Api\V1\VideoController;

// Authenticated user route
Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

// API v1 routes
Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('login', 'App\Http\Controllers\Api\V1\UserController@login');

    // Public media routes
    Route::get("/img", [ImageController::class, "show"]);
    Route::get("/vids", [VideoController::class, "show"]);
    Route::get("/vids/latest", [VideoController::class, "showLatest"]);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {

        // User
        Route::post('logout', 'App\Http\Controllers\Api\V1\UserController@logout');

        // Farm routes
        Route::get('farms', 'App\Http\Controllers\Api\V1\FarmController@index');
        Route::get('farms/most-productive', 'App\Http\Controllers\Api\V1\FarmController@getMostProductiveFarm');
        Route::get('farms/count', 'App\Http\Controllers\Api\V1\FarmController@totalFarmsAndHives');
        Route::get('farms/time-until-harvest', 'App\Http\Controllers\Api\V1\FarmController@timeUntilHarvestSeason');
        Route::get('farms/supplementary-feeding', 'App\Http\Controllers\Api\V1\FarmController@getFarmsRequiringSupplementaryFeeding');
        Route::get('farms/{farm_id}/hives', 'App\Http\Controllers\Api\V1\HiveController@index');
        Route::get('farms/{farm_id}/temperature-stats', 'App\Http\Controllers\Api\V1\FarmController@getFarmTemperatureStats');
        Route::get('farms/{farm_id}/temperature-average', 'App\Http\Controllers\Api\V1\FarmController@getFarmAverageTemperature');
        Route::get('farms/{farm_id}/weight-average', 'App\Http\Controllers\Api\V1\FarmController@getFarmAverageWeight');

        // Hive routes
        Route::get('hives/{hive_id}/latest-weight', 'App\Http\Controllers\Api\V1\HiveController@getLatestWeight');
        Route::get('hives/{hive_id}/latest-temperature', 'App\Http\Controllers\Api\V1\HiveController@getLatestTemperature');
        Route::get('hives/{hive_id}/state', 'App\Http\Controllers\Api\V1\HiveController@getCurrentHiveState');

        // Hive parameter data by date range
        Route::get('hives/{hive_id}/temperature/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveParameterDataController@getTemperatureForDateRange');
        Route::get('hives/{hive_id}/humidity/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveParameterDataController@getHumidityForDateRange');
        Route::get('hives/{hive_id}/weight/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveParameterDataController@getWeightForDateRange');
        Route::get('hives/{hive_id}/carbondioxide/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveParameterDataController@getCarbondioxideForDateRange');

        // Hive media data by date range
        Route::get('hives/{hive_id}/images/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveMediaDataController@getImagesForDateRange');
        Route::get('hives/{hive_id}/videos/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveMediaDataController@getVideosForDateRange');
        Route::get('hives/{hive_id}/audios/{from_date}/{to_date}', 'App\Http\Controllers\Api\V1\HiveMediaDataController@getAudiosForDateRange');

        // Hive management
        Route::post('farms/{farm_id}/hives', 'App\Http\Controllers\Api\V1\HiveController@addHive');
        Route::delete('hives/{hive_id}', 'App\Http\Controllers\Api\V1\HiveController@deleteHive');
        Route::put('hives/{hive_id}', 'App\Http\Controllers\Api\V1\HiveController@updateHive');
    });

    // Optional ThingSpeak route (you can uncomment if needed)
    // Route::get('/thingspeak-data', [ThingSpeakController::class, 'fetchAndStoreData']);

    // Raw file upload route (for ESP device, for example)
    Route::post('/upload', function (Request $request) {
        try {
            $filename = $request->header('File-Name', 'uploaded_file.bin');
            Storage::disk('public')->put("esp/{$filename}", $request->getContent());
            return response()->json(['message' => "File $filename uploaded successfully."], 200);
        } catch (\Exception $e) {
            Log::error('Upload failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Upload failed', 'details' => $e->getMessage()], 500);
        }
    });
});

// External form submission route
Route::post('/submit-inspection', [BeehiveInspectionController::class, 'storeInspection']);
