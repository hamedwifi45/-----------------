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
        Schema::create('escrows', function (Blueprint $table) {
            $table->id();
            // الروابط الأساسية
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // محفظة صاحب العمل
            
            // المبالغ
            $table->decimal('amount', 12, 2); // المبلغ الأصلي
            $table->decimal('commission', 12, 2)->default(0); // عمولة المنصة
            $table->decimal('net_amount', 12, 2); // الصافي للمستقل
            $table->decimal('refunded_amount', 12, 2)->default(0); // المبلغ المسترد
            
            // الحالة
            $table->enum('status', [
                'pending',      // بانتظار الإيداع
                'held',         // محجوز في الضمان
                'released',     // تم الإطلاق للمستقل
                'refunded',     // تم الاسترداد لصاحب العمل
                'disputed',     // تحت النزاع
                'partially_released' // إطلاق جزئي
            ])->default('pending');
            
            // التواريخ الحرجة
            $table->timestamp('funded_at')->nullable(); // متى تم الإيداع
            $table->timestamp('released_at')->nullable(); // متى تم الإطلاق
            $table->timestamp('refunded_at')->nullable(); // متى تم الاسترداد
            $table->timestamp('expires_at')->nullable(); // تاريخ انتهاء الضمان
            
            // من قام بالعملية
            $table->foreignId('released_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('refunded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disputed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // الملاحظات
            $table->text('release_note')->nullable();
            $table->text('refund_note')->nullable();
            $table->text('admin_notes')->nullable(); // ملاحظات إدارية فقط
            
            // التتبع
            $table->string('reference_id')->unique(); // رقم مرجعي فريد للضمان
            $table->json('metadata')->nullable();
            
            // الفهارس
            $table->unique('contract_id'); // ضمان واحد لكل عقد
            $table->index('employer_id');
            $table->index('freelancer_id');
            $table->index('status');
            $table->index('funded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrows');
    }
};
