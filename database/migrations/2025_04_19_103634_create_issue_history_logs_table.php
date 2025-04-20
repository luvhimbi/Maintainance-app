<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('issue_history_logs', function (Blueprint $table) {
            $table->id();
           $table->foreignId('issue_id')->constrained('issue', 'issue_id')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->string('action'); // e.g., 'created', 'updated', 'status_changed'
            $table->json('old_values')->nullable(); // Stores previous values
            $table->json('new_values')->nullable(); // Stores new values
            $table->text('description')->nullable(); // Human-readable change description
            $table->timestamps();
            
            $table->index('issue_id');
            $table->index('user_id');
            $table->index('action');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_history_logs');
    }
};
