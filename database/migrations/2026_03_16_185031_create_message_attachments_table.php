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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            // معلومات الملف
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type'); // mime type
            $table->string('file_extension');
            $table->unsignedBigInteger('file_size'); // بالبايت
            
            // الأمان
            $table->string('file_hash')->nullable(); // للتحقق من سلامة الملف
            $table->boolean('is_scanned')->default(false); // هل تم فحص الفيروسات؟
            $table->boolean('is_malicious')->default(false);
            
            // الفهارس
            $table->index('message_id');
            $table->index('file_type');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
