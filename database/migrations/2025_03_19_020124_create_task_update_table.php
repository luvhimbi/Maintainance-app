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
        Schema::create('task_update', function (Blueprint $table) {
            $table->id('update_id'); // Primary key with auto-increment
            $table->unsignedBigInteger('task_id'); // Foreign key to tasks table
            $table->unsignedBigInteger('staff_id')->nullable(); // Foreign key to user table, nullable
            $table->text('update_description'); // Update description
            $table->string('status_change', 50); // Status change
            $table->timestamp('update_timestamp')->useCurrent(); // Timestamp with default current time

            // Foreign key constraints
            $table->foreign('task_id')
                  ->references('task_id')
                  ->on('tasks')
                  ->onDelete('cascade'); // Cascade on delete

            $table->foreign('staff_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null'); // Set null on delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_update');
    }
};
