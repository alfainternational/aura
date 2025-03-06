<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SudanCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Sudan country ID
        $sudan = DB::table('countries')->where('code', 'SD')->first();
        
        if (!$sudan) {
            $this->command->error('Sudan country not found. Please run CountrySeeder first.');
            return;
        }

        // Lista de ciudades de Sudán
        $cityNames = [
            'Khartoum', 'Omdurman', 'Khartoum North (Bahri)', 'Port Sudan', 'Kassala',
            'Al-Ubayyid', 'Nyala', 'Wad Madani', 'Al-Fashir', 'Atbara',
            'Ad-Damazin', 'Al-Qadarif', 'Singa', 'Dongola', 'Rabak'
        ];
        
        // Verificar qué ciudades ya existen
        $existingCities = DB::table('cities')
            ->where('country_id', $sudan->id)
            ->whereIn('name', $cityNames)
            ->pluck('name')
            ->toArray();
        
        $cities = [];
        
        foreach ($cityNames as $cityName) {
            if (!in_array($cityName, $existingCities)) {
                $cities[] = [
                    'name' => $cityName,
                    'country_id' => $sudan->id,
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
