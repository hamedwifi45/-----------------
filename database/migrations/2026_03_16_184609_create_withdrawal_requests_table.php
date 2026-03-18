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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            
            // المبلغ
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2);
            
            // طريقة السحب
            $table->enum('method', ['bank_transfer', 'paypal', 'payoneer', 'crypto']);
            $table->json('payment_details'); // بيانات الحساب (مشفرة)
            
            // الحالة
            $table->enum('status', [
                'pending',
                'processing',
                'approved',
                'rejected',
                'completed',
                'failed',
                'cancelled'
            ])->default('pending');
            
            // التواريخ
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // المراجعة
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            
            // التتبع الخارجي
            $table->string('transaction_reference')->nullable(); // من البنك أو البوابة
            $table->string('failure_reason')->nullable();
            
            // الفهارس
            $table->index('user_id');
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
        Schema::dropIfExists('withdrawal_requests');
    }
};
