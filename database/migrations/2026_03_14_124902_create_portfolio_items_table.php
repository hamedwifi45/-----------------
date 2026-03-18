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
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            // الربط
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            
            // معلومات المشروع
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('project_details')->nullable(); // تفاصيل إضافية
            
            // التصنيف
            $table->string('category')->nullable();
            $table->json('skills_used')->nullable(); // المهارات المستخدمة في المشروع
            
            // العميل (اختياري)
            $table->string('client_name')->nullable();
            $table->boolean('is_confidential')->default(false); // هل هو مشروع سري؟
            
            // الروابط
            $table->string('project_url')->nullable();
            $table->string('demo_url')->nullable();
            
            // الحالة
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            
            // الترتيب
            $table->integer('sort_order')->default(0);
            
            // الفهارس
            $table->index('profile_id');
            $table->index('is_published');
            $table->index('category');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
