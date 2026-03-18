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
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            
            // الروابط
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_admin')->nullable()->constrained('users')->onDelete('set null');
            
            // معلومات النزاع
            $table->string('title');
            $table->text('description');
            $table->enum('reason', [
                'poor_quality',
                'late_delivery',
                'non_delivery',
                'payment_issue',
                'communication_issue',
                'scope_change',
                'other'
            ]);
            
            // الحالة
            $table->enum('status', [
                'open',
                'under_review',
                'awaiting_evidence',
                'mediation',
                'resolved',
                'escalated',
                'closed'
            ])->default('open');
            
            // القرار
            $table->enum('resolution', [
                'full_refund',
                'partial_refund',
                'full_payment',
                'partial_payment',
                'no_action',
                'pending'
            ])->default('pending');
            
            // المبالغ
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            
            // التواريخ
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            // القرار النهائي
            $table->text('admin_decision')->nullable();
            $table->text('resolution_notes')->nullable();
            
            // الفهارس
            $table->index('contract_id');
            $table->index('status');
            $table->index('opened_at');
            $table->index('assigned_admin');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
