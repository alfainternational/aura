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
            $table->string('phone_number')->nullable()->after('name');
            $table->string('profile_image')->nullable()->after('user_type');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            $table->boolean('is_verified')->default(false)->after('is_active');
            $table->string('verification_code')->nullable()->after('is_verified');
            $table->timestamp('phone_verified_at')->nullable()->after('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'profile_image',
                'last_login_at',
                'is_active',
                'is_verified',
                'verification_code',
                'phone_verified_at'
            ]);
        });
    }
};
