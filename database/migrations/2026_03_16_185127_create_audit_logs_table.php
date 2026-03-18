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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
             // من فعل ماذا
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_type')->nullable(); // للمرونة مع نماذج أخرى
            
            // الإجراء
            $table->string('action'); // created, updated, deleted, login, logout, etc.
            $table->string('model_type')->nullable(); // النموذج المتأثر
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_attribute')->nullable(); // الحقل المتأثر
            
            // التفاصيل
            $table->json('old_values')->nullable(); // القيم قبل التعديل
            $table->json('new_values')->nullable(); // القيم بعد التعديل
            $table->text('description')->nullable();
            
            // التتبع الأمني
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('request_method')->nullable(); // GET, POST, PUT, DELETE
            $table->string('request_url')->nullable();
            
            // الحالة
            $table->boolean('is_suspicious')->default(false); // للنشاط المشبوه
            $table->text('suspicion_reason')->nullable();
            
            // الفهارس
            $table->index('user_id');
            $table->index('action');
            $table->index('model_type');
            $table->index('model_id');
            $table->index('created_at');
            $table->index('is_suspicious');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
