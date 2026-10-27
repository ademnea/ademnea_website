<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\User already lists 'role' in $fillable, but the column was never
 * created, so mass-assigning it has been failing silently. This reconciles the
 * two. Existing users default to 'farmer'.
 */
class AddRoleToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('farmer')->after('password');
            $table->index('role', 'idx_users_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropColumn('role');
        });
    }
}
