<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * How a hive is categorised, on two independent axes. Stored as history rather
 * than as columns on `hives`, because "was strong in September, average in
 * October" is itself the useful signal.
 *
 * size_class      - a level, from the baseline, relative to its apiary peers:
 *                   strong | average | weak | very_weak | unclassified
 *                   Thresholds approximate the roughly fourfold span between
 *                   small and large colonies reported in the literature.
 *
 * condition_class - a trajectory, from the trend:
 *                   establishing | stable | declining | critical | silent | unknown
 *
 * The two must stay separate: a small hive can be perfectly healthy and a
 * large one can be failing. Note also that none of these names asserts a
 * cause - counts cannot distinguish absconding from theft from colony death,
 * so 'silent' says what was measured and nothing more.
 *
 * Stored as strings rather than enums so new classes can be added without an
 * ALTER on a populated table.
 */
class CreateHiveClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hive_classifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hive_id');
            $table->unsignedInteger('colony_epoch')->default(1);

            $table->string('size_class', 24)->default('unclassified');
            $table->string('condition_class', 24)->default('unknown');

            $table->float('size_ratio')->nullable();      // hive baseline / apiary median
            $table->float('trend_pct_per_day')->nullable();
            $table->float('pct_of_baseline')->nullable(); // recent level vs baseline

            $table->date('effective_from');
            $table->date('effective_to')->nullable();     // null = current

            $table->text('evidence')->nullable();
            $table->string('threshold_version', 32)->nullable();

            $table->timestamps();

            $table->foreign('hive_id')
                ->references('id')->on('hives')
                ->onDelete('cascade');

            $table->index(['hive_id', 'effective_from'], 'idx_hc_hive_from');
            $table->index('effective_to', 'idx_hc_effective_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hive_classifications');
    }
}
