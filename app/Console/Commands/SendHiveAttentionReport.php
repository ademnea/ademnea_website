<?php

namespace App\Console\Commands;

use App\Models\ReportSetting;
use App\Services\HiveHealthService;
use App\Services\HiveReportMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendHiveAttentionReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hives:send-attention-report
                            {--scheduled : Only send if automatic sending is on, the daily send time has passed and today\'s report has not gone out yet}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email the hives-needing-attention report to the recipients set on the report settings page';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(HiveReportMailer $mailer, HiveHealthService $hiveHealthService)
    {
        $settings = ReportSetting::current();

        if ($this->option('scheduled')) {
            if (!$settings->auto_send || !$settings->isConfigured()) {
                return 0;
            }

            if (now()->format('H:i') < $settings->send_time) {
                return 0;
            }

            if ($settings->last_sent_at && $settings->last_sent_at->isToday()) {
                return 0;
            }

            if ($hiveHealthService->build()['counts']['attention'] === 0) {
                return 0;
            }
        }

        try {
            $count = $mailer->send($settings);
        } catch (\Exception $e) {
            Log::error('Hive report email failed: ' . $e->getMessage());
            $this->error('Could not send the report: ' . $e->getMessage());

            return 1;
        }

        $this->info("Report sent to " . count($settings->recipients) . " recipient(s): $count hive(s) needing attention.");

        return 0;
    }
}
