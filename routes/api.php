<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'App\Http\Controllers\Api\V1', 'prefix' => 'v1'], function () {
    
    /*Routes for fetching hive parameter data given a certain date range */
    Route::get('hives/{hive_id}/temperature/{from_date}/{to_date}', 'HiveParameterDataController@getTemperatureForDateRange');
    Route::get('hives/{hive_id}/humidity/{from_date}/{to_date}', 'HiveParameterDataController@getHumidityForDateRange');
    Route::get('hives/{hive_id}/weight/{from_date}/{to_date}', 'HiveParameterDataController@getWeightForDateRange');
    Route::get('hives/{hive_id}/carbondioxide/{from_date}/{to_date}', 'HiveParameterDataController@getCarbondioxideForDateRange');
    
    
    /*Routes for fetching hive media data given a certain date range */
    Route::get('hives/{hive_id}/images/{from_date}/{to_date}', 'HiveMediaDataController@getImagesForDateRange');
    Route::get('hives/{hive_id}/videos/{from_date}/{to_date}', 'HiveMediaDataController@getVideosForDateRange');
    Route::get('hives/{hive_id}/audios/{from_date}/{to_date}', 'HiveMediaDataController@getAudiosForDateRange');

});




