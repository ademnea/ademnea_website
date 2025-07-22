<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeeCountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('bee_counts', function (Blueprint $table) {
        $table->id();
        $table->timestamp('timestamp')->nullable(); // Optional: track detection time
        $table->foreignId('hive_video_id')->constrained()->onDelete('cascade');
        $table->string('video_filename'); // Path to the video file
        $table->integer('bee_count');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bee_counts');
    }
}
