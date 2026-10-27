<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hive extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hives';

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
        'latitude',
        'longitude',
        'farm_id',
        'connected',
        'colonized',
        'entrance_orientation',
        'hive_type',
        'colony_epoch',
        'colony_epoch_started_on',
        'site_group',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'connected'               => 'boolean',
        'colonized'               => 'boolean',
        'colony_epoch'            => 'integer',
        'colony_epoch_started_on' => 'date',
    ];

    /**
     * Get the farm that owns the hive.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the photos for the hive.
     */
    public function photos()
    {
        return $this->hasMany(HivePhoto::class);
    }

    /**
     * Get the audios for the hive.
     */
    public function audios()
    {
        return $this->hasMany(HiveAudio::class);
    }

    /**
     * Get the videos for the hive.
     */
    public function videos()
    {
        return $this->hasMany(HiveVideo::class);
    }


    /**
     * Get the inspections for the hive.
     */
    public function inspections()
    {
        return $this->hasMany(BeehiveInspection::class);
    }

    /**
     * Get the analysed bee counts for the hive.
     *
     * bee_counts has no hive_id of its own - it reaches a hive only through
     * hive_videos - so this goes through that table.
     */
    public function beeCounts()
    {
        return $this->hasManyThrough(
            BeeCount::class,
            HiveVideo::class,
            'hive_id',       // foreign key on hive_videos
            'hive_video_id', // foreign key on bee_counts
            'id',
            'id'
        );
    }

    /**
     * Get the daily analysis features for the hive.
     */
    public function dailyFeatures()
    {
        return $this->hasMany(HiveDailyFeature::class);
    }

    /**
     * Get the baselines for the hive.
     */
    public function baselines()
    {
        return $this->hasMany(HiveBaseline::class);
    }

    /**
     * Get the insights raised for the hive.
     */
    public function insights()
    {
        return $this->hasMany(HiveInsight::class);
    }

    /**
     * Get the classification history for the hive.
     */
    public function classifications()
    {
        return $this->hasMany(HiveClassification::class);
    }

    /**
     * Get the classification currently in force, if any.
     */
    public function currentClassification()
    {
        return $this->hasOne(HiveClassification::class)->whereNull('effective_to');
    }
}
