<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What "normal" is for one hive. One row per hive, per colony epoch, per type.
 *
 * baseline_type:
 *   'initial' - the first 7 valid days of a colony epoch, then frozen. Answers
 *               "how does this hive compare to when it started?"
 *   'rolling' - a trailing 28-day window, recomputed weekly. Absorbs seasonal
 *               drift, and is what the anomaly detectors compare against.
 *
 * hourly_profile holds the 24-hour shape as JSON rather than 24 rows, because
 * it is only ever read as a whole. Shape:
 *   {"6": {"median": 0.4, "q25": 0.2, "q75": 0.7}, "7": {...}, ...}
 *
 * Normal is a curve, not a number: three bees at 06:00 is not three bees at
 * 13:00, so a single threshold on raw counts is wrong by construction.
 */
class CreateHiveBaselinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hive_baselines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hive_id');
            $table->unsignedInteger('colony_epoch')->default(1);
            $table->string('baseline_type', 16)->default('rolling');

            $table->date('window_start');
            $table->date('window_end');
            $table->unsignedInteger('days_used')->default(0);

            // daily-level numbers
            $table->float('base_daily')->nullable();        // median daily total
            $table->float('base_daily_sd')->nullable();
            $table->float('base_share')->nullable();        // median share of apiary
            $table->float('residual_sd')->nullable();       // sd of log(observed/expected)

            // 24-hour shape
            $table->json('hourly_profile')->nullable();

            $table->string('model_version', 64)->nullable();
            $table->timestamps();

            $table->foreign('hive_id')
                ->references('id')->on('hives')
                ->onDelete('cascade');

            $table->unique(
                ['hive_id', 'colony_epoch', 'baseline_type'],
                'uq_hive_baseline'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hive_baselines');
    }
}
