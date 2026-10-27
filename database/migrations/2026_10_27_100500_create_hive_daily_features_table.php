<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per hive per day. This is the atom the whole analysis rests on -
 * charts, detectors, classifications and insights all read from here, never
 * from bee_counts directly.
 *
 * Deliberately source-agnostic: nothing here assumes the numbers came from
 * video. When sensor modalities are re-introduced they add columns to this
 * table and everything above it keeps working.
 *
 * No monthly rollup table. Fifteen hives over a year is a few thousand rows;
 * the (hive_id, date) index makes month and season queries cheap, and a
 * rollup would only be another thing to keep in sync.
 */
class CreateHiveDailyFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hive_daily_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hive_id');
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedInteger('colony_epoch')->default(1);
            $table->date('date');

            // --- level -------------------------------------------------
            $table->float('total')->nullable();          // sum of mean_count over the day
            $table->float('peak')->nullable();           // highest mean_count of the day
            $table->unsignedTinyInteger('peak_hour')->nullable();
            $table->float('mean_of_means')->nullable();
            $table->float('max_of_maxes')->nullable();
            $table->float('mean_activity_fraction')->nullable();

            // --- shape -------------------------------------------------
            $table->unsignedTinyInteger('first_active_hour')->nullable();
            $table->unsignedTinyInteger('last_active_hour')->nullable();
            $table->float('within_day_sd')->nullable();

            // --- sample quality ----------------------------------------
            $table->unsignedSmallInteger('n_usable')->default(0);
            $table->unsignedSmallInteger('n_expected')->default(0);
            $table->float('completeness')->default(0);   // n_usable / n_expected
            $table->unsignedSmallInteger('n_low_confidence')->default(0);
            $table->unsignedSmallInteger('n_no_observation')->default(0);

            // --- weather-adjusted --------------------------------------
            // weather_factor is 1.0 while no weather source is configured
            $table->float('weather_factor')->nullable();
            $table->float('expected_total')->nullable();
            $table->float('ratio')->nullable();          // total / expected
            $table->float('z')->nullable();              // log(ratio) / residual_sd

            // --- apiary-relative ---------------------------------------
            $table->float('apiary_total')->nullable();
            $table->float('apiary_median')->nullable();
            $table->float('share')->nullable();          // this hive / apiary total
            $table->float('vs_median')->nullable();
            $table->unsignedSmallInteger('apiary_hive_count')->nullable();

            // --- trend --------------------------------------------------
            $table->float('slope10')->nullable();        // fractional change per day

            // --- provenance ---------------------------------------------
            $table->string('model_version', 64)->nullable();
            $table->string('threshold_version', 32)->nullable();

            $table->timestamps();

            $table->foreign('hive_id')
                ->references('id')->on('hives')
                ->onDelete('cascade');

            $table->unique(['hive_id', 'date'], 'uq_hive_daily_features');
            $table->index(['farm_id', 'date'], 'idx_hdf_farm_date');
            $table->index('date', 'idx_hdf_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hive_daily_features');
    }
}
