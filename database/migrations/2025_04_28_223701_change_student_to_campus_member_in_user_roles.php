<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Step 1: Remove default constraint
            DB::statement('ALTER TABLE users ALTER COLUMN user_role DROP DEFAULT');
            
            // Step 2: Drop the existing check constraint
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_user_role_check');
            
            // Step 3: Change column to text temporarily
            DB::statement('ALTER TABLE users ALTER COLUMN user_role TYPE TEXT');
            
            // Step 4: Update values
            DB::table('users')
                ->where('user_role', 'Student')
                ->update(['user_role' => 'Campus_Member']);
                
            // Step 5: Create new check constraint
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_user_role_check 
                          CHECK (user_role IN (\'Campus_Member\', \'Technician\', \'Admin\'))');
            
            // Step 6: Set new default
            DB::statement("ALTER TABLE users ALTER COLUMN user_role SET DEFAULT 'Campus_Member'");
        } 
        // For other databases (MySQL, SQLite)
        else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_role', 20)
                      ->default('Campus_Member')
                      ->change();
            });
            
            DB::table('users')
                ->where('user_role', 'Student')
                ->update(['user_role' => 'Campus_Member']);
        }
    }

    public function down()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN user_role DROP DEFAULT');
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_user_role_check');
            DB::statement('ALTER TABLE users ALTER COLUMN user_role TYPE TEXT');
            
            DB::table('users')
                ->where('user_role', 'Campus_Member')
                ->update(['user_role' => 'Student']);
                
            DB::statement('ALTER TABLE users ADD CONSTRAINT users_user_role_check 
                          CHECK (user_role IN (\'Student\', \'Technician\', \'Admin\'))');
            DB::statement("ALTER TABLE users ALTER COLUMN user_role SET DEFAULT 'Student'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_role', 20)
                      ->default('Student')
                      ->change();
            });
            
            DB::table('users')
                ->where('user_role', 'Campus_Member')
                ->update(['user_role' => 'Student']);
        }
    }
};