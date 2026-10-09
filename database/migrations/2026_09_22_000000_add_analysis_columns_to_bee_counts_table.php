<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reconciles the schema with columns that were added directly on production
 * via a raw SQL script (see ademnea/db_migration.sql, outside this repo) on
 * 2026-09-12, so this migration exists but is marked as already-run there
 * rather than re-executed. See DASHBOARD-INTEGRATION.md §5.1.
 */
class AddAnalysisColumnsToBeeCountsTable extends Migration
{
    public function up()
    {
        Schema::table('bee_counts', function (Blueprint $table) {
            $table->float('mean_count')->nullable()->after('bee_count');
            $table->integer('max_count')->nullable()->after('mean_count');
            $table->float('activity_fraction')->nullable()->after('max_count');
            $table->integer('frames_analyzed')->nullable()->after('activity_fraction');
            $table->string('model_version', 64)->nullable()->after('frames_analyzed');
            $table->enum('processing_status', ['pending', 'processing', 'done', 'failed'])
                ->default('pending')
                ->after('model_version');
            $table->text('error_message')->nullable()->after('processing_status');
            $table->tinyInteger('attempts')->unsigned()->default(0)->after('error_message');
            $table->timestamp('processed_at')->nullable()->after('attempts');

            $table->index('processing_status', 'idx_status');
            $table->index(['hive_video_id', 'processing_status'], 'idx_hive_video_status');
        });
    }

    public function down()
    {
        Schema::table('bee_counts', function (Blueprint $table) {
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_hive_video_status');
            $table->dropColumn([
                'mean_count', 'max_count', 'activity_fraction', 'frames_analyzed',
                'model_version', 'processing_status', 'error_message', 'attempts', 'processed_at',
            ]);
        });
    }
}
