<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول توقعات المبيعات
     */
    public function up(): void
    {
        Schema::create('sales_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('prediction_type')->default('product'); // product, category, overall
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->date('prediction_date');
            $table->date('prediction_period_start');
            $table->date('prediction_period_end');
            $table->decimal('predicted_sales_amount', 12, 2);
            $table->integer('predicted_sales_quantity')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->json('factors')->nullable(); // Factors that influenced the prediction
            $table->json('data_points')->nullable(); // Historical data points used
            $table->decimal('actual_sales_amount', 12, 2)->nullable(); // For verification later
            $table->integer('actual_sales_quantity')->nullable(); // For verification later
            $table->decimal('accuracy_percentage', 5, 2)->nullable(); // How accurate was the prediction
            $table->timestamps();
            
            // إضافة فهرس للبحث السريع مع أسماء أقصر
            $table->index(['merchant_id', 'prediction_type'], 'sp_merchant_type_idx');
            $table->index(['product_id', 'prediction_date'], 'sp_product_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_predictions');
    }
};
