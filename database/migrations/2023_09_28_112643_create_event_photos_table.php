<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('event_photos', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('event_id');
        $table->string('photo_url');
        $table->timestamps();

        $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_photos');
    }
}
