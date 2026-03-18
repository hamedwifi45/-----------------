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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            
            // بيانات المهارة
            $table->string('name'); // اسم المهارة (مثال: PHP, Laravel, تصميم شعارات)
            $table->string('slug'); // للرابط SEO (مثال: php, laravel)
            $table->string('name_ar')->nullable(); // الاسم بالعربية
            $table->string('name_en')->nullable(); // الاسم بالإنجليزية
            
            // التصنيف
            $table->string('category')->nullable(); // (تطوير، تصميم، كتابة، إلخ)
            $table->foreignId('parent_skill_id')->nullable()->constrained('skills')->onDelete('set null'); // للمهارات الفرعية
            
            // الحالة والترتيب
            $table->boolean('is_active')->default(true); // لإخفاء مهارات قديمة بدون حذف
            $table->boolean('is_featured')->default(false); // مهارات مميزة تظهر أولاً
            $table->integer('sort_order')->default(0); // للترتيب اليدوي
            $table->integer('usage_count')->default(0); // عدد مرات استخدام المهارة (لإحصائيات)
            
            // وصف اختياري
            $table->text('description')->nullable();
            
            // الفهارس للأداء
            $table->unique('slug');
            $table->index('category');
            $table->index('is_active');
            $table->index('parent_skill_id');
            $table->index('sort_order');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
