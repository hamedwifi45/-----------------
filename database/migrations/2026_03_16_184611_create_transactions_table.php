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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
             // الروابط
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('escrow_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('withdrawal_request_id')->nullable()->constrained()->onDelete('set null');
            
            // نوع الحركة
            $table->enum('type', [
                'deposit',           // إيداع من بوابة دفع
                'withdrawal',        // سحب للبنك
                'payment',           // دفع لصاحب عمل
                'earnings',          // ربح من مشروع
                'refund',            // استرداد
                'commission',        // عمولة المنصة
                'escrow_hold',       // حجز في ضمان
                'escrow_release',    // إطلاق من ضمان
                'bonus',             // مكافأة
                'adjustment'         // تعديل إداري
            ]);
            
            // الاتجاه (دائن / مدين)
            $table->enum('direction', ['credit', 'debit']);
            
            // المبالغ (decimal دائماً)
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 12, 2)->default(0); // الرسوم
            $table->decimal('net_amount', 12, 2); // الصافي بعد الرسوم
            $table->decimal('balance_before', 12, 2); // الرصيد قبل الحركة
            $table->decimal('balance_after', 12, 2); // الرصيد بعد الحركة (للتتبع)
            
            // المعلومات
            $table->string('reference_id')->nullable(); // رقم مرجعي داخلي
            $table->string('external_reference')->nullable(); // رقم من بوابة الدفع
            $table->string('payment_method')->nullable(); // visa, mastercard, mada, bank_transfer
            $table->string('payment_gateway')->nullable(); // moyasar, hyperpay, stripe
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // بيانات إضافية بصيغة JSON
            
            // الحالة
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'reversed'
            ])->default('pending');
            
            // التواريخ
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason')->nullable();
            
            // التتبع الأمني
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null'); // للمعاملات اليدوية
            
            // منع التعديل (مهم جداً)
            $table->boolean('is_locked')->default(false); // لا يعدل بعد الإقفال
            
            // الفهارس
            $table->index('user_id');
            $table->index('wallet_id');
            $table->index('type');
            $table->index('status');
            $table->index('direction');
            $table->index('created_at');
            $table->index('reference_id');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
