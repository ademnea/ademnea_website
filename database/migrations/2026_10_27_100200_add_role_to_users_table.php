<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\User already lists 'role' in $fillable. The column exists on some
 * environments because it was added directly rather than through a migration
 * (the same thing happened to bee_counts - see DASHBOARD-INTEGRATION.md 5.1),
 * so every step here is guarded and this is safe to run anywhere.
 *
 * Existing users default to 'farmer'.
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
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 32)->default('farmer')->after('password');
            });
        }

        if (!$this->hasIndex('users', 'idx_users_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'idx_users_role');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->hasIndex('users', 'idx_users_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_role');
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }

    /**
     * Whether a named index exists on a table.
     *
     * Laravel 8's Schema builder has no hasIndex(), so this checks the
     * information schema directly.
     *
     * @param  string  $table
     * @param  string  $index
     * @return bool
     */
    protected function hasIndex($table, $index)
    {
        $rows = DB::select(
            'SELECT 1 FROM information_schema.statistics
              WHERE table_schema = ? AND table_name = ? AND index_name = ?
              LIMIT 1',
            [DB::getDatabaseName(), $table, $index]
        );

        return count($rows) > 0;
    }
}
