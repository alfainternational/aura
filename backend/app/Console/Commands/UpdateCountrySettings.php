<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class UpdateCountrySettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aura:update-countries 
                            {--reset : Reset and apply default settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update country settings to set Sudan as default country and Saudi Arabia for testing only';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating country settings...');

        // Si la opción de reset está presente, restablecer todas las configuraciones
        if ($this->option('reset')) {
            $this->resetAllCountries();
        } else {
            $this->updateCountries();
        }

        $this->info('Country settings updated successfully!');
    }

    /**
     * Update countries based on new requirements
     */
    private function updateCountries()
    {
        // 1. Establecer Sudán como país predeterminado y activo
        $sudan = Country::where('code', 'SD')->first();
        if ($sudan) {
            $sudan->update([
                'is_default' => true,
                'is_active' => true,
                'allow_registration' => true
            ]);
            $this->info('Sudan set as default country');
        } else {
            $this->error('Sudan country not found');
        }

        // 2. Establecer Arabia Saudita como activa pero solo para pruebas
        $saudiArabia = Country::where('code', 'SA')->first();
        if ($saudiArabia) {
            $saudiArabia->update([
                'is_default' => false,
                'is_active' => true,
                'allow_registration' => true
            ]);
            $this->info('Saudi Arabia set as active for testing');
        }

        // 3. Desactivar todos los demás países
        Country::whereNotIn('code', ['SD', 'SA'])->update([
            'is_default' => false,
            'is_active' => false,
            'allow_registration' => false
        ]);
        $this->info('All other countries deactivated');
    }

    /**
     * Reset all country settings and apply defaults
     */
    private function resetAllCountries()
    {
        // Eliminar países actuales
        $this->info('Resetting all countries...');
        DB::table('countries')->delete();

        // Ejecutar seeder
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\CountrySeeder',
            '--force' => true
        ]);
    }
}
