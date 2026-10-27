<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiveInsight extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hive_insights';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hive_id',
        'farm_id',
        'date',
        'verdict',
        'confidence',
        'message',
        'action',
        'evidence',
        'escalate',
        'acknowledged_at',
        'acknowledged_by',
        'threshold_version',
    ];

    protected $casts = [
        'date'            => 'date',
        'escalate'        => 'boolean',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Verdicts the reconciliation ladder can produce, in evaluation order.
     * First match wins, so order matters: the guards run before anything is
     * attributed to a colony.
     */
    public const VERDICTS = [
        'monitor_offline',
        'insufficient_data',
        'apiary_wide',
        'weather_confined',
        'hive_anomaly_sudden',
        'hive_trend_decline',
        'hive_spike',
        'normal',
    ];

    /**
     * Verdicts worth showing a farmer. The rest are either silence or
     * bookkeeping.
     */
    public const REPORTABLE = [
        'monitor_offline',
        'hive_anomaly_sudden',
        'hive_trend_decline',
        'hive_spike',
    ];

    /**
     * Get the hive that owns the insight.
     */
    public function hive()
    {
        return $this->belongsTo(Hive::class);
    }

    /**
     * Get the farm the hive belongs to.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Only insights that warrant the farmer's attention.
     */
    public function scopeReportable($query)
    {
        return $query->whereIn('verdict', self::REPORTABLE);
    }

    /**
     * Insights nobody has dealt with yet.
     */
    public function scopeOpen($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    /**
     * Those where weather, peers and data faults have all been ruled out.
     * These are the only ones that justify looking at sensor data.
     */
    public function scopeEscalated($query)
    {
        return $query->where('escalate', true);
    }
}
