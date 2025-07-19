<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHiveVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hive_videos', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->unsignedBigInteger('hive_id');

            $table->foreign('hive_id')
            ->references('id')->on('hives')
            ->onDelete('cascade');

            $table->timestamps();
        });

        // Remove duplicate videos (only if table has data)
        // This should only run if there's existing data, not on fresh installations
        if (DB::table('hive_videos')->count() > 0) {
            $duplicates = DB::table('hive_videos')
                        ->select('path', 'hive_id', DB::raw('MIN(id) as keep_id'))
                        ->groupBy('path', 'hive_id')
                        ->having(DB::raw('COUNT(*)'), '>', 1)
                        ->get();

                    foreach ($duplicates as $duplicate) {
                        // Delete all except the one with the lowest ID
                        DB::table('hive_videos')
                            ->where('path', $duplicate->path)
                            ->where('hive_id', $duplicate->hive_id)
                            ->where('id', '!=', $duplicate->keep_id)
                            ->delete();
                    }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hive_videos');
    }
}
