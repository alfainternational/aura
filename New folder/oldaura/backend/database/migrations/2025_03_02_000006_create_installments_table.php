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
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_plan_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->timestamp('due_date');
            $table->timestamp('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('status')->default('pending'); // pending, paid, overdue, defaulted
            $table->integer('installment_number');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index('status');
            $table->index('due_date');
            $table->unique(['installment_plan_id', 'installment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
