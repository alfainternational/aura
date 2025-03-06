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
            $table->boolean('biometric_enabled')->default(false)->after('two_factor_enabled');
            $table->json('biometric_credentials')->nullable()->after('biometric_enabled');
            $table->timestamp('biometric_registered_at')->nullable()->after('biometric_credentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['biometric_enabled', 'biometric_credentials', 'biometric_registered_at']);
        });
    }
};
