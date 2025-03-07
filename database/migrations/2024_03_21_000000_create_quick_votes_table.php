<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quick_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->string('question');
            $table->string('access_code')->unique(); // For QR code generation
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quick_vote_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quick_vote_id')->constrained('quick_votes')->onDelete('cascade');
            $table->string('voter_name');
            $table->enum('response', ['yea', 'nay', 'abstain']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_vote_responses');
        Schema::dropIfExists('quick_votes');
    }
}; 