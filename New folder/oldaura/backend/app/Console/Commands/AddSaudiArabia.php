<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;

class AddSaudiArabia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:add-saudi-arabia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Saudi Arabia to allowed countries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding Saudi Arabia to allowed countries...');
        
        // Check if Saudi Arabia already exists
        $saudiArabia = Country::where('code', 'SA')->first();
        
        if (!$saudiArabia) {
            // Create Saudi Arabia
            Country::create([
                'name' => 'Saudi Arabia',
                'code' => 'SA',
                'phone_code' => '+966',
                'currency' => 'SAR',
                'is_active' => true,
                'allow_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info('Saudi Arabia has been added to allowed countries.');
        } else {
            // Update Saudi Arabia to allow registration
            $saudiArabia->update([
                'is_active' => true,
                'allow_registration' => true,
                'updated_at' => now(),
            ]);
            
            $this->info('Saudi Arabia has been updated to allow registration.');
        }
        
        // Make sure Sudan exists and is active
        $sudan = Country::where('code', 'SD')->first();
        
        if (!$sudan) {
            // Create Sudan
            Country::create([
                'name' => 'Sudan',
                'code' => 'SD',
                'phone_code' => '+249',
                'currency' => 'SDG',
                'is_active' => true,
                'allow_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info('Sudan has been added to allowed countries.');
        } else {
            // Update Sudan to allow registration
            $sudan->update([
                'is_active' => true,
                'allow_registration' => true,
                'updated_at' => now(),
            ]);
            
            $this->info('Sudan has been updated to allow registration.');
        }
        
        $this->info('Countries have been updated successfully.');
    }
}
