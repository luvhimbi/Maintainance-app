<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Step 1: First ensure there are no NULL values
            DB::table('maintenance_staff')
                ->whereNull('specialization')
                ->update(['specialization' => 'General']); 
            
            // Step 2: Drop existing constraints
            DB::statement('ALTER TABLE maintenance_staff ALTER COLUMN specialization DROP DEFAULT');
            DB::statement('ALTER TABLE maintenance_staff DROP CONSTRAINT IF EXISTS maintenance_staff_specialization_check');
            
            // Step 3: Create the new ENUM type
            DB::statement("CREATE TYPE specialization_enum AS ENUM ('General', 'Electrical', 'Structural', 'Plumbing', 'Furniture', 'Other')");
            
            // Step 4: Convert column to text temporarily
            DB::statement('ALTER TABLE maintenance_staff ALTER COLUMN specialization TYPE TEXT');
            
            // Step 5: Convert to new ENUM type
            DB::statement('ALTER TABLE maintenance_staff ALTER COLUMN specialization TYPE specialization_enum 
                          USING (specialization::specialization_enum)');
            
            // Step 6: Add NOT NULL constraint (now safe since we handled NULLs)
            DB::statement('ALTER TABLE maintenance_staff ALTER COLUMN specialization SET NOT NULL');
            
            // Step 7: Set default value if needed
            DB::statement("ALTER TABLE maintenance_staff ALTER COLUMN specialization SET DEFAULT 'General'");
        }
        // For MySQL
        else {
            // First handle NULL values
            DB::table('maintenance_staff')
                ->whereNull('specialization')
                ->update(['specialization' => 'General']);
                
            DB::statement("ALTER TABLE maintenance_staff 
                          MODIFY COLUMN specialization ENUM('General', 'Electrical', 'Structural', 'Plumbing', 'Furniture', 'Other') NOT NULL DEFAULT 'General'");
        }
    }

    public function down()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Convert back to text/varchar
            DB::statement('ALTER TABLE maintenance_staff ALTER COLUMN specialization DROP NOT NULL');
            DB::statement('ALTER TABLE maintenance_staff ALTER COLUMN specialization TYPE VARCHAR(255)');
            DB::statement('DROP TYPE IF EXISTS specialization_enum');
        }
        // For MySQL
        else {
            DB::statement("ALTER TABLE maintenance_staff 
                          MODIFY COLUMN specialization VARCHAR(255)");
        }
    }
};
