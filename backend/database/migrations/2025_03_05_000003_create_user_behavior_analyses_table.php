<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول تحليلات سلوك المستخدم
     */
    public function up(): void
    {
        Schema::create('user_behavior_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('analysis_type', ['purchase', 'browsing', 'engagement', 'retention', 'churn_risk', 'loyalty']);
            $table->json('behavior_data');
            $table->json('insights')->nullable();
            $table->json('recommendations')->nullable();
            $table->decimal('engagement_score', 5, 2)->nullable();
            $table->decimal('loyalty_score', 5, 2)->nullable();
            $table->decimal('churn_risk_score', 5, 2)->nullable();
            $table->timestamp('analysis_date');
            $table->timestamp('next_analysis_due')->nullable();
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع مع اسم أقصر
            $table->index(['user_id', 'analysis_type'], 'uba_user_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behavior_analyses');
    }
};
