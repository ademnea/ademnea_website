<?php

namespace App\Mail;

use App\Services\HiveReportPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HiveAttentionReport extends Mailable
{
    use Queueable, SerializesModels;

    public $hives;
    public $counts;
    public $staleThresholdHours;
    public $isTest;

    /** Raw PDF bytes; kept out of the view data. */
    protected $pdf;

    /**
     * @param  array  $hives  hive health rows needing attention
     * @param  array  $counts  hive counts per status
     * @param  int  $staleThresholdHours
     * @param  bool  $isTest
     * @param  string|null  $pdf  the full report, attached as a PDF
     */
    public function __construct(array $hives, array $counts, $staleThresholdHours, $isTest = false, $pdf = null)
    {
        $this->pdf = $pdf;
        $this->hives = $hives;
        $this->counts = $counts;
        $this->staleThresholdHours = $staleThresholdHours;
        $this->isTest = $isTest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $count = count($this->hives);
        $subject = $count > 0
            ? $count . ' hive' . ($count > 1 ? 's' : '') . ' needing attention'
            : 'All hives reporting normally';

        $this->subject(($this->isTest ? '[TEST] ' : '') . 'AdeMNEA hive report: ' . $subject)
            ->view('emails.hive_attention_report');

        if ($this->pdf) {
            $this->attachData($this->pdf, HiveReportPdf::filename(), ['mime' => 'application/pdf']);
        }

        return $this;
    }
}
