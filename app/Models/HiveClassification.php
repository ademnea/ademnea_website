<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiveClassification extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hive_classifications';

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
        'size_class',
        'condition_class',
        'size_ratio',
        'trend_pct_per_day',
        'pct_of_baseline',
        'effective_from',
        'effective_to',
        'evidence',
        'threshold_version',
    ];

    protected $casts = [
        'effective_from'     => 'date',
        'effective_to'       => 'date',
        'size_ratio'         => 'float',
        'trend_pct_per_day'  => 'float',
        'pct_of_baseline'    => 'float',
    ];

    /**
     * Size classes, largest first. A level, not a judgement of health.
     */
    public const SIZE_CLASSES = [
        'strong',
        'average',
        'weak',
        'very_weak',
        'unclassified',
    ];

    /**
     * Condition classes. A trajectory. None of these asserts a cause.
     */
    public const CONDITION_CLASSES = [
        'establishing',
        'stable',
        'declining',
        'critical',
        'silent',
        'unknown',
    ];

    /**
     * Get the hive that owns the classification.
     */
    public function hive()
    {
        return $this->belongsTo(Hive::class);
    }

    /**
     * The classification currently in force.
     */
    public function scopeCurrent($query)
    {
        return $query->whereNull('effective_to');
    }

    /**
     * A short label for display, e.g. "Average size, declining".
     *
     * @return string
     */
    public function getLabelAttribute()
    {
        $size = str_replace('_', ' ', $this->size_class);
        $cond = str_replace('_', ' ', $this->condition_class);

        return ucfirst($size) . ' size, ' . $cond;
    }
}
