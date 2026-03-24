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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
    
            // الربط مع المستخدم (واحد لواحد)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // البيانات الشخصية
            $table->string('first_name');
            $table->string('last_name');
            $table->string('display_name')->nullable(); // الاسم الظاهر للعامة
            $table->text('bio')->nullable(); // النبذة التعريفية
            $table->string('headline')->nullable(); // عنوان مهني قصير (مثال: مطور ويب متكامل)
            
            // الموقع الجغرافي
            $table->string('country');
            $table->string('city')->nullable();
            
            // معلومات التواصل (عامة)
            $table->string('phone_public')->nullable(); // رقم ظاهر للعامة (اختياري)
            $table->json('social_links')->nullable(); // روابط السوشيال ميديا
            
            // البيانات المهنية (للمستقلين)
            $table->decimal('hourly_rate', 10, 2)->nullable(); // سعر الساعة
            $table->string('availability')->default('available'); // available, busy, unavailable
            $table->integer('job_success_score')->default(0); // نسبة النجاح (0-100)
            $table->integer('completed_jobs')->default(0);
            $table->integer('in_progress_jobs')->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('total_spent', 12, 2)->default(0); // لأصحاب العمل
            
            // الملف الشخصي
            $table->string('profile_image')->nullable(); // مسار الصورة الشخصية
            $table->string('cover_image')->nullable(); // صورة الغلاف
            
            // التوثيق
            $table->boolean('is_verified')->default(false); // هل تم توثيق الهوية؟
            $table->timestamp('verified_at')->nullable();
            $table->json('verification_documents')->nullable(); // وثائق التوثيق
            
            // حالة الملف
            $table->boolean('is_complete')->default(false); // هل اكتمل الملف 100%؟
            $table->boolean('is_public')->default(true); // هل الملف ظاهر للعامة؟
            
            // الفهارس للأداء
            $table->unique('user_id'); // ملف واحد لكل مستخدم
            $table->index('country');
            $table->index('availability');
            $table->index('is_verified');
            $table->index('job_success_score');
            
            $table->timestamps();
            $table->softDeletes(); // للحذف الناعم
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
