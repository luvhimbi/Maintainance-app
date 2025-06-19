<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        //this migration creates a table for staff members
        Schema::create('staff_members', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary(); 
            $table->string('department');
            $table->string('position_title');
            $table->timestamps();
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};
