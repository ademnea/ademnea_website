<?php

namespace App\Http\Controllers\HiveData;

use Response;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\HiveTemperature;
use App\Models\HiveHumidity;
use App\Models\HiveCarbondioxide;

use App\Charts\HiveDataChart;

class HiveDataController extends Controller
{
    //
    // public function show($id)
    // {

    //     $newsletter = Newsletter::findOrFail($id);

    //     return view('website.individual_newsletter', ['newsletter'=> $newsletter]);
    // }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */

    public function hiveData($id) {

    $hiveTemperatureRecords = HiveTemperature::select('created_at', 'record')->get();

    $hiveTemperatureHoney = [];
    $hiveTemperatureBrood = [];
    $hiveTemperatureClimate = [];
    $labels = [];

    foreach ($hiveTemperatureRecords as $record) {
        $dataArray = explode('*', $record->record);

        // Skip record if any value in $dataArray equals 2
        if (in_array('2', $dataArray)) {
            continue;
        }

        $hiveTemperatureHoney[] = $dataArray[0];
        $hiveTemperatureBrood[] = $dataArray[1];
        $hiveTemperatureClimate[] = $dataArray[2];
        $labels[] = $record->created_at;

    }
}

}


// $Tchart = new HiveDataChart;
// $Tchart->labels($labels);

// $Tchart->dataset('Honey', 'line', $hiveTemperatureHoney)
//       ->Color('blue')
//       ->options([
//         'backgroundColor' => 'transparent',
//          'lineTension' => 0,
//          'pointRadius' => 2, // Set point radius to 1
//          'pointHitRadius' => 5, // Set point hit radius to 5
//          'scales' => [
//             'yAxes' => [[
//                 'beginAtZero' => false,
//                 'suggestedMin' => '20',
//                 'suggestedMax' => '50',
//                 'stepSize' => 5
//             ]]
//         ]

//       ]);

// $Tchart->dataset('Brood', 'line', $hiveTemperatureBrood)
//       ->Color('red')
//       ->options([
//         'backgroundColor' => 'transparent',
//          'lineTension' => 0,
//          'pointRadius' => 2, // Set point radius to 1
//          'scales' => [
//             'yAxes' => [[
//                 'beginAtZero' => false,
//                 'suggestedMin' => '20',
//                 'suggestedMax' => '50',
//                 'stepSize' => 5
//             ]]
//         ]

//       ]);

// $Tchart->dataset('Climate', 'line', $hiveTemperatureClimate)
//       ->Color('green')
//       ->options([
//         'backgroundColor' => 'transparent',
//          'lineTension' => 0,
//          'pointRadius' => 2, // Set point radius to 1
//          'pointHitRadius' => 5, // Set point hit radius to 5
//          'scales' => [
//             'yAxes' => [[
//                 'beginAtZero' => false,
//                 'suggestedMin' => '20',
//                 'suggestedMax' => '50',
//                 'stepSize' => 5
//             ]]
//         ]

//       ]);


// //          $hiveTemperature = HiveTemperature::pluck('record', 'created_at');
//     // $hiveTemp = HiveTemperature::findorfail($id);

//     // $hiveTemperature = $hiveTemp::pluck('record', 'created_at');




//     // return $hiveTemperature->keys();
//     // return $hiveTemperature->values();

//     //       $Tchart = new HiveDataChart;

// //           $Tchart->labels($hiveTemperature->keys());

//     //$Tchart->dataset('Temperature', 'line', $hiveTemperature->values());

//     //      $Tchart->dataset('Temperature', 'line', $hiveTemperature->values())->Color('green')->options(['backgroundColor' => 'transparent']);

//       $hiveHumidityRecords = HiveHumidity::select('created_at', 'record')->get();

// $hiveHumidityHoney = [];
// $hiveHumidityBrood = [];
// $hiveHumidityClimate = [];
// $labels = [];

// foreach ($hiveHumidityRecords as $record) {
//     $dataArray = explode('*', $record->record);

//     // Skip record if any value in $dataArray equals 2
//     if (in_array('2', $dataArray)) {
//         continue;
//     }

//     $hiveHumidityHoney[] = $dataArray[0];
//     $hiveHumidityBrood[] = $dataArray[1];
//     $hiveHumidityClimate[] = $dataArray[2];

//     // check if the value in the record is 2, skip the current record if true
//     // if ($hiveHumidityHoney == 2 || $hiveHumidityBrood == 2 || $hiveHumidityClimate == 2) {
//     //     continue;
//     // }

//     // $hiveHumidityHoney[] = $hiveHumidityHoney;
//     // $hiveHumidityBrood[] = $hiveHumidityBrood;
//     // $hiveHumidityClimate[] = $hiveHumidityClimate;
//     $labels[] = $record->created_at;
// }

// $Hchart = new HiveDataChart;
// $Hchart->labels($labels);

// $Hchart->dataset('Honey', 'line', $hiveHumidityHoney)
//       ->Color('blue')
//       ->options([
//         'backgroundColor' => 'transparent',
//          'lineTension' => 0,
//          'pointRadius' => 2, // Set point radius to 1
//          'pointHitRadius' => 5, // Set point hit radius to 5
//          'scales' => [
//             'yAxes' => [[
//                 'beginAtZero' => false,
//                 'suggestedMin' => '20',
//                 'suggestedMax' => '50',
//                 'stepSize' => 5
//             ]]
//         ]

//       ]);

// $Hchart->dataset('Brood', 'line', $hiveHumidityBrood)
//       ->Color('red')
//       ->options([
//         'backgroundColor' => 'transparent',
//          'lineTension' => 0,
//          'pointRadius' => 2, // Set point radius to 1
//          'pointHitRadius' => 5, // Set point hit radius to 5
//          'scales' => [
//             'yAxes' => [[
//                 'beginAtZero' => false,
//                 'suggestedMin' => '20',
//                 'suggestedMax' => '50',
//                 'stepSize' => 5
//             ]]
//         ]

//       ]);

// $Hchart->dataset('Climate', 'line', $hiveHumidityClimate)
//       ->Color('green')
//       ->options([
//         'backgroundColor' => 'transparent',
//          'lineTension' => 0,
//          'pointRadius' => 2, // Set point radius to 1
//          'pointHitRadius' => 5, // Set point hit radius to 5
//          'scales' => [
//             'yAxes' => [[
//                 'beginAtZero' => false,
//                 'suggestedMin' => '20',
//                 'suggestedMax' => '50',
//                 'stepSize' => 5
//             ]]
//         ]

//       ]);






//     //     $hiveHumidity = HiveHumidity::pluck('record', 'created_at');
//     // $hiveHum = HiveTemperature::findorfail($id);

//     // $hiveHumidity = $hiveHum::pluck('record', 'created_at');


//     //    $Hchart = new HiveDataChart;

//     //    $Hchart->labels($hiveHumidity->keys());

//     //$Hchart->dataset('Humidity', 'line', $hiveHumidity->values());

//     //     $Hchart->dataset('Humidity', 'line', $hiveHumidity->values())->Color('blue')->options(['backgroundColor' => 'transparent']);
//     //$Hchart->dataset('Humidity', 'line', $hiveHumidity->values())->Color('red')->options(['backgroundColor' => 'transparent']);

//     //->backgroundColor('red')

//     $hiveCarbondioxide = HiveCarbondioxide::pluck('record', 'created_at');
//     // $hiveCar = HiveTemperature::findorfail($id);

//     // $hiveCarbondioxide = $hiveCar::pluck('record', 'created_at');


//     $Cchart = new HiveDataChart;

//     $Cchart->labels($hiveCarbondioxide->keys());

//     $Cchart->dataset('Carbondioxide', 'line', $hiveCarbondioxide->values())->Color('red')->options(['backgroundColor' => 'transparent']);


//     //$Cchart->dataset('Carbondioxide', 'scatter', $hiveCarbondioxide->values())->backgroundColor('red')->options([
//     //    'showLine' => true,
//     //]);

//     return view('website.hive_data', compact('Tchart', 'Hchart', 'Cchart'));
//     }

// //      public function show($id)
// //      {

// //     $hiveTemperature = HiveTemperature::findOrFail($id);

// //     return view('website.hive_data', ['hiveTemperature'=> $hiveTemperature]);
// //  }


// }
