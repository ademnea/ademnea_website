<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiveBaseline extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hive_baselines';

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
        'colony_epoch',
        'baseline_type',
        'window_start',
        'window_end',
        'days_used',
        'base_daily',
        'base_daily_sd',
        'base_share',
        'residual_sd',
        'hourly_profile',
        'model_version',
    ];

    protected $casts = [
        'window_start'    => 'date',
        'window_end'      => 'date',
        'days_used'       => 'integer',
        'base_daily'      => 'float',
        'base_daily_sd'   => 'float',
        'base_share'      => 'float',
        'residual_sd'     => 'float',
        'hourly_profile'  => 'array',
    ];

    /**
     * Get the hive that owns the baseline.
     */
    public function hive()
    {
        return $this->belongsTo(Hive::class);
    }

    /**
     * The trailing window the detectors compare against.
     */
    public function scopeRolling($query)
    {
        return $query->where('baseline_type', 'rolling');
    }

    /**
     * The frozen first-week window, for comparison against establishment.
     */
    public function scopeInitial($query)
    {
        return $query->where('baseline_type', 'initial');
    }
}
