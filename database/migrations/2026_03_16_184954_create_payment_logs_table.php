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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
             // الروابط
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            
            // معلومات البوابة
            $table->string('gateway'); // moyasar, hyperpay, stripe, etc.
            $table->string('gateway_transaction_id')->unique(); // الرقم من البوابة
            $table->string('payment_method'); // visa, mastercard, mada, apple_pay
            $table->string('currency')->default('SAR');
            
            // المبالغ
            $table->decimal('amount', 12, 2);
            $table->decimal('gateway_fee', 12, 2)->default(0);
            
            // الحالة من البوابة
            $table->enum('status', [
                'initiated',
                'pending',
                'success',
                'failed',
                'cancelled',
                'refunded'
            ]);
            
            // البيانات الخام من البوابة (للمراجعة)
            $table->json('raw_response'); // الاستجابة الكاملة من البوابة
            $table->json('raw_request'); // الطلب الذي أرسل للبوابة
            
            // التتبع
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('failure_message')->nullable();
            
            // التواريخ
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // الفهارس
            $table->index('user_id');
            $table->index('gateway');
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
        Schema::dropIfExists('payment_logs');
    }
};
