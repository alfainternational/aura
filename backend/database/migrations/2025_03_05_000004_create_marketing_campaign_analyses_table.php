<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول تحليلات حملات التسويق
     */
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['email', 'sms', 'push', 'social', 'display', 'search', 'affiliate', 'other'])->default('email');
            $table->enum('status', ['draft', 'scheduled', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->json('target_audience')->nullable();
            $table->json('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // إضافة فهرس للبحث السريع مع اسم أقصر
            $table->index(['merchant_id', 'status'], 'mc_merchant_status_idx');
            $table->index(['start_date', 'end_date'], 'mc_date_range_idx');
        });
        
        Schema::create('marketing_campaign_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->onDelete('cascade');
            $table->timestamp('analysis_date');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->decimal('cost_per_click', 10, 2)->nullable();
            $table->decimal('cost_per_conversion', 10, 2)->nullable();
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('roi', 10, 2)->nullable();
            $table->json('demographics')->nullable();
            $table->json('engagement_metrics')->nullable();
            $table->json('recommendations')->nullable();
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع مع اسم أقصر
            $table->index(['campaign_id', 'analysis_date'], 'mca_campaign_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_campaign_analyses');
        Schema::dropIfExists('marketing_campaigns');
    }
};
