<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ecommerce\Order;
use App\Models\Wallet;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCurrencySettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aura:update-currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all currency references to use Sudanese Pound (SDG) as default currency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating currency references in the database...');
        
        $defaultCurrency = Country::getDefaultCurrency();
        
        // 1. Actualizar moneda en órdenes
        $this->updateOrderCurrencies($defaultCurrency);
        
        // 2. Actualizar moneda en carteras (wallets)
        $this->updateWalletCurrencies($defaultCurrency);
        
        // 3. Actualizar cualquier otra referencia a moneda
        $this->updateOtherCurrencyReferences($defaultCurrency);
        
        $this->info('Currency settings updated successfully!');
    }
    
    /**
     * Actualizar moneda en todas las órdenes
     */
    private function updateOrderCurrencies($defaultCurrency)
    {
        // Verificar si la tabla tiene la columna currency
        $hasColumn = Schema::hasColumn('orders', 'currency');
        
        if ($hasColumn) {
            $count = Order::where('currency', '!=', $defaultCurrency)
                ->orWhereNull('currency')
                ->update(['currency' => $defaultCurrency]);
                
            $this->info("Updated {$count} orders to use {$defaultCurrency} as currency");
        } else {
            $this->warn("La tabla 'orders' no tiene la columna 'currency'. Omitiendo esta actualización.");
        }
    }
    
    /**
     * Actualizar moneda en todas las carteras
     */
    private function updateWalletCurrencies($defaultCurrency)
    {
        // Verificar si la tabla tiene la columna currency
        $hasColumn = Schema::hasColumn('wallets', 'currency');
        
        if ($hasColumn) {
            $count = Wallet::where('currency', '!=', $defaultCurrency)
                ->orWhereNull('currency')
                ->update(['currency' => $defaultCurrency]);
                
            $this->info("Updated {$count} wallets to use {$defaultCurrency} as currency");
        } else {
            $this->warn("La tabla 'wallets' no tiene la columna 'currency'. Omitiendo esta actualización.");
        }
    }
    
    /**
     * Actualizar otras referencias a moneda (configurable según necesidades)
     */
    private function updateOtherCurrencyReferences($defaultCurrency)
    {
        // Verificar si la tabla user_settings existe
        if (!Schema::hasTable('user_settings')) {
            $this->warn("La tabla 'user_settings' no existe. Omitiendo esta actualización.");
            return;
        }
        
        // Verificar si la tabla tiene columna ui_preferences
        $hasColumn = Schema::hasColumn('user_settings', 'ui_preferences');
        
        if ($hasColumn) {
            try {
                $count = DB::table('user_settings')
                    ->whereRaw("JSON_EXTRACT(ui_preferences, '$.currency_display') != ?", [$defaultCurrency])
                    ->update([
                        'ui_preferences' => DB::raw("JSON_SET(ui_preferences, '$.currency_display', '{$defaultCurrency}')")
                    ]);
                    
                $this->info("Updated {$count} user settings to use {$defaultCurrency} as display currency");
            } catch(\Exception $e) {
                $this->error("Error al actualizar 'user_settings': " . $e->getMessage());
            }
        } else {
            $this->warn("La tabla 'user_settings' no tiene la columna 'ui_preferences'. Omitiendo esta actualización.");
        }
    }
}
