<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // مدن السودان (country_id = 1)
        $sudanCities = [
            ['name' => 'الخرطوم', 'country_id' => 1, 'is_active' => true],
            ['name' => 'أم درمان', 'country_id' => 1, 'is_active' => true],
            ['name' => 'بحري', 'country_id' => 1, 'is_active' => true],
            ['name' => 'بورتسودان', 'country_id' => 1, 'is_active' => true],
            ['name' => 'ود مدني', 'country_id' => 1, 'is_active' => true],
            ['name' => 'الأبيض', 'country_id' => 1, 'is_active' => true],
            ['name' => 'نيالا', 'country_id' => 1, 'is_active' => true],
            ['name' => 'كسلا', 'country_id' => 1, 'is_active' => true],
            ['name' => 'الفاشر', 'country_id' => 1, 'is_active' => true],
            ['name' => 'عطبرة', 'country_id' => 1, 'is_active' => true],
        ];

        // إضافة تاريخ الإنشاء والتحديث
        foreach ($sudanCities as &$city) {
            $city['created_at'] = now();
            $city['updated_at'] = now();
        }

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('cities')->insert($sudanCities);
    }
}
