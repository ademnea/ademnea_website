<?php

namespace App\Services;

use App\Mail\HiveAttentionReport;
use App\Models\ReportSetting;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class HiveReportMailer
{
    const MAILER = 'report_smtp';

    protected $hiveHealthService;
    protected $reportPdf;

    public function __construct(HiveHealthService $hiveHealthService, HiveReportPdf $reportPdf)
    {
        $this->hiveHealthService = $hiveHealthService;
        $this->reportPdf = $reportPdf;
    }

    /**
     * Open a connection to the SMTP server and log in, without sending
     * anything. Throws if the server cannot be reached or rejects the login.
     *
     * @throws \Exception
     */
    public function testConnection($host, $port, $encryption, $username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new RuntimeException('Both the email address and the app password are required to sign in.');
        }

        $transport = new \Swift_SmtpTransport($host, (int) $port, $encryption ?: null);
        $transport->setTimeout(15);
        $transport->setUsername($username);
        $transport->setPassword($password);

        $transport->start();
        $transport->stop();
    }

    /**
     * Email the hives-needing-attention report to the configured recipients,
     * through the SMTP server the admin set up on the report settings page
     * (not the application's default mailer).
     *
     * @param  bool  $isTest  marks the email as a test and leaves last_sent_at alone
     * @return int  number of hives needing attention that were reported
     *
     * @throws \RuntimeException when the settings are incomplete
     * @throws \Exception when the SMTP server rejects or cannot be reached
     */
    public function send(ReportSetting $settings, $isTest = false)
    {
        if (!$settings->isConfigured()) {
            throw new RuntimeException('Report email settings are incomplete: SMTP host, port, sign-in email, app password, from address and at least one recipient are required.');
        }

        $health = $this->hiveHealthService->build();
        $hives = array_values(array_filter($health['rows'], function ($row) {
            return $row['status'] === 'attention';
        }));

        config(['mail.mailers.' . self::MAILER => [
            'transport' => 'smtp',
            'host' => $settings->smtp_host,
            'port' => $settings->smtp_port,
            'encryption' => $settings->smtp_encryption ?: null,
            'username' => $settings->smtp_username,
            'password' => $settings->smtp_password,
            'timeout' => 20,
            'auth_mode' => null,
        ]]);

        // Drop any mailer built from older settings earlier in this process.
        Mail::purge(self::MAILER);

        $pdf = $this->reportPdf->render($health, $isTest);

        $report = new HiveAttentionReport($hives, $health['counts'], HiveHealthService::STALE_THRESHOLD_HOURS, $isTest, $pdf);
        $report->from($settings->from_address, $settings->from_name ?: config('app.name'));

        Mail::mailer(self::MAILER)->to($settings->recipients)->send($report);

        if (!$isTest) {
            $settings->last_sent_at = now();
            $settings->save();
        }

        return count($hives);
    }
}
