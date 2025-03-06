<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول محادثات الذكاء الاصطناعي
     */
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('context')->nullable();
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->enum('type', ['general', 'customer_support', 'product_recommendation', 'technical'])->default('general');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // إضافة فهرس للبحث السريع
            $table->index(['user_id', 'status', 'type']);
        });
        
        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->onDelete('cascade');
            $table->enum('role', ['user', 'assistant', 'system'])->default('user');
            $table->text('content');
            $table->json('attachments')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع
            $table->index(['conversation_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
