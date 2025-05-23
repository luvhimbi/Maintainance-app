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
        Schema::create('maintenance_staff', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary(); // Primary key
            $table->string('specialization', 100); // Specialization
            $table->enum('availability_status', ['Available', 'Busy', 'On Leave'])->default('Available'); // Availability status
            $table->integer('current_workload')->default(0); // Current workload
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade'); // Foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_staff');
    }
};
