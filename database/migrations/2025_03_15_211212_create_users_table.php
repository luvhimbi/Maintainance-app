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
            $table->id('user_id'); // Primary key, auto-incrementing
            $table->string('username', 50)->unique(); // Unique username
            $table->string('password_hash'); // Password hash
            $table->string('email', 200)->unique(); // Unique email
            $table->string('phone_number', 20)->nullable()->unique(); // Optional, unique phone number
            $table->enum('user_role', ['Student', 'Technician', 'Admin'])->default('Student'); // User role
            $table->timestamps(); // Adds `created_at` and `updated_at` columns
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
