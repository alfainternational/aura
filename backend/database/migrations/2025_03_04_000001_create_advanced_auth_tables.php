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
        // جدول جلسات المستخدمين المدارة
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->text('location')->nullable();
            $table->timestamp('login_at');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_trusted')->default(false);
            $table->timestamps();
        });

        // جدول لتخزين بيانات البصمة الحيوية المشفرة
        Schema::create('biometric_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_id')->index();
            $table->string('device_name');
            $table->text('biometric_token');  // تخزن مشفرة
            $table->text('biometric_public_key');
            $table->string('biometric_type')->default('fingerprint'); // fingerprint, face, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'device_id']);
        });

        // جدول لتخزين محاولات تسجيل الدخول الفاشلة ورصد النشاط المشبوه
        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('incident_type'); // failed_login, suspicious_activity, account_locked, etc.
            $table->text('incident_details')->nullable();
            $table->text('location_data')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolution_notes')->nullable();
            $table->timestamps();
        });

        // جدول لتخزين وثائق التحقق من الهوية KYC
        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // id_card, passport, driving_license, etc.
            $table->string('document_number');
            $table->string('document_file_path');
            $table->string('selfie_file_path')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('verified_by')->nullable();
            $table->json('ai_verification_results')->nullable();
            $table->timestamps();
        });

        // جدول لوسائل المصادقة الثنائية المتعددة
        Schema::create('two_factor_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('method_type'); // sms, email, app, recovery
            $table->string('identifier')->nullable(); // phone, email, etc.
            $table->text('secret')->nullable(); // encrypted TOTP secret
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_methods');
        Schema::dropIfExists('kyc_documents');
        Schema::dropIfExists('security_incidents');
        Schema::dropIfExists('biometric_credentials');
        Schema::dropIfExists('user_sessions');
    }
};
