<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        // Disable foreign key checks (for MySQL)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        
        // For PostgreSQL, defer constraints
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('SET CONSTRAINTS ALL DEFERRED');
        }

        // Delete records in reverse order of dependencies
        DB::table('notifications')->truncate();
        DB::table('issue')->truncate();
        DB::table('task')->truncate();
        DB::table('Location')->truncate();
        DB::table('task_update')->truncate();
        DB::table('maintenance_staff')->truncate();
        DB::table('admin')->truncate();
        DB::table('users')->truncate();

        // Re-enable foreign key checks (for MySQL)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
