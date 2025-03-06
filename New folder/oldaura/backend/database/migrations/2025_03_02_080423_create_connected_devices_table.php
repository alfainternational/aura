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
        Schema::create('connected_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('token')->nullable(); // For device identification
            $table->timestamp('last_active_at')->nullable();
            $table->boolean('is_current_device')->default(false);
            $table->boolean('is_trusted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connected_devices');
    }
};
