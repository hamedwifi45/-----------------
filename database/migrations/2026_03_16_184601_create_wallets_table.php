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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
                    
            // الربط مع المستخدم (واحد لواحد)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // الأرصدة (جميعها decimal وليس float أبداً)
            $table->decimal('balance', 12, 2)->default(0); // رصيد قابل للسحب
            $table->decimal('pending_balance', 12, 2)->default(0); // رصيد معلق (تحت المراجعة)
            $table->decimal('reserved_balance', 12, 2)->default(0); // رصيد محجوز (في ضمانات)
            $table->decimal('total_deposited', 12, 2)->default(0); // إجمالي ما تم إيداعه
            $table->decimal('total_withdrawn', 12, 2)->default(0); // إجمالي ما تم سحبه
            $table->decimal('total_earned', 12, 2)->default(0); // إجمالي الأرباح
            $table->decimal('total_spent', 12, 2)->default(0); // إجمالي ما تم صرفه
            
            // الإعدادات
            $table->string('currency')->default('SAR');
            $table->boolean('is_active')->default(true);
            $table->boolean('can_withdraw')->default(true); // هل مسموح بالسحب؟
            $table->decimal('withdrawal_limit_daily', 12, 2)->nullable(); // حد السحب اليومي
            $table->decimal('withdrawal_limit_monthly', 12, 2)->nullable(); // حد السحب الشهري
            
            // التتبع
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamp('last_withdrawal_at')->nullable();
            $table->integer('withdrawal_count')->default(0);
            
            // ملاحظات إدارية (لا يراها المستخدم)
            $table->text('admin_notes')->nullable();
            $table->timestamp('frozen_at')->nullable();
            $table->string('freeze_reason')->nullable();
            
            // الفهارس والحماية
            $table->unique('user_id'); // محفظة واحدة لكل مستخدم
            $table->index('balance');
            $table->index('is_active');
            $table->index('can_withdraw');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
