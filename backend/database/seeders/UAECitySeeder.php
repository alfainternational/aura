<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UAECitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get UAE country ID
        $uae = DB::table('countries')->where('code', 'AE')->first();
        
        if (!$uae) {
            $this->command->error('United Arab Emirates country not found. Please run CountrySeeder first.');
            return;
        }

        // Lista de ciudades de los Emiratos Árabes Unidos
        $cityNames = [
            'Dubai', 'Abu Dhabi', 'Sharjah', 'Al Ain', 'Ajman',
            'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Khor Fakkan', 'Dibba Al-Fujairah'
        ];
        
        // Verificar qué ciudades ya existen
        $existingCities = DB::table('cities')
            ->where('country_id', $uae->id)
            ->whereIn('name', $cityNames)
            ->pluck('name')
            ->toArray();
        
        $cities = [];
        
        foreach ($cityNames as $cityName) {
            if (!in_array($cityName, $existingCities)) {
                $cities[] = [
                    'name' => $cityName,
                    'country_id' => $uae->id,
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
