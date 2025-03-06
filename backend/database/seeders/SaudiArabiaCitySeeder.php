<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaudiArabiaCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Saudi Arabia country ID
        $saudiArabia = DB::table('countries')->where('code', 'SA')->first();
        
        if (!$saudiArabia) {
            $this->command->error('Saudi Arabia country not found. Please run CountrySeeder first.');
            return;
        }

        // Lista de ciudades de Arabia Saudita
        $cityNames = [
            'Riyadh', 'Jeddah', 'Mecca', 'Medina', 'Dammam',
            'Taif', 'Tabuk', 'Buraydah', 'Khobar', 'Abha',
            'Najran', 'Jazan', 'Hail', 'Jubail', 'Yanbu'
        ];
        
        // Verificar qué ciudades ya existen
        $existingCities = DB::table('cities')
            ->where('country_id', $saudiArabia->id)
            ->whereIn('name', $cityNames)
            ->pluck('name')
            ->toArray();
        
        $cities = [];
        
        foreach ($cityNames as $cityName) {
            if (!in_array($cityName, $existingCities)) {
                $cities[] = [
                    'name' => $cityName,
                    'country_id' => $saudiArabia->id,
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
