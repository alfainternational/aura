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
        if (!Schema::hasTable('currencies')) {
            Schema::create('currencies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 3)->unique();
                $table->string('symbol', 10);
                $table->string('format', 50)->nullable();
                $table->integer('decimal_places')->default(2);
                $table->decimal('exchange_rate', 10, 6)->default(1);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('currency_exchange_rates')) {
            Schema::create('currency_exchange_rates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_currency_id')->constrained('currencies')->onDelete('cascade');
                $table->foreignId('to_currency_id')->constrained('currencies')->onDelete('cascade');
                $table->decimal('rate', 10, 6);
                $table->timestamp('date');
                $table->timestamps();

                // Usar un nombre de índice personalizado más corto
                $table->unique(['from_currency_id', 'to_currency_id', 'date'], 'exchange_rate_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_exchange_rates');
        Schema::dropIfExists('currencies');
    }
};
