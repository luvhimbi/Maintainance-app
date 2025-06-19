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
            $table->id('task_id'); 
            $table->unsignedBigInteger('issue_id'); 
            $table->unsignedBigInteger('assignee_id')->nullable(); 
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->dateTime('assignment_date')->useCurrent(); 
            $table->dateTime('expected_completion'); 
            $table->dateTime('actual_completion')->nullable();
            $table->enum('issue_status', ['Pending', 'In Progress', 'Completed'])->default('Pending'); 
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');

            // Foreign key constraints
            $table->foreign('issue_id')->references('issue_id')->on('issue')->onDelete('cascade');
            $table->foreign('assignee_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('user_id')->on('admin')->onDelete('set null');

            $table->timestamps(); 
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
