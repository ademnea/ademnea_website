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
    // Validate the incoming request
    $request->validate([
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer|between:2022,2025',
        'hive_id' => 'required|string',
    ]);

    $month = $request->input('month');
    $year = $request->input('year');
    $hive_id = $request->input('hive_id');

    // Define paths
    $pythonBinPath = base_path('MODULES/report_scripts/venv/bin/python');
    $pythonScriptPath = base_path('MODULES/report_scripts/main.py');
    $outputDir = base_path('MODULES/generated_report');
    $csvDir = base_path('MODULES/csv_data');

    // Build CSV file paths
    $co2File = "{$csvDir}/hive_carbondioxide_{$hive_id}.csv";
    $weightFile = "{$csvDir}/hive_weights_{$hive_id}.csv";
    $tempFile = "{$csvDir}/hive_temperatures_{$hive_id}.csv";
    $humidityFile = "{$csvDir}/hive_humidity_{$hive_id}.csv";

    // Ensure output directory exists
    Storage::makeDirectory('public/reports');

    // Create the process using the virtualenv python binary
    $process = new Process([
        $pythonBinPath,
        $pythonScriptPath,
        $co2File,
        $weightFile,
        $tempFile,
        $humidityFile,
        $year,
        $month,
        $hive_id,
        $outputDir
    ]);

    $process->setTimeout(60);

    try {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        $pdfFile = "public/reports/Monthly_Report_{$hive_id}_{$year}_{$monthStr}.pdf";

        if (!Storage::exists($pdfFile)) {
            return response()->json(['success' => false, 'message' => 'Report generation failed'], 500);
        }

        $plots = [
            'co2_monthly_trend.png',
            'co2_diurnal_variation.png',
            'weight_monthly_trend.png',
            'temperature_trend.png',
            'humidity_trend.png',
            'combined_trends.png',
            'correlation_heatmap.png'
        ];

        $plotUrls = [];

        foreach ($plots as $plot) {
            $plotPath = "public/reports/{$plot}";
            if (Storage::exists($plotPath)) {
                $plotUrls[] = Storage::url($plotPath);
            }
        }

        return response()->json([
            'success' => true,
            'download_url' => Storage::url($pdfFile),
            'plots' => $plotUrls
        ]);

    } catch (ProcessFailedException $e) {
        \Log::error('Python execution failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error running report script'
        ], 500);
    }
}

}
