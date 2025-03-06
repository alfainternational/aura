<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Country;
use App\Models\AdminProfile;
use App\Models\CustomerProfile;
use App\Models\MerchantProfile;
use App\Models\AgentProfile;
use App\Models\MessengerProfile;
use Carbon\Carbon;

class ResetAndSeedDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate all user-related tables first (in reverse order of relationships)
        $this->command->info('Truncating user-related tables...');
        
        // Disable foreign key checks to allow truncating tables with foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate user-related tables
        DB::table('biometric_credentials')->truncate();
        DB::table('login_history')->truncate();
        DB::table('notifications')->truncate();
        DB::table('messenger_profiles')->truncate();
        DB::table('agent_profiles')->truncate();
        DB::table('merchant_profiles')->truncate();
        DB::table('customer_profiles')->truncate();
        DB::table('admin_profiles')->truncate();
        DB::table('users')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('All user-related tables have been truncated.');
        
        // Add Saudi Arabia to allowed countries
        $this->addSaudiArabia();
        
        // Seed users
        $this->seedUsers();
        
        $this->command->info('Database has been reset and seeded with new sample data.');
    }
    
    /**
     * Add Saudi Arabia to allowed countries
     */
    private function addSaudiArabia(): void
    {
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
            
            $this->command->info('Saudi Arabia has been added to allowed countries.');
        } else {
            // Update Saudi Arabia to allow registration
            $saudiArabia->update([
                'is_active' => true,
                'allow_registration' => true,
                'updated_at' => now(),
            ]);
            
            $this->command->info('Saudi Arabia has been updated to allow registration.');
        }
    }
    
    /**
     * Seed users with different roles
     */
    private function seedUsers(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@aura.com',
            'phone' => '966500000000',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'country_id' => Country::where('code', 'SD')->orWhere('code', 'SA')->first()->id,
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
        
        $this->command->info('Admin user created: admin@aura.com / password');
        
        // Create Customer User
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@aura.com',
            'phone' => '966500000001',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'country_id' => Country::where('code', 'SD')->orWhere('code', 'SA')->first()->id,
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
        
        $this->command->info('Customer user created: customer@aura.com / password');
        
        // Create Merchant User
        $merchant = User::create([
            'name' => 'Merchant User',
            'email' => 'merchant@aura.com',
            'phone' => '966500000002',
            'password' => Hash::make('password'),
            'user_type' => 'merchant',
            'country_id' => Country::where('code', 'SD')->orWhere('code', 'SA')->first()->id,
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
        
        $this->command->info('Merchant user created: merchant@aura.com / password');
        
        // Create Agent User
        $agent = User::create([
            'name' => 'Agent User',
            'email' => 'agent@aura.com',
            'phone' => '966500000003',
            'password' => Hash::make('password'),
            'user_type' => 'agent',
            'country_id' => Country::where('code', 'SD')->orWhere('code', 'SA')->first()->id,
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
        
        $this->command->info('Agent user created: agent@aura.com / password');
        
        // Create Messenger User
        $messenger = User::create([
            'name' => 'Messenger User',
            'email' => 'messenger@aura.com',
            'phone' => '966500000004',
            'password' => Hash::make('password'),
            'user_type' => 'messenger',
            'country_id' => Country::where('code', 'SD')->orWhere('code', 'SA')->first()->id,
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
        
        $this->command->info('Messenger user created: messenger@aura.com / password');
    }
}
