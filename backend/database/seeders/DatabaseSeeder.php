<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            SudanCitySeeder::class,
            EgyptCitySeeder::class,
            SaudiArabiaCitySeeder::class,
            UAECitySeeder::class,
            QatarCitySeeder::class,
            UserSeeder::class,
            TestUsersSeeder::class,
        ]);
    }
}
