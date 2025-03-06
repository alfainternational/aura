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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 8, 2)->default(0);
            $table->string('transaction_type'); // deposit, withdrawal, payment
            $table->string('status')->default('pending'); // pending, completed, failed, cancelled
            $table->string('reference_id')->unique();
            $table->string('external_reference')->nullable();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->string('verification_code')->nullable(); // OTP para transacciones
            $table->integer('verification_attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index('transaction_type');
            $table->index('status');
            $table->index('reference_id');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
