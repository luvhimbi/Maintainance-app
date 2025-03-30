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
            $table->id('attachment_id'); // Auto-incrementing primary key
            $table->unsignedBigInteger('issue_id'); // Foreign key to issue table
            $table->string('file_path', 255); // Path to the file
            $table->string('original_name', 255); // Original file name
            $table->string('mime_type', 50); // MIME type (e.g., image/jpeg, application/pdf)
            $table->integer('file_size'); // File size in bytes
            $table->string('storage_disk', 50)->default('local'); // Storage disk (e.g., local, s3)
            $table->dateTime('upload_date')->useCurrent(); // Timestamp for upload
            $table->timestamps(); // Adds created_at and updated_at columns

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
