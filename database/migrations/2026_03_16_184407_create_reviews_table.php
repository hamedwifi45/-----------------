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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_id')->constrained('users')->onDelete('cascade');
            
            // نوع التقييم
            $table->enum('type', ['employer_to_freelancer', 'freelancer_to_employer']);
            
            // التقييمات
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->tinyInteger('communication_rating')->nullable(); // جودة التواصل
            $table->tinyInteger('quality_rating')->nullable(); // جودة العمل
            $table->tinyInteger('deadline_rating')->nullable(); // الالتزام بالوقت
            $table->tinyInteger('budget_rating')->nullable(); // الالتزام بالميزانية
            
            // المحتوى
            $table->text('comment')->nullable();
            $table->text('private_feedback')->nullable(); // ملاحظات خاصة للإدارة
            
            // الحالة
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_verified')->default(false); // هل تم التحقق من صحة التقييم؟
            
            // التفاعل
            $table->integer('helpful_count')->default(0);
            $table->json('helpful_users')->nullable();
            
            // منع التكرار
            $table->unique(['contract_id', 'reviewer_id']); // تقييم واحد لكل عقد
            
            // الفهارس
            $table->index('reviewed_id');
            $table->index('rating');
            $table->index('type');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
