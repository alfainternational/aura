<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QatarCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Qatar country ID
        $qatar = DB::table('countries')->where('code', 'QA')->first();
        
        if (!$qatar) {
            $this->command->error('Qatar country not found. Please run CountrySeeder first.');
            return;
        }

        // Lista de ciudades de Qatar
        $cityNames = [
            'Doha', 'Al Rayyan', 'Al Wakrah', 'Al Khor', 'Umm Salal',
            'Al Daayen', 'Madinat ash Shamal', 'Al Shahaniya'
        ];
        
        // Verificar qué ciudades ya existen
        $existingCities = DB::table('cities')
            ->where('country_id', $qatar->id)
            ->whereIn('name', $cityNames)
            ->pluck('name')
            ->toArray();
        
        $cities = [];
        
        foreach ($cityNames as $cityName) {
            if (!in_array($cityName, $existingCities)) {
                $cities[] = [
                    'name' => $cityName,
                    'country_id' => $qatar->id,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($cities)) {
            DB::table('cities')->insert($cities);
        }
    }
}
