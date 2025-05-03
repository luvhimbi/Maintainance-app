<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the username column first
            $table->dropColumn('username');

            // Add new columns with nullable (to avoid NOT NULL violation)
            $table->string('first_name', 100)->nullable()->after('user_role');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->string('address', 255)->nullable()->after('last_name');
        });

//        DB::statement("ALTER TABLE users ALTER COLUMN user_role DROP DEFAULT");
//
//        // ⚡ Step 2: Create old ENUM
//        DB::statement("CREATE TYPE user_role_enum_old AS ENUM ('Student', 'Technician', 'Admin')");
//
//        // ⚡ Step 3: Change back to old ENUM
//        DB::statement("ALTER TABLE users ALTER COLUMN user_role TYPE user_role_enum_old USING user_role::text::user_role_enum_old");
//
//        // ⚡ Step 4: Set old default
//        DB::statement("ALTER TABLE users ALTER COLUMN user_role SET DEFAULT 'Student'");
//
//        // ⚡ Step 5: Drop the new ENUM
//        DB::statement("DROP TYPE IF EXISTS user_role_enum_new");


    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username back
            $table->string('username', 50)->unique()->after('user_id');

            // Drop added columns
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('address');
        });

//        // Drop the new ENUM
//        DB::statement("DROP TYPE IF EXISTS user_role_enum_new");
//
//        // Create old ENUM
//        DB::statement("CREATE TYPE user_role_enum_old AS ENUM ('Student', 'Technician', 'Admin')");
//
//        // Change back to old ENUM
//        DB::statement("ALTER TABLE users ALTER COLUMN user_role TYPE user_role_enum_old USING user_role::text::user_role_enum_old");
//
//        // Set old default
//        DB::statement("ALTER TABLE users ALTER COLUMN user_role SET DEFAULT 'Student'");
    }
};
