<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;
use App\Models\Hive;

use App\Models\Vibration;

class HiveVibrationsController extends Controller
{

    public function plot(Request $request)
        {
            $hiveId = $request->route('hive_id');

            // First, get the Hive object
            $hive = Hive::findOrFail($hiveId);

            // Define the directory where the CSV files are stored
            $directory = public_path('hivevibration');

            // the latest CSV file that matches the hive ID
            // The folder is filled by MODULES/register_hivevibration.py and may not exist yet (e.g. locally)
            $csvFiles = File::isDirectory($directory) ? File::files($directory) : [];
            $csvFilePath = null;
            foreach ($csvFiles as $file) {
                if (strpos($file->getFilename(), (string)$hiveId) !== false && $file->getExtension() == 'csv') {
                    $csvFilePath = $file->getRealPath();
                    break;
                }
            }

            // No CSV yet for this hive: the page shows an empty state instead of the charts
            $csvContent = ($csvFilePath && file_exists($csvFilePath)) ? file_get_contents($csvFilePath) : null;

            return view('admin.hivegraphs.vibrations', [
                'data' => $csvContent === null ? null : json_encode($csvContent),
                'hive_id' => $hiveId,
                'hive' => $hive
            ]);
        }
    }
