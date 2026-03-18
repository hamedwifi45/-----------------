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
        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            
            // معلومات التسليم
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('version')->default(1); // إصدار التسليم
            $table->boolean('is_final')->default(false); // هل هو التسليم النهائي؟
            
            // الحالة
            $table->enum('status', ['submitted', 'reviewing', 'approved', 'rejected', 'revision_requested'])->default('submitted');
            
            // المراجعات
            $table->integer('revision_number')->default(0);
            $table->text('client_feedback')->nullable();
            $table->text('freelancer_response')->nullable();
            
            // التواريخ
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('revision_requested_at')->nullable();
            
            // من قام بالمراجعة
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // الفهارس
            $table->index('contract_id');
            $table->index('freelancer_id');
            $table->index('status');
            $table->index('submitted_at');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliverables');
    }
};
