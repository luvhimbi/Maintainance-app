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
      Schema::create('feedback', function (Blueprint $table) {
    $table->id();
    $table->foreignId('issue_id')->constrained('issue', 'issue_id')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete();
    $table->tinyInteger('rating')->unsigned();
    $table->text('comments')->nullable();
    $table->timestamps();
    
    $table->unique(['issue_id', 'user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
