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
            Schema::create('campus_members', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->enum('member_type', ['Student', 'Staff']);
            $table->string('student_staff_id', 50)->unique();
            $table->string('faculty_department', 100)->nullable();
            $table->string('program_course', 100)->nullable();
            $table->integer('year_of_study')->nullable();
            $table->string('position_title', 100)->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campus_members');
    }
};
