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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('type')->comment('deposit, withdrawal, payment, refund, transfer, bonus');
            $table->string('status')->default('pending')->comment('pending, completed, failed, canceled');
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable()->index()->comment('ID de referencia externa (ej: ID de transacción de pago)');
            $table->string('reference_type')->nullable()->comment('Tipo de referencia (ej: order_payment, order_refund)');
            $table->json('transaction_details')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
