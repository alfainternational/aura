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
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique(); // Dirección IP bloqueada
            $table->string('reason')->nullable(); // Razón del bloqueo
            $table->unsignedBigInteger('security_incident_id')->nullable(); // Incidente de seguridad relacionado
            $table->timestamp('expires_at')->nullable(); // Fecha de expiración del bloqueo (null = permanente)
            $table->timestamps();
            
            // Índices
            $table->index('ip_address');
            $table->index('expires_at');
            
            // Relaciones
            $table->foreign('security_incident_id')->references('id')->on('security_incidents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};
