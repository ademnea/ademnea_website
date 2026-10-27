<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiveDailyFeature extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hive_daily_features';

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
        'colony_epoch',
        'date',
        'total',
        'peak',
        'peak_hour',
        'mean_of_means',
        'max_of_maxes',
        'mean_activity_fraction',
        'first_active_hour',
        'last_active_hour',
        'within_day_sd',
        'n_usable',
        'n_expected',
        'completeness',
        'n_low_confidence',
        'n_no_observation',
        'weather_factor',
        'expected_total',
        'ratio',
        'z',
        'apiary_total',
        'apiary_median',
        'share',
        'vs_median',
        'apiary_hive_count',
        'slope10',
        'model_version',
        'threshold_version',
    ];

    protected $casts = [
        'date'                   => 'date',
        'total'                  => 'float',
        'peak'                   => 'float',
        'peak_hour'              => 'integer',
        'completeness'           => 'float',
        'n_usable'               => 'integer',
        'n_expected'             => 'integer',
        'weather_factor'         => 'float',
        'expected_total'         => 'float',
        'ratio'                  => 'float',
        'z'                      => 'float',
        'share'                  => 'float',
        'vs_median'              => 'float',
        'slope10'                => 'float',
    ];

    /**
     * Get the hive that owns the daily feature row.
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
     * Days with enough usable captures to draw a conclusion from. Below this
     * the day is reported as insufficient_data rather than analysed - an
     * alert built on a quarter of a day is worse than no alert.
     */
    public function scopeConclusive($query, $minCompleteness = 0.30)
    {
        return $query->where('completeness', '>=', $minCompleteness);
    }

    /**
     * Restrict to a date range, inclusive.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
