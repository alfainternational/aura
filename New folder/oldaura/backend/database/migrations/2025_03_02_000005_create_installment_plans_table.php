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
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('installment_amount', 12, 2);
            $table->integer('number_of_installments');
            $table->string('frequency')->default('monthly');
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('status')->default('active'); // active, completed, defaulted, cancelled
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['merchant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};
