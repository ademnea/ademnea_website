<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Farmer-facing conclusions. One row per hive per day per verdict.
 *
 * verdict is the outcome of the reconciliation ladder, evaluated in order,
 * first match wins:
 *   monitor_offline | insufficient_data | apiary_wide | weather_confined
 *   | hive_anomaly_sudden | hive_trend_decline | hive_spike | normal
 *
 * threshold_version records which set of thresholds produced the row. Those
 * thresholds live in config and will be retuned; without the version, a past
 * insight cannot be reproduced or explained.
 *
 * `message` states an observation and `action` what to do about it. Neither
 * should ever name a cause the counts cannot establish.
 */
class CreateHiveInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hive_insights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hive_id');
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->date('date');

            $table->string('verdict', 32);
            $table->string('confidence', 16)->default('medium');
            $table->text('message');
            $table->string('action', 255)->nullable();
            $table->text('evidence')->nullable();

            // true once the ladder has ruled out weather, peers and data faults
            $table->boolean('escalate')->default(false);

            $table->timestamp('acknowledged_at')->nullable();
            $table->unsignedBigInteger('acknowledged_by')->nullable();

            $table->string('threshold_version', 32)->nullable();
            $table->timestamps();

            $table->foreign('hive_id')
                ->references('id')->on('hives')
                ->onDelete('cascade');

            $table->unique(['hive_id', 'date', 'verdict'], 'uq_hive_insight');
            $table->index(['farm_id', 'date'], 'idx_hi_farm_date');
            $table->index('verdict', 'idx_hi_verdict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hive_insights');
    }
}
