<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EgyptCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Egypt country ID
        $egypt = DB::table('countries')->where('code', 'EG')->first();
        
        if (!$egypt) {
            $this->command->error('Egypt country not found. Please run CountrySeeder first.');
            return;
        }

        // Lista de ciudades de Egipto
        $cityNames = [
            'Cairo', 'Alexandria', 'Giza', 'Shubra El Kheima', 'Port Said',
            'Suez', 'Luxor', 'Aswan', 'Asyut', 'Ismailia',
            'Faiyum', 'Zagazig', 'Damietta', 'Mansoura', 'Tanta'
        ];
        
        // Verificar qué ciudades ya existen
        $existingCities = DB::table('cities')
            ->where('country_id', $egypt->id)
            ->whereIn('name', $cityNames)
            ->pluck('name')
            ->toArray();
        
        $cities = [];
        
        foreach ($cityNames as $cityName) {
            if (!in_array($cityName, $existingCities)) {
                $cities[] = [
                    'name' => $cityName,
                    'country_id' => $egypt->id,
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
