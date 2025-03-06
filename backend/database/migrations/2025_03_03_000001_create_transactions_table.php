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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('balance_after', 10, 2);
            $table->string('type'); // deposit, withdrawal, transfer, payment, refund, commission
            $table->string('status'); // pending, completed, failed, cancelled
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('description')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
            
            // Índices para mejorar el rendimiento de las consultas
            $table->index(['user_id', 'created_at']);
            $table->index(['wallet_id', 'created_at']);
            $table->index(['type', 'status']);
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
