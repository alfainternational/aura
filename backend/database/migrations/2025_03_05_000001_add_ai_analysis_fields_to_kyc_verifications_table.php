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
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->json('ai_analysis')->nullable()->after('rejection_reason');
            $table->timestamp('ai_processed_at')->nullable()->after('ai_analysis');
            $table->float('document_score', 8, 2)->nullable()->after('ai_processed_at');
            $table->float('face_match_score', 8, 2)->nullable()->after('document_score');
            $table->float('risk_score', 8, 2)->nullable()->after('face_match_score');
            $table->string('ai_recommendation')->nullable()->after('risk_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropColumn([
                'ai_analysis',
                'ai_processed_at',
                'document_score',
                'face_match_score',
                'risk_score',
                'ai_recommendation'
            ]);
        });
    }
};
