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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('require_otp')->default(true);
            $table->decimal('otp_threshold', 12, 2)->default(1000);
            $table->json('notification_preferences')->nullable();
            $table->json('ui_preferences')->nullable();
            $table->timestamps();
            
            // Índice para búsquedas rápidas por usuario
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
