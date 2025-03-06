<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Country;
use App\Models\AdminProfile;
use App\Models\CustomerProfile;
use App\Models\MerchantProfile;
use App\Models\AgentProfile;
use App\Models\MessengerProfile;

class ResetAndSeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-and-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset user data and add sample users including admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete all existing user data. Are you sure you want to continue?', true)) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Starting database reset and seed...');

        // Add Saudi Arabia to allowed countries
        $this->addSaudiArabia();
        
        // Reset and seed users
        $this->resetAndSeedUsers();
        
        $this->info('Database has been reset and seeded with new sample data.');
    }

    /**
     * Add Saudi Arabia to allowed countries
     */
    private function addSaudiArabia()
    {
        $this->info('Adding Saudi Arabia to allowed countries...');
        
        // Check if Saudi Arabia already exists
        $saudiArabia = Country::where('code', 'SA')->first();
        
        if (!$saudiArabia) {
            // Create Saudi Arabia
            Country::create([
                'name' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'code' => 'SA',
                'phone_code' => '+966',
                'currency' => 'SAR',
                'currency_name' => 'Saudi Riyal',
                'currency_symbol' => '﷼',
                'is_active' => true,
                'is_default' => false,
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
                'name_ar' => 'السودان',
                'code' => 'SD',
                'phone_code' => '+249',
                'currency' => 'SDG',
                'currency_name' => 'Sudanese Pound',
                'currency_symbol' => 'ج.س',
                'is_active' => true,
                'is_default' => true,
                'allow_registration' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->info('Sudan has been added to allowed countries.');
        } else {
            // Update Sudan to allow registration
            $sudan->update([
                'is_active' => true,
                'is_default' => true,
                'allow_registration' => true,
                'updated_at' => now(),
            ]);
            
            $this->info('Sudan has been updated to allow registration.');
        }
    }

    /**
     * Reset and seed users
     */
    private function resetAndSeedUsers()
    {
        $this->info('Resetting and seeding users...');
        
        try {
            // Truncate all user-related tables first (in reverse order of relationships)
            $this->info('Truncating user-related tables...');
            
            // Disable foreign key checks to allow truncating tables with foreign key constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Truncate user-related tables
            if (Schema::hasTable('biometric_credentials')) {
                DB::table('biometric_credentials')->truncate();
            }
            
            if (Schema::hasTable('login_histories')) {
                DB::table('login_histories')->truncate();
            } else if (Schema::hasTable('login_history')) {
                DB::table('login_history')->truncate();
            }
            
            if (Schema::hasTable('notifications')) {
                DB::table('notifications')->truncate();
            }
            
            if (Schema::hasTable('messenger_profiles')) {
                DB::table('messenger_profiles')->truncate();
            }
            
            if (Schema::hasTable('agent_profiles')) {
                DB::table('agent_profiles')->truncate();
            }
            
            if (Schema::hasTable('merchant_profiles')) {
                DB::table('merchant_profiles')->truncate();
            }
            
            if (Schema::hasTable('customer_profiles')) {
                DB::table('customer_profiles')->truncate();
            }
            
            if (Schema::hasTable('admin_profiles')) {
                DB::table('admin_profiles')->truncate();
            }
            
            if (Schema::hasTable('users')) {
                DB::table('users')->truncate();
            }
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->info('All user-related tables have been truncated.');
        } catch (\Exception $e) {
            $this->error('Error truncating tables: ' . $e->getMessage());
            $this->info('Continuing with user creation...');
        }
        
        // Get country ID
        $country = Country::where('code', 'SD')->orWhere('code', 'SA')->first();
        
        if (!$country) {
            $this->error('No country found with code SD or SA. Please run the country migration first.');
            return;
        }
        
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@aura.com',
            'phone' => '966500000000',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'country_id' => $country->id,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        AdminProfile::create([
            'user_id' => $admin->id,
            'position' => 'System Administrator',
            'department' => 'IT',
            'bio' => 'Main system administrator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info('Admin user created: admin@aura.com / password');
        
        // Create Customer User
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@aura.com',
            'phone' => '966500000001',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'country_id' => $country->id,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        CustomerProfile::create([
            'user_id' => $customer->id,
            'address' => 'Sample Address 123',
            'city' => 'Riyadh',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info('Customer user created: customer@aura.com / password');
        
        // Create Merchant User
        $merchant = User::create([
            'name' => 'Merchant User',
            'email' => 'merchant@aura.com',
            'phone' => '966500000002',
            'password' => Hash::make('password'),
            'user_type' => 'merchant',
            'country_id' => $country->id,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        MerchantProfile::create([
            'user_id' => $merchant->id,
            'business_name' => 'Sample Business',
            'business_type' => 'Retail',
            'business_address' => 'Business Address 123',
            'business_city' => 'Riyadh',
            'tax_number' => 'TX12345678',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info('Merchant user created: merchant@aura.com / password');
        
        // Create Agent User
        $agent = User::create([
            'name' => 'Agent User',
            'email' => 'agent@aura.com',
            'phone' => '966500000003',
            'password' => Hash::make('password'),
            'user_type' => 'agent',
            'country_id' => $country->id,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        AgentProfile::create([
            'user_id' => $agent->id,
            'agency_name' => 'Sample Agency',
            'agency_address' => 'Agency Address 123',
            'agency_city' => 'Riyadh',
            'commission_rate' => 5.0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info('Agent user created: agent@aura.com / password');
        
        // Create Messenger User
        $messenger = User::create([
            'name' => 'Messenger User',
            'email' => 'messenger@aura.com',
            'phone' => '966500000004',
            'password' => Hash::make('password'),
            'user_type' => 'messenger',
            'country_id' => $country->id,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        MessengerProfile::create([
            'user_id' => $messenger->id,
            'vehicle_type' => 'Motorcycle',
            'vehicle_plate' => 'ABC123',
            'zone_id' => null,
            'is_available' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info('Messenger user created: messenger@aura.com / password');
    }
}
