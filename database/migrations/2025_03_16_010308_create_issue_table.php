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
            $table->id('issue_id');
            $table->unsignedBigInteger('reporter_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('issue_type', 50); // electrical, structural, hvac, furniture, general, pc
            $table->text('issue_description');
            $table->dateTime('report_date')->useCurrent();
            $table->enum('issue_status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->enum('urgency_level', ['Low', 'Medium', 'High'])->default('Low');
            $table->integer("urgency_score")->default(0);
            // Issue characteristics
            $table->boolean('safety_hazard')->default(false);
            $table->boolean('affects_operations')->default(false);
            $table->integer('affected_areas')->default(1);

            // PC-specific fields (nullable for non-PC issues)
            $table->string('pc_number', 20)->nullable()->comment('Identifier for the specific PC');
            $table->enum('pc_issue_type', [
                'hardware',
                'software',
                'network',
                'peripheral',
                'other'
            ])->nullable();
            $table->boolean('critical_work_affected')->default(false);

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('reporter_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('location_id')->references('location_id')->on('location')->onDelete('set null');

            // Index for faster queries on PC issues
            $table->index(['issue_type', 'pc_number']);
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
