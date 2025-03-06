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
        Schema::table('countries', function (Blueprint $table) {
            $table->boolean('allow_registration')->default(false)->after('is_active');
            $table->text('registration_message')->nullable()->after('allow_registration');
        });
        
        // تحديث دولة السودان للسماح بالتسجيل
        DB::table('countries')
            ->where('code', 'SD')
            ->update([
                'allow_registration' => true,
                'registration_message' => 'مرحباً بك في منصة أورا السودان'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['allow_registration', 'registration_message']);
        });
    }
};
