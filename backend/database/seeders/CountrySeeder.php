<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countryCodes = ['SD', 'EG', 'SA', 'AE', 'QA'];
        $existingCountries = DB::table('countries')->whereIn('code', $countryCodes)->pluck('code')->toArray();
        
        $countries = [];
        
        // Sudán
        if (!in_array('SD', $existingCountries)) {
            $countries[] = [
                'name' => 'Sudan',
                'name_ar' => 'السودان',
                'code' => 'SD',
                'phone_code' => '+249',
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Egipto
        if (!in_array('EG', $existingCountries)) {
            $countries[] = [
                'name' => 'Egypt',
                'name_ar' => 'مصر',
                'code' => 'EG',
                'phone_code' => '+20',
                'flag' => 'flags/eg.png',
                'is_active' => false,
                'is_default' => false,
                'allow_registration' => false,
                'registration_message' => 'مرحباً بك في منصة أورا مصر',
                'currency' => 'EGP',
                'currency_name' => 'Egyptian Pound',
                'currency_symbol' => 'ج.م',
                'language' => 'ar',
                'timezone' => 'Africa/Cairo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Arabia Saudita
        if (!in_array('SA', $existingCountries)) {
            $countries[] = [
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'code' => 'SA',
                'phone_code' => '+966',
                'flag' => 'flags/sa.png',
                'is_active' => true,
                'is_default' => false,
                'allow_registration' => true,
                'registration_message' => 'مرحباً بك في منصة أورا السعودية',
                'currency' => 'SAR',
                'currency_name' => 'Saudi Riyal',
                'currency_symbol' => 'ر.س',
                'language' => 'ar',
                'timezone' => 'Asia/Riyadh',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Emiratos Árabes Unidos
        if (!in_array('AE', $existingCountries)) {
            $countries[] = [
                'name' => 'United Arab Emirates',
                'name_ar' => 'الإمارات العربية المتحدة',
                'code' => 'AE',
                'phone_code' => '+971',
                'flag' => 'flags/ae.png',
                'is_active' => false,
                'is_default' => false,
                'allow_registration' => false,
                'registration_message' => 'مرحباً بك في منصة أورا الإمارات',
                'currency' => 'AED',
                'currency_name' => 'UAE Dirham',
                'currency_symbol' => 'د.إ',
                'language' => 'ar',
                'timezone' => 'Asia/Dubai',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Qatar
        if (!in_array('QA', $existingCountries)) {
            $countries[] = [
                'name' => 'Qatar',
                'name_ar' => 'قطر',
                'code' => 'QA',
                'phone_code' => '+974',
                'flag' => 'flags/qa.png',
                'is_active' => false,
                'is_default' => false,
                'allow_registration' => false,
                'registration_message' => 'مرحباً بك في منصة أورا قطر',
                'currency' => 'QAR',
                'currency_name' => 'Qatari Riyal',
                'currency_symbol' => 'ر.ق',
                'language' => 'ar',
                'timezone' => 'Asia/Qatar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        if (!empty($countries)) {
            DB::table('countries')->insert($countries);
        }
    }
}
