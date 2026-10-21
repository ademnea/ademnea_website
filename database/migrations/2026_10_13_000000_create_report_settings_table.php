<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_settings', function (Blueprint $table) {
            $table->id();
            $table->string('smtp_host')->nullable();
            $table->unsignedInteger('smtp_port')->default(587);
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // stored encrypted
            $table->string('smtp_encryption')->nullable(); // tls, ssl or null for none
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->text('recipients')->nullable(); // JSON array of email addresses
            $table->boolean('auto_send')->default(false);
            $table->string('send_time', 5)->default('08:00'); // HH:MM, app timezone
            $table->timestamp('last_sent_at')->nullable();
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
        Schema::dropIfExists('report_settings');
    }
}
