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
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('transaction_type'); // deposit, withdrawal
            $table->string('status'); // pending, completed, failed, cancelled
            $table->string('reference_number')->nullable();
            $table->string('description')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
            
            // Indices for improved query performance
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_type', 'status']);
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
