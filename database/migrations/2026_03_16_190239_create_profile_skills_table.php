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
        Schema::create('profile_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->integer('proficiency_level')->default(3); // 1-5 (مبتدئ، متوسط، محترف، خبير، خارق)
            $table->integer('years_of_experience')->nullable();
            $table->boolean('is_primary')->default(false); // هل هي مهارة أساسية؟
            
            // منع التكرار
            $table->unique(['profile_id', 'skill_id']);
            
            // فهارس
            $table->index('skill_id');
            $table->index('proficiency_level');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_skills');
    }
};
