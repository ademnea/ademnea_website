<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Context the analysis layer needs about each hive. All nullable, so existing
 * rows stay valid and the columns can be filled in over time.
 *
 * - entrance_orientation and hive_type both shift the timing and level of
 *   daily activity, so two healthy hives can differ for structural reasons.
 *   Recording them is what stops cross-hive comparison misreading that.
 * - colony_epoch increments when a hive is re-colonised. Without it, a hive's
 *   history is silently spliced across two different colonies.
 * - site_group is for the case where one farm spans more than one physical
 *   site. Null means "the whole farm is one site", which is the normal case.
 */
class AddAnalysisColumnsToHivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hives', function (Blueprint $table) {
            $table->string('entrance_orientation', 16)->nullable()->after('colonized');
            $table->string('hive_type', 32)->nullable()->after('entrance_orientation');
            $table->unsignedInteger('colony_epoch')->default(1)->after('hive_type');
            $table->date('colony_epoch_started_on')->nullable()->after('colony_epoch');
            $table->string('site_group', 64)->nullable()->after('colony_epoch_started_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hives', function (Blueprint $table) {
            $table->dropColumn([
                'entrance_orientation',
                'hive_type',
                'colony_epoch',
                'colony_epoch_started_on',
                'site_group',
            ]);
        });
    }
}
