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
            $table->enum('kyc_status', ['not_submitted', 'pending', 'approved', 'rejected'])->default('not_submitted')->after('requires_kyc');
            $table->text('kyc_rejection_reason')->nullable()->after('kyc_status');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_rejection_reason');
            $table->timestamp('kyc_verified_at')->nullable()->after('kyc_submitted_at');
            $table->integer('kyc_step')->default(0)->after('kyc_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kyc_status');
            $table->dropColumn('kyc_rejection_reason');
            $table->dropColumn('kyc_submitted_at');
            $table->dropColumn('kyc_verified_at');
            $table->dropColumn('kyc_step');
        });
    }
};
