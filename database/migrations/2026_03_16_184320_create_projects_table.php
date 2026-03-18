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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // المالك (صاحب العمل)
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            
            // بيانات المشروع
            $table->string('title');
            $table->string('slug')->unique(); // للرابط SEO
            $table->text('description'); // وصف تفصيلي
            $table->text('requirements')->nullable(); // متطلبات إضافية
            
            // الميزانية
            $table->decimal('budget_min', 10, 2);
            $table->decimal('budget_max', 10, 2)->nullable();
            $table->enum('budget_type', ['fixed', 'hourly'])->default('fixed');
            $table->string('currency')->default('SAR');
            
            // المدة الزمنية
            $table->integer('duration_days')->nullable();
            $table->timestamp('deadline')->nullable();
            
            // الحالة والنشر
            $table->enum('status', ['draft', 'open', 'in_progress', 'completed', 'cancelled', 'archived'])->default('draft');
            $table->integer('proposals_count')->default(0); // عدد العروض
            $table->boolean('is_featured')->default(false); // مشروع مميز
            $table->boolean('is_urgent')->default(false); // مشروع عاجل
            $table->boolean('is_hidden')->default(false); // مخفي عن البحث
            
            // التواريخ
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء النشر التلقائي
            
            // إحصائيات
            $table->integer('views_count')->default(0);
            $table->integer('favorites_count')->default(0);
            
            // الحذف الناعم والاسترجاع
            $table->softDeletes();
            
            // الفهارس للأداء
            $table->index('employer_id');
            $table->index('status');
            $table->index('budget_type');
            $table->index('published_at');
            $table->index('expires_at');
            $table->index('is_featured');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
