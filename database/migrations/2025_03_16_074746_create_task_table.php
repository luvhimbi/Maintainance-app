<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->id('task_id'); // Primary key with auto-increment
            $table->unsignedBigInteger('issue_id'); // Foreign key to issue table
            $table->unsignedBigInteger('assignee_id')->nullable(); // Foreign key to user table
            $table->unsignedBigInteger('admin_id')->nullable(); // Foreign key to admin table
            $table->dateTime('assignment_date')->useCurrent(); // Default to current timestamp
            $table->dateTime('expected_completion'); // Expected completion date
            $table->enum('issue_status', ['Pending', 'In Progress', 'Completed'])->default('Pending'); // Enum with default value
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium'); // Enum with default value

            // Foreign key constraints
            $table->foreign('issue_id')->references('issue_id')->on('issue')->onDelete('cascade');
            $table->foreign('assignee_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('user_id')->on('admin')->onDelete('set null');

            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }

};
