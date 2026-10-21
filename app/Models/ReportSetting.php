<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSetting extends Model
{
    use HasFactory;

    protected $table = 'report_settings';

    protected $fillable = [
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_address',
        'from_name',
        'recipients',
        'auto_send',
        'send_time',
        'last_sent_at',
    ];

    protected $hidden = ['smtp_password'];

    protected $casts = [
        'smtp_password' => 'encrypted',
        'recipients' => 'array',
        'auto_send' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    /**
     * There is only ever one row of report settings.
     */
    public static function current()
    {
        return static::first() ?? new static([
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'recipients' => [],
            'auto_send' => false,
            'send_time' => '08:00',
        ]);
    }

    /**
     * Whether enough has been filled in to attempt sending a report.
     */
    public function isConfigured()
    {
        return !empty($this->smtp_host)
            && !empty($this->smtp_port)
            && !empty($this->smtp_username)
            && !empty($this->smtp_password)
            && !empty($this->from_address)
            && !empty($this->recipients);
    }
}
