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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); 
            $table->string('username', 50)->unique();
            $table->string('password_hash');
            $table->string('email', 200)->unique(); 
            $table->string('phone_number', 20)->nullable()->unique(); 
            $table->enum('user_role', ['Student', 'Technician', 'Admin'])->default('Student'); // User role
            $table->enum('status', ['Active', 'Inactive', 'Suspended'])->default('Active');
            $table->softDeletes();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
