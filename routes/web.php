<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Admin\HiveDataController; 

use App\Exports\HiveTemperatureExport;
use GuzzleHttp\Psr7\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ThingSpeakController2;
use App\Http\Controllers\Admin\DataReportController;
use App\Http\Controllers\Admin\ScholarshipsController;
use App\Http\Controllers\ScholarshipDisplayController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\GalleryInternController;
use App\Http\Controllers\Admin\VOCController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\FeedbackController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[App\Http\Controllers\WebsiteController::class, 'index'])->name('website'); //route for home page, before login
Route::get('register', [AuthController::class, 'registerForm'])->name('registerForm');//
Route::post('register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/login', [AuthController::class, 'loginForm'])->name('loginForm');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');
Route::post('password/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('password/reset', [AuthController::class, 'passwordReset']);
//Route::view('/galleries', 'gallery');

//the following routes handle the event-galleries.
Route::post('/post_events','App\Http\Controllers\Admin\GalleryController@insert');

// Route::get('/albums_gallery','App\Http\Controllers\GalleryController@albums');
// Route::get('/teams_gallery','App\Http\Controllers\GalleryController@teams');
// Route::get('/galleries_gallery','App\Http\Controllers\GalleryController@galleries');
// Route::get('/photos_gallery','App\Http\Controllers\GalleryController@photos');

//gallery view page
 //Route::view('/galleryview','galleryview');
 Route::get('/gallery_view','App\Http\Controllers\DisplayEventController@view_gallery');

Route::middleware('auth:web')->group(function () {
    Route::get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('admin/blog', 'App\Http\Controllers\Admin\BlogController');
    Route::resource('admin/tasks', 'App\Http\Controllers\Admin\TaskController');
    Route::resource('admin/work-package', 'App\Http\Controllers\Admin\WorkPackageController');
    // Route::resource('admin/scholarship', 'App\Http\Controllers\Admin\ScholarshipsController');
    Route::resource('admin/team', 'App\Http\Controllers\Admin\TeamController');
    Route::resource('admin/publication', 'App\Http\Controllers\Admin\PublicationController');
    Route::resource('admin/newsletter', 'App\Http\Controllers\Admin\NewsletterController');
    Route::resource('admin/research-profile', 'App\Http\Controllers\Admin\ResearchProfileController');
    Route::resource('admin/farm', 'App\Http\Controllers\Admin\FarmController');
    Route::resource('admin/farmer', 'App\Http\Controllers\Admin\FarmerController');

    // Admin route for scholarship
     Route::resource('admin/scholarship', 'App\Http\Controllers\Admin\ScholarshipsController');


    //added for dashboard_page
    Route::resource('admin/dashboard', 'App\Http\Controllers\Admin\DashboardController');
    Route::resource('admin/hive', 'App\Http\Controllers\Admin\HiveController');
    Route::resource('admin/hivedata', 'App\Http\Controllers\Admin\HiveDataController');
    Route::resource('admin/videodata', 'App\Http\Controllers\Admin\HiveVideoController');
    
    Route::resource('admin/photodata', 'App\Http\Controllers\Admin\HivePhotoController');
    Route::resource('admin/audiodata', 'App\Http\Controllers\Admin\HiveAudioController');
    Route::resource('admin/temperaturedata', 'App\Http\Controllers\Admin\HiveTemperatureController');
    Route::resource('admin/humiditydata', 'App\Http\Controllers\Admin\HiveHumidityController');
    Route::resource('admin/weightdata', 'App\Http\Controllers\Admin\HiveWeightController');
    Route::resource('admin/carbondioxidedata', 'App\Http\Controllers\Admin\HiveCarbondioxideController');
    // add for VOC here 
    Route::resource('admin/vocdata', 'App\Http\Controllers\Admin\VOCController');

    Route::get('/vocdata/export', [VOCController::class, 'export'])->name('voc.export');


//gallery route for the admin side
    Route::get('/admin/gallery', 'App\Http\Controllers\Admin\GalleryController@index');
    Route::post('/admin/event','App\Http\Controllers\Admin\GalleryController@update');

 //hive controllers
    Route::post('/edithive','App\Http\Controllers\Admin\HiveController@update');
});

                /* ------------HIVE DATA------------------*/

/* ------------TEMPERATURE------------------*/
Route::get('/hive_data/{id}', [App\Http\Controllers\Admin\HiveDataController::class, 'temperatureDefault']);
Route::get('hive_data/temperature_data/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'getTemperatureData']);
Route::get('/hive_data/temperature_data_default/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'temperatureDefault']);

/* ------------HUMIDITY------------------*/
Route::get('/hive_data/humidity_data_default/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'humidityDefault']);
Route::get('/hive_data/humidity_data/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'getHumidityData']);

/* ------------CARBONDIOXIDE------------------*/
Route::get('/hive_data/carbondioxide_data_default/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'carbondioxide_default']);
Route::get('/hive_data/carbondioxide_data/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'getCarbondioxide_data']);

/* ------------WEIGTHT------------------*/
Route::get('/hive_data/weight_data_default/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'weightDefault']);
Route::get('/hive_data/weight_data/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'getWeightData']);

/* ------------TEMPERATURE Vs HUMIDITY------------------*/
Route::get('/hive_data/tempHumidity_data_default/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'tempHumidity_default']);
Route::get('/hive_data/tempHumidity_data/{hive}', [App\Http\Controllers\Admin\HiveDataController::class, 'getTempHumidity']);



/*--------------------------------VIBRATIONS---------------*/
Route::get('/hive_data/hiveVibration_data_default/{hive_id}',[App\Http\Controllers\HiveVibrationsController::class,'plot']);

// Route::get('/hive/vibration/{id}', [App\Http\Controllers\HiveVibrationsController::class, 'vibrationDefault'])->name('hive.vibration');
// Route::get('/hive/vibration-data/{hive}', [App\Http\Controllers\HiveVibrationsController::class, 'getVibrationData'])->name('hive.vibration.data');



Route::get('/individual_newsletter/{id}', [App\Http\Controllers\DisplayIndividualNewsletterController::class, 'show']);

Route::get('displaynewsletter', [App\Http\Controllers\DisplayNewsletterController::class, 'displayNewsletter']);
Route::get('displaypublication', [App\Http\Controllers\DisplayPublicationController::class, 'displayPublication']);
// Route::get('/displayevent', [App\Http\Controllers\DisplayEventController::class, 'displayEvent']);

Route::get('/displayevent', [App\Http\Controllers\GalleryController::class, 'index']);

// Scholarships
Route::get('/displayscholarships', [ScholarshipDisplayController::class, 'index'])->name('scholarships');
Route::get('/scholarships/{id}', [ScholarshipsController::class, 'show'])->name('scholarships.show');
Route::get('/scholarship/download-instructions/{id}', [ScholarshipDisplayController::class, 'downloadInstructionsPdf'])
    ->name('scholarship.downloadInstructions');


// Feedback routes
Route::post('/feedback', [App\Http\Controllers\Admin\FeedbackController::class, 'store'])->name('feedback.store');

Route::middleware('auth:web')->group(function () {
    Route::get('/admin/feedback', [App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('admin.feedback');
    Route::delete('/admin/feedback/{id}', [App\Http\Controllers\Admin\FeedbackController::class, 'destroy'])->name('feedback.destroy');
});


// Route::get('/mastersscholarship-uganda', [App\Http\Controllers\Admin\MastersController::class, 'uganda'])->name('mastersscholarship-uganda');
// Route::get('/mastersscholarship-sudan', [App\Http\Controllers\Admin\MastersController::class, 'sudan'])->name('mastersscholarship-sudan');
// Route::get('/mastersscholarship-tanzania', [App\Http\Controllers\Admin\MastersController::class, 'tanzania'])->name('mastersscholarship-tanzania');

// Route::get('/phdscholarship-uganda', [App\Http\Controllers\Admin\PhdController::class, 'uganda'])->name('phdscholarship-uganda');
// Route::get('/phdscholarship-sudan', [App\Http\Controllers\Admin\PhdController::class, 'sudan'])->name('phdscholarship-sudan');
// Route::get('/phdscholarship-tanzania', [App\Http\Controllers\Admin\PhdController::class, 'tanzania'])->name('phdscholarship-tanzania');

Route::get('/mastersprofile-uganda', [App\Http\Controllers\WebsiteController::class, 'uganda'])->name('mastersprofile-uganda');
Route::get('/mastersprofile-sudan', [App\Http\Controllers\WebsiteController::class, 'sudan'])->name('mastersprofile-sudan');
Route::get('/mastersprofile-tanzania', [App\Http\Controllers\WebsiteController::class, 'tanzania'])->name('mastersprofile-tanzania');

Route::get('/phdprofile-uganda', [App\Http\Controllers\Admin\ResearchProfileController::class, 'uganda'])->name('phdprofile-uganda');
Route::get('/phdprofile-sudan', [App\Http\Controllers\Admin\ResearchProfileController::class, 'sudan'])->name('phdprofile-sudan');
Route::get('/phdprofile-tanzania', [App\Http\Controllers\Admin\ResearchProfileController::class, 'tanzania'])->name('phdprofile-tanzania');
// work packages
Route::get('/workpackages-wp1', [App\Http\Controllers\WebsiteController::class, 'wp1'])->name('workpackages-wp1');
Route::get('/workpackages-wp2', [App\Http\Controllers\WebsiteController::class, 'wp2'])->name('workpackages-wp2');
Route::get('/workpackages-wp3', [App\Http\Controllers\WebsiteController::class, 'wp3'])->name('workpackages-wp3');
Route::get('/workpackages-wp4', [App\Http\Controllers\WebsiteController::class, 'wp4'])->name('workpackages-wp4');

Route::get('/gallery', [App\Http\Controllers\Admin\GalleryController::class, 'index'])->name('gallery');
Route::get('/admin/tasks/create/{id}', [App\Http\Controllers\Admin\TaskController::class, 'create']);

Route::get('/article/{id}', [App\Http\Controllers\ArticleController::class, 'index']);


//upload images in the ck editor
Route::post('ckeditor/upload', 'App\Http\Controllers\Admin\NewsletterController@upload')->name('upload');

// ...........................MAP...............................
Route::get('/farms/map/{id}', [App\Http\Controllers\MapController::class, 'displayMap'])->name('farms.hives');

Route::get('/farms/map/{id}', [App\Http\Controllers\MapController::class, 'displayMap'])->name('farms.hives');

Route::get('/map', [App\Http\Controllers\MapController::class, 'displayMap'])->name('map');



Route::get('/sensor-monitoring', [App\Http\Controllers\SensorMonitoringController::class, 'sensorMonitor']);
// Route::get('admin/power-monitoring', [App\Http\Controllers\Admin\PowerMonitoringController::class, 'powerMonitor']);
// .........................POWER MONITORING...................................
// Route::get('/admin/power-monitoring/index/{hive_id}', [App\Http\Controllers\PowerMonitoringController::class, 'batteryDefault']);


/*----------------------------- DOWNLOAD EXCEL EXPORT ROUTES---------------------*/


Route::get('admin/temperature/export', [App\Http\Controllers\Admin\HiveTemperatureController::class, 'export'])->name('temperature.export');

Route::get('admin/humidity/export', [App\Http\Controllers\Admin\HiveHumidityController::class, 'export'])->name('humidity.export');

Route::get('admin/carbondioxide/export', [App\Http\Controllers\Admin\HiveCarbondioxideController::class, 'export'])->name('carbondioxide.export');

Route::get('weights/export', [App\Http\Controllers\Admin\HiveWeightController::class, 'export'])->name('weights.export');

/*------------------------------------ThingSpeak Battery Monitoring--------------*/

// Route::get('/sensor-data', [App\Http\Controllers\ThingSpeakController::class, 'index']);
// Route::get('/fetch-sensor-data', [App\Http\Controllers\ThingSpeakController::class, 'fetchData']);
Route::get('/admin/thingspeak-monitoring/{hive_id}', [App\Http\Controllers\ThingSpeakController::class, 'showMonitoring'])
     ->name('thingspeak.monitoring');

/*----------------------------------------analytics--------------------------------------------------*/
Route::get('analytics/weight_analytics',[App\Http\Controllers\WeightAnalyticsController::class,'index']);
Route::get('analytics/data_reports',[App\Http\Controllers\Admin\DataReportController::class,'index']);
Route::get('analytics/temperature_humidity',[App\Http\Controllers\TemperatureAnalyticsController::class,'index']);

Route::get('website/gallery', [GalleryController::class, 'index']);

// Route::get('/thingspeak-data', [ThingSpeakController2::class,'fetchAndStoreData']);


Route::get('/battery-readings/{hiveId}', [BatteryReadingController::class, 'showData']);

Route::get('/admin/work-package/{id}/edit', 'WorkPackageController@edit');
Route::patch('/admin/work-package/{id}', 'WorkPackageController@update');


Route::get('/analytics/data_reports', [DataReportController::class, 'index']);
Route::post('/generate-local-report', [DataReportController::class, 'generateReport']);

Route::post('/generate-local-report', [DataReportController::class, 'generateReport']);


// gallery routes 
// Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
// Route::get('/gallery/{gallery}', [GalleryController::class, 'show'])->name('gallery.show');
Route::get('/displaygallery', [GalleryController::class, 'index'])->name('gallery.display');
// routes/web.php
// Route::get('/displayevent', [GalleryController::class, 'index']);


Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

// Gallery interns 
Route::resource('admin/gallery_interns', \App\Http\Controllers\Admin\GalleryInternController::class);

#team page route
Route::get('team', [TeamController::class, 'team'])->name('team');
