<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Hive;
use App\Models\Farm;
use App\Models\Bee;

class DataReportController extends Controller
{
    /**
     * Show the report generation view.
     */
    public function index()
    {
        $hive_id = Hive::where('farm_id', session('farm_id'))->first()->id ?? 'No Hive';
        return view('admin.datareports.index', compact('hive_id'));
    }

    /**
     * Handle report generation via Python script.
     */
public function generateReport(Request $request)
{
    try {
        $request->validate([
            'start_month' => 'required|integer|between:1,12',
            'end_month'   => 'required|integer|between:1,12',
            'year'        => 'required|integer|between:2022,2025',
            'hive_id'     => 'required|string',
            'attributes'  => 'required|array|min:1',
        ]);

        $startMonth = $request->input('start_month');
        $endMonth   = $request->input('end_month');
        $year       = $request->input('year');
        $hiveId     = $request->input('hive_id');
        $attributes = $request->input('attributes');

        $pythonBin   = base_path('MODULES/report_scripts/venv/bin/python');
        $script      = base_path('MODULES/attribute_scripts/main.py');
        $outputDir   = base_path('MODULES/generated_report');
        $downloadDir = storage_path('app/public/reports');
        $csvDataDir  = base_path('MODULES/csv_data');

        $filePatterns = [
            'co2'         => "hive_carbondioxide_{$hiveId}.csv",
            'temperature' => "hive_temperatures_{$hiveId}.csv",
            'humidity'    => "hive_humidity_{$hiveId}.csv",
            'weight'      => "hive_weights_{$hiveId}.csv"
        ];

        // Build Python command
        $command = escapeshellcmd("{$pythonBin} {$script} " .
            "--year {$year} " .
            "--start_date " . sprintf("%02d/%d", $startMonth, $year) . " " .
            "--end_date " . sprintf("%02d/%d", $endMonth, $year) . " " .
            "--attributes " . implode(' ', $attributes) . " " .
            "--co2_file {$csvDataDir}/{$filePatterns['co2']} " .
            "--temp_file {$csvDataDir}/{$filePatterns['temperature']} " .
            "--humidity_file {$csvDataDir}/{$filePatterns['humidity']} " .
            "--weight_file {$csvDataDir}/{$filePatterns['weight']} " .
            "--hive_id {$hiveId} " .
            "--output_dir {$outputDir} " .
            "--download_dir {$downloadDir}"
        );

        \Log::info("Running command: {$command}");
        $processOutput = shell_exec("{$command} 2>&1");
        \Log::info("Python output: {$processOutput}");

        if (strpos($processOutput, 'ERROR') !== false) {
            throw new \Exception("Python script error: {$processOutput}");
        }

        // Map frontend attribute names to Python names and capitalize
        $attributeMap = [
            'co2'         => 'Carbondioxide',
            'temperature' => 'Temperature',
            'humidity'    => 'Humidity',
            'weight'      => 'Weight'
        ];

        $pdfFiles = [];
        foreach ($attributes as $attr) {
            if (!isset($attributeMap[$attr])) continue;

            $attrName = $attributeMap[$attr];
            $pdfFilename = "{$attrName}_Report_{$hiveId}_{$year}_" .
                date("F", mktime(0, 0, 0, $startMonth, 1)) .
                "_to_" . $year . "_" . date("F", mktime(0, 0, 0, $endMonth, 1)) .
                ".pdf";

            $pdfPath = "public/reports/{$pdfFilename}";
            if (Storage::exists($pdfPath)) {
                $pdfFiles[] = asset("storage/reports/{$pdfFilename}");
            }
        }

        if (empty($pdfFiles)) {
            throw new \Exception('No reports generated.');
        }

        // Single PDF or ZIP
        if (count($pdfFiles) === 1) {
            return response()->json(['success' => true, 'download_url' => $pdfFiles[0]]);
        } else {
            $zip = new \ZipArchive();
            $zipName = "HiveReport_{$hiveId}_{$year}_{$startMonth}_{$endMonth}.zip";
            $zipPath = storage_path("app/public/reports/{$zipName}");
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file.');
            }
            foreach ($pdfFiles as $fileUrl) {
                $filePath = str_replace(asset(''), base_path('public/'), $fileUrl);
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();
            return response()->json(['success' => true, 'download_url' => asset("storage/reports/{$zipName}")]);
        }

    } catch (\Exception $e) {
        \Log::error("Report generation failed: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

}
