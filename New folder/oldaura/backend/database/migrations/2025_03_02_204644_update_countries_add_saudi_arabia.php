<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;

class UpdateCountriesAddSaudiArabia extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the name_ar column exists in the countries table
        if (!Schema::hasColumn('countries', 'name_ar')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->string('name_ar')->nullable()->after('name');
                $table->string('currency_name')->nullable()->after('currency');
                $table->string('currency_symbol')->nullable()->after('currency_name');
                $table->boolean('is_default')->default(false)->after('is_active');
            });
        }
        
        // Add Saudi Arabia if it doesn't exist
        $saudiArabia = Country::where('code', 'SA')->first();
        
        if (!$saudiArabia) {
            Country::create([
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'code' => 'SA',
                'phone_code' => '+966',
                'currency' => 'SAR',
                'currency_name' => 'Saudi Riyal',
                'currency_symbol' => '﷼',
                'is_active' => true,
                'is_default' => false,
                'allow_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $saudiArabia->update([
                'is_active' => true,
                'allow_registration' => true,
                'updated_at' => now(),
            ]);
        }
        
        // Make sure Sudan exists and is active
        $sudan = Country::where('code', 'SD')->first();
        
        if (!$sudan) {
            Country::create([
                'name' => 'Sudan',
                'name_ar' => 'السودان',
                'code' => 'SD',
                'phone_code' => '+249',
                'currency' => 'SDG',
                'currency_name' => 'Sudanese Pound',
                'currency_symbol' => 'ج.س',
                'is_active' => true,
                'is_default' => true,
                'allow_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $sudan->update([
                'is_active' => true,
                'is_default' => true,
                'allow_registration' => true,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to remove Saudi Arabia in down method
        // Just remove the added columns if needed
        if (Schema::hasColumn('countries', 'name_ar')) {
            Schema::table('countries', function (Blueprint $table) {
                $table->dropColumn('name_ar');
                $table->dropColumn('currency_name');
                $table->dropColumn('currency_symbol');
                $table->dropColumn('is_default');
            });
        }
    }
}
