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
        Schema::table('users', function (Blueprint $table) {
            // Añadir campos de ubicación
            $table->decimal('latitude', 10, 7)->nullable()->after('requires_kyc');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->foreignId('country_id')->nullable()->after('longitude')->constrained('countries')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('country_id')->constrained('cities')->nullOnDelete();
            $table->timestamp('last_location_update')->nullable()->after('city_id');
            
            // Indexar para búsquedas geográficas
            $table->index(['latitude', 'longitude']);
            $table->index(['country_id', 'city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['country_id', 'city_id']);
            
            // Eliminar columnas
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['latitude', 'longitude', 'country_id', 'city_id', 'last_location_update']);
        });
    }
};
