<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeeCount extends Model
{
    use HasFactory;

    protected $casts = [
        'mean_count'        => 'float',
        'max_count'         => 'integer',
        'activity_fraction' => 'float',
        'frames_analyzed'   => 'integer',
        'attempts'          => 'integer',
        'processed_at'      => 'datetime',
    ];

    public function hiveVideo()
    {
        return $this->belongsTo(HiveVideo::class);
    }

    /**
     * Only rows the dispatcher has finished analysing. Every metric column
     * is null until then - see DASHBOARD-INTEGRATION.md §4.1.
     */
    public function scopeAnalysed($query)
    {
        return $query->where('processing_status', 'done');
    }
}
