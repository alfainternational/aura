<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2 country code
            $table->string('phone_code', 10)->nullable();
            $table->string('flag')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('default_currency', 3)->nullable();
            $table->string('language', 10)->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
        });
        
        // إضافة سجل دولة السودان كدولة افتراضية
        DB::table('countries')->insert([
            'name' => 'السودان',
            'code' => 'SD',
            'phone_code' => '249',
            'flag' => 'flags/sd.png',
            'is_active' => true,
            'default_currency' => 'SDG',
            'language' => 'ar',
            'timezone' => 'Africa/Khartoum',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
