<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول رؤى الأعمال
     */
    public function up(): void
    {
        Schema::create('business_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['sales', 'customer', 'product', 'market', 'trend', 'general'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_actionable')->default(false);
            $table->text('action_description')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // إضافة فهرس للبحث السريع
            $table->index(['merchant_id', 'type', 'priority', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_insights');
    }
};
