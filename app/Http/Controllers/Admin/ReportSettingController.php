<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportSetting;
use App\Services\HiveReportMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReportSettingController extends Controller
{
    /**
     * Show the report email settings form.
     */
    public function edit()
    {
        $settings = ReportSetting::current();

        return view('admin.report_settings.edit', compact('settings'));
    }

    /**
     * Save the SMTP settings and the recipient list.
     */
    public function update(Request $request)
    {
        $recipients = $this->parseRecipients($request->input('recipients'));

        $validator = Validator::make(
            array_merge($request->all(), ['recipient_list' => $recipients]),
            [
                'smtp_host' => 'required|string|max:255',
                'smtp_port' => 'required|integer|min:1|max:65535',
                'smtp_username' => 'required|email|max:255',
                'smtp_password' => $this->passwordRule(),
                'smtp_encryption' => 'nullable|in:tls,ssl',
                'from_address' => 'required|email|max:255',
                'from_name' => 'nullable|string|max:255',
                'recipient_list' => 'required|array|min:1',
                'recipient_list.*' => 'email',
                'send_time' => 'required|date_format:H:i',
            ],
            $this->credentialMessages() + [
                'recipient_list.required' => 'Enter at least one recipient email address.',
                'recipient_list.*.email' => 'One of the recipients is not a valid email address.',
            ]
        );

        if ($validator->fails()) {
            return redirect('admin/report-settings')->withErrors($validator)->withInput($request->except('smtp_password'));
        }

        $settings = ReportSetting::current();
        $settings->fill([
            'smtp_host' => $request->input('smtp_host'),
            'smtp_port' => $request->input('smtp_port'),
            'smtp_username' => $request->input('smtp_username'),
            'smtp_encryption' => $request->input('smtp_encryption'),
            'from_address' => $request->input('from_address'),
            'from_name' => $request->input('from_name'),
            'recipients' => $recipients,
            'auto_send' => $request->boolean('auto_send'),
            'send_time' => $request->input('send_time'),
        ]);

        // A blank password field means "keep the one already saved".
        if ($request->filled('smtp_password')) {
            $settings->smtp_password = $this->normalizePassword($request->input('smtp_password'));
        }

        $settings->save();

        return redirect('admin/report-settings')->with('flash_message', 'Report email settings saved.');
    }

    /**
     * Check that the SMTP details currently typed in the form (saved or not)
     * can connect and log in. Answers with JSON for the popup on the page.
     */
    public function testConnection(Request $request, HiveReportMailer $mailer)
    {
        $validator = Validator::make($request->all(), [
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|email|max:255',
            'smtp_password' => $this->passwordRule(),
            'smtp_encryption' => 'nullable|in:tls,ssl',
        ], $this->credentialMessages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // A blank password field means "use the one already saved".
        $password = $request->filled('smtp_password')
            ? $this->normalizePassword($request->input('smtp_password'))
            : ReportSetting::current()->smtp_password;

        try {
            $mailer->testConnection(
                $request->input('smtp_host'),
                $request->input('smtp_port'),
                $request->input('smtp_encryption'),
                $request->input('smtp_username'),
                $password
            );
        } catch (\Exception $e) {
            Log::warning('Report SMTP connection test failed: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => \Illuminate\Support\Str::limit($e->getMessage(), 300)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Connected to ' . $request->input('smtp_host') . ':' . $request->input('smtp_port')
                . ' and signed in as ' . $request->input('smtp_username') . '.',
        ]);
    }

    /**
     * Send a test copy of the report to the saved recipients.
     */
    public function sendTest(HiveReportMailer $mailer)
    {
        return $this->sendReport($mailer, true);
    }

    /**
     * Send the hives-needing-attention report to the saved recipients now.
     */
    public function sendNow(HiveReportMailer $mailer)
    {
        return $this->sendReport($mailer, false);
    }

    private function sendReport(HiveReportMailer $mailer, $isTest)
    {
        $settings = ReportSetting::current();

        try {
            $count = $mailer->send($settings, $isTest);
        } catch (\Exception $e) {
            Log::error('Hive report email failed: ' . $e->getMessage());

            return redirect('admin/report-settings')->with('flash_error', 'Could not send the email: ' . $e->getMessage());
        }

        $to = count($settings->recipients) . ' recipient' . (count($settings->recipients) > 1 ? 's' : '');
        $message = $isTest
            ? "Test email sent to $to."
            : "Report sent to $to ($count hive" . ($count === 1 ? '' : 's') . ' needing attention).';

        return redirect('admin/report-settings')->with('flash_message', $message);
    }

    /**
     * The app password is required unless one is already saved, in which
     * case leaving the field blank keeps it.
     */
    private function passwordRule()
    {
        return ReportSetting::current()->smtp_password
            ? 'nullable|string|max:255'
            : 'required|string|max:255';
    }

    private function credentialMessages()
    {
        return [
            'smtp_username.required' => 'Enter the email address used to sign in to the mail server.',
            'smtp_username.email' => 'The sign-in email must be a valid email address.',
            'smtp_password.required' => 'Enter the app password generated for this email account.',
        ];
    }

    /**
     * Gmail shows app passwords as four groups of four letters
     * ("abcd efgh ijkl mnop"); the spaces are not part of the password.
     */
    private function normalizePassword($password)
    {
        $password = trim($password);

        return preg_match('/^[a-z]{4}( [a-z]{4}){3}$/i', $password) ? str_replace(' ', '', $password) : $password;
    }

    /**
     * Recipients arrive as recipients[] (one per row added on the page); a
     * plain string separated by newlines / commas / semicolons is also accepted.
     */
    private function parseRecipients($value)
    {
        $value = is_array($value) ? implode("\n", $value) : (string) $value;
        $parts = preg_split('/[\s,;]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_map('strtolower', $parts)));
    }
}
