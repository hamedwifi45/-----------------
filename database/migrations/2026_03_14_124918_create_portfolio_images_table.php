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
        Schema::create('portfolio_images', function (Blueprint $table) {
            $table->id();
             // الربط
            $table->foreignId('portfolio_item_id')->constrained()->onDelete('cascade');
            
            // معلومات الصورة
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            
            // الأبعاد
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // الترتيب
            $table->boolean('is_cover')->default(false); // الصورة الرئيسية
            $table->integer('sort_order')->default(0);
            
            // الفهارس
            $table->index('portfolio_item_id');
            $table->index('is_cover');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_images');
    }
};
