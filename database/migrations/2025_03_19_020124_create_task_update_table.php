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
            $table->id('update_id'); 
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('staff_id')->nullable(); 
            $table->text('update_description');
            $table->string('status_change', 50); 
            $table->timestamp('update_timestamp')->useCurrent(); 

            // Foreign key constraints
            $table->foreign('task_id')
                  ->references('task_id')
                  ->on('task')
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
