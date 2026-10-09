<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiveVideo extends Model
{
    use HasFactory;
    protected $fillable = ['path', 'hive_id'];


    protected static function boot()
      {
          parent::boot();

          static::creating(function ($video) {
              // Check if a photo with the same path and hive_id already exists
              $exists = self::where('hive_id', $video->hive_id)
                            ->where('path', $video->path)
                            ->exists();

              if ($exists) {
                  return false;
              }
          });
      }

    /**
     * Get the hive that owns the video.
     */
    public function hive()
    {
        return $this->belongsTo(Hive::class);
    }

        // HiveVideo.php

        public function beeCounts()
        {
            return $this->hasMany(BeeCount::class, 'hive_video_id');
        }

        public function latestBeeCount()
        {
            return $this->hasOne(BeeCount::class, 'hive_video_id')->latestOfMany();
        }

        /**
         * Like latestBeeCount(), but only ever resolves to a row the
         * dispatcher has finished analysing - safe to read mean_count,
         * max_count, activity_fraction etc. from. Legacy pre-pipeline rows
         * (processing_status = pending) never match this, by design - see
         * DASHBOARD-INTEGRATION.md §4.1-4.2.
         */
        public function latestAnalysedCount()
        {
            return $this->hasOne(BeeCount::class, 'hive_video_id')->analysed()->latestOfMany();
        }


}
