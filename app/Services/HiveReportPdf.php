<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class HiveReportPdf
{
    /**
     * Render the hive health report (summary, charts and tables) as a PDF.
     *
     * @param  array  $health  result of HiveHealthService::build()
     * @param  bool  $isTest
     * @return string  raw PDF bytes
     */
    public function render(array $health, $isTest = false)
    {
        $hives = array_values(array_filter($health['rows'], function ($row) {
            return $row['status'] === 'attention';
        }));

        // How many of the hives needing attention have each data type gone quiet
        $staleByType = array_fill_keys(array_values(HiveHealthService::DATA_TYPE_LABELS), 0);
        foreach ($hives as $row) {
            foreach ($row['stale_types'] as $stale) {
                $staleByType[$stale['label']]++;
            }
        }
        arsort($staleByType);

        // Hives needing attention per district
        $byDistrict = [];
        foreach ($hives as $row) {
            $district = $row['farm_district'];
            $byDistrict[$district] = ($byDistrict[$district] ?? 0) + 1;
        }
        arsort($byDistrict);

        return Pdf::loadView('pdf.hive_attention_report', [
            'hives' => $hives,
            'counts' => $health['counts'],
            'staleThresholdHours' => HiveHealthService::STALE_THRESHOLD_HOURS,
            'isTest' => $isTest,
            'typeLabels' => HiveHealthService::DATA_TYPE_LABELS,
            'staleByType' => $staleByType,
            'byDistrict' => $byDistrict,
        ])->setPaper('a4', 'landscape')
            // Embed only the glyphs used, otherwise the font alone makes the attachment ~850KB
            ->setOption('enable_font_subsetting', true)
            ->output();
    }

    /**
     * File name for the attachment, e.g. hive-report-2026-10-20.pdf
     */
    public static function filename()
    {
        return 'hive-report-' . now()->format('Y-m-d') . '.pdf';
    }
}
