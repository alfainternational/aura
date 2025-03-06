<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إنشاء جدول حوادث الأمان
     */
    public function up(): void
    {
        if (!Schema::hasTable('security_incidents')) {
            Schema::create('security_incidents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->enum('type', [
                    'failed_login', 'suspicious_login', 'brute_force', 'unauthorized_access',
                    'data_breach', 'malware', 'phishing', 'ddos', 'other'
                ])->default('other');
                $table->text('description');
                $table->json('details')->nullable();
                $table->enum('status', ['detected', 'investigating', 'mitigated', 'resolved', 'false_positive'])->default('detected');
                $table->timestamp('detected_at');
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->string('affected_resources')->nullable();
                $table->timestamps();
                
                // إضافة فهرس للبحث السريع مع أسماء أقصر
                $table->index(['user_id', 'severity', 'type'], 'si_user_sev_type_idx');
                $table->index(['status', 'detected_at'], 'si_status_date_idx');
                $table->index('ip_address', 'si_ip_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_incidents');
    }
};
