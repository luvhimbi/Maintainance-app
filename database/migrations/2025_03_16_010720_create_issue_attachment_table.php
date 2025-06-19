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
        Schema::create('issue_attachment', function (Blueprint $table) {
            $table->id('attachment_id'); 
            $table->unsignedBigInteger('issue_id');
            $table->string('file_path', 255); 
            $table->string('original_name', 255); 
            $table->string('mime_type', 50); 
            $table->integer('file_size'); 
            $table->string('storage_disk', 50)->default('local'); 
            $table->dateTime('upload_date')->useCurrent(); 
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('issue_id')->references('issue_id')->on('issue')->onDelete('cascade');

            // Index for faster queries
            $table->index('issue_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_attachment');
    }
};
