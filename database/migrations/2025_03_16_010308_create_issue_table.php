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
        Schema::create('issue', function (Blueprint $table) {
            $table->id('issue_id'); // This will create an auto-incrementing primary key
            $table->unsignedBigInteger('reporter_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('issue_type', 50);
            $table->text('issue_description');
            $table->dateTime('report_date')->useCurrent();
            $table->enum('issue_status', ['Open', 'In Progress', 'Resolved', 'Closed']);
            $table->enum('urgency_level', ['Low', 'Medium', 'High']);
            $table->timestamps(); // Adds created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('reporter_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('location_id')->references('location_id')->on('location')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue');
    }
};
