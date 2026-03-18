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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
             // الروابط الأساسية
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('bid_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            
            // تفاصيل العقد
            $table->decimal('amount', 10, 2); // القيمة المتفق عليها
            $table->decimal('commission', 10, 2)->default(0); // عمولة المنصة
            $table->decimal('net_amount', 10, 2)->default(0); // الصافي للمستقل
            $table->integer('duration_days');
            
            // التواريخ
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // حالة العقد
            $table->enum('status', ['pending', 'active', 'paused', 'completed', 'cancelled', 'disputed'])->default('pending');
            
            // التسليمات والمراجعات
            $table->integer('deliverables_count')->default(0);
            $table->integer('revisions_allowed')->default(2);
            $table->integer('revisions_used')->default(0);
            
            // أسباب الإلغاء أو النزاع
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            
            // ملاحظات إدارية (لا يراها المستخدمون)
            $table->text('admin_notes')->nullable();
            
            // فريد لكل عرض
            $table->unique('bid_id');
            
            // الفهارس
            $table->index('employer_id');
            $table->index('freelancer_id');
            $table->index('project_id');
            $table->index('status');
            $table->index('created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
