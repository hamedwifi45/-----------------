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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // الروابط
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_message_id')->nullable()->constrained('messages')->onDelete('cascade'); // للردود
            
            // محتوى الرسالة
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_deleted_by_sender')->default(false);
            $table->boolean('is_deleted_by_receiver')->default(false);
            
            // نوع الرسالة
            $table->enum('type', ['text', 'system', 'notification'])->default('text');
            
            // التتبع
            $table->timestamp('read_at')->nullable();
            $table->string('sender_ip')->nullable();
            
            // الفهارس
            $table->index('contract_id');
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('is_read');
            $table->index('created_at');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
