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
            $table->string('name_ar')->nullable();
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2 country code
            $table->string('phone_code', 10)->nullable();
            $table->string('flag')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->boolean('allow_registration')->default(false);
            $table->text('registration_message')->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('currency_name')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('language', 10)->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
        });
        
        // إضافة سجل دولة السودان كدولة افتراضية
        DB::table('countries')->insert([
            'name' => 'Sudan',
            'name_ar' => 'السودان',
            'code' => 'SD',
            'phone_code' => '249',
            'flag' => 'flags/sd.png',
            'is_active' => true,
            'is_default' => true,
            'allow_registration' => true,
            'registration_message' => 'مرحباً بك في منصة أورا السودان',
            'currency' => 'SDG',
            'currency_name' => 'Sudanese Pound',
            'currency_symbol' => 'ج.س',
            'language' => 'ar',
            'timezone' => 'Africa/Khartoum',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // إضافة المملكة العربية السعودية
        DB::table('countries')->insert([
            'name' => 'Saudi Arabia',
            'name_ar' => 'المملكة العربية السعودية',
            'code' => 'SA',
            'phone_code' => '966',
            'flag' => 'flags/sa.png',
            'is_active' => true,
            'is_default' => false,
            'allow_registration' => true,
            'registration_message' => 'مرحباً بك في منصة أورا السعودية',
            'currency' => 'SAR',
            'currency_name' => 'Saudi Riyal',
            'currency_symbol' => '﷼',
            'language' => 'ar',
            'timezone' => 'Asia/Riyadh',
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
