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
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type');
            $table->json('activity_details')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('device_info')->nullable();
            $table->json('location_data')->nullable();
            $table->string('session_id')->nullable();
            $table->integer('duration')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
            
            // Índices para optimizar búsquedas
            $table->index('activity_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
