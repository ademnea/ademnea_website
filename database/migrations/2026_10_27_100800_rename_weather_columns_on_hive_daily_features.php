<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The analysis runs on bee counts alone for now. No weather source is wired
 * up, and the in-hive sensor tables are not yet in scope, so the factor that
 * scales a hive's baseline is renamed from weather_factor to the neutral
 * conditions_factor and sits at 1.0.
 *
 * analysis_mode records how a given day was judged, because the confidence a
 * farmer should place in a verdict depends on it:
 *   'solo'          - fewer than three reporting hives in the farm. Only the
 *                     hive's own baseline is available, so a site-wide cause
 *                     cannot be ruled out.
 *   'peer_compared' - three or more. Peers absorb shared conditions, so
 *                     hive-specific and site-wide become separable.
 *
 * conditions_source names what produced conditions_factor. 'none' today;
 * 'exterior_sensor' once hive_temperatures exterior readings are available.
 */
class RenameWeatherColumnsOnHiveDailyFeatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hive_daily_features', function (Blueprint $table) {
            $table->renameColumn('weather_factor', 'conditions_factor');
        });

        Schema::table('hive_daily_features', function (Blueprint $table) {
            $table->string('analysis_mode', 24)->default('solo')->after('conditions_factor');
            $table->string('conditions_source', 32)->default('none')->after('analysis_mode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hive_daily_features', function (Blueprint $table) {
            $table->dropColumn(['analysis_mode', 'conditions_source']);
        });

        Schema::table('hive_daily_features', function (Blueprint $table) {
            $table->renameColumn('conditions_factor', 'weather_factor');
        });
    }
}
