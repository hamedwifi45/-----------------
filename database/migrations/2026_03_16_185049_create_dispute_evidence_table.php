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
        Schema::create('dispute_evidence', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('dispute_id')->constrained()->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            
            // نوع الدليل
            $table->enum('type', ['message', 'file', 'deliverable', 'contract', 'other']);
            
            // المحتوى
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('message_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('deliverable_id')->nullable()->constrained()->onDelete('set null');
            
            // الحالة
            $table->boolean('is_verified')->default(false);
            
            // التواريخ
            $table->timestamp('submitted_at');
            
            // الفهارس
            $table->index('dispute_id');
            $table->index('submitted_by');
            $table->index('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispute_evidence');
    }
};
