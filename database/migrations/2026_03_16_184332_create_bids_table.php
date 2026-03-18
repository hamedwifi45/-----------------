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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            
            // تفاصيل العرض
            $table->decimal('amount', 10, 2); // السعر المقترح
            $table->integer('duration_days'); // المدة المقترحة للتنفيذ
            $table->text('proposal'); // رسالة العرض
            $table->text('cover_letter')->nullable(); // رسالة تغطية إضافية
            
            // حالة العرض
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn', 'expired'])->default('pending');
            
            // التقييمات الداخلية (لصاحب العمل)
            $table->integer('rating')->nullable(); // تقييم صاحب العمل للعرض (1-5)
            $table->text('employer_feedback')->nullable(); // ملاحظات صاحب العمل
            
            // التواريخ
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // انتهاء صلاحية العرض
            
            // منع التكرار
            $table->unique(['project_id', 'freelancer_id']); // عرض واحد فقط لكل مشروع
            
            // الفهارس
            $table->index('project_id');
            $table->index('freelancer_id');
            $table->index('status');
            $table->index('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
