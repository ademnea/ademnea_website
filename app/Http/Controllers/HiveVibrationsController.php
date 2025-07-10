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
            $csvFiles = File::files($directory);
            $csvFilePath = null;
            foreach ($csvFiles as $file) {
                if (strpos($file->getFilename(), (string)$hiveId) !== false && $file->getExtension() == 'csv') {
                    $csvFilePath = $file->getRealPath();
                    break;
                }
            }

            // Check if the CSV file exists
            if (!$csvFilePath || !file_exists($csvFilePath)) {
                return response()->json(['error' => 'CSV file not found for this hive ID']);
            }

            // Read the file content
            $csvContent = file_get_contents($csvFilePath);

            return view('admin.hivegraphs.vibrations', [
                'data' => json_encode($csvContent),
                'hive_id' => $hiveId,
                'hive' => $hive
            ]);
        }
    }
