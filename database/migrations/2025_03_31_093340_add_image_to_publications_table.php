<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToPublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('publications', function (Blueprint $table) {
        //     $table->string('image')->nullable()->after('attachment');
        // });
    }
    
    public function down()
    {
        // Schema::table('publications', function (Blueprint $table) {
        //     $table->dropColumn('image');
        // });
    }
}
