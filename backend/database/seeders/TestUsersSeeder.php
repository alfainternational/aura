<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Sudan and its cities
        $sudan = DB::table('countries')->where('code', 'SD')->first();
        $khartoum = DB::table('cities')->where('name', 'Khartoum')->where('country_id', $sudan->id)->first();
        
        if (!$sudan || !$khartoum) {
            $this->command->error('Sudan country or Khartoum city not found. Please run CountrySeeder and SudanCitySeeder first.');
            return;
        }

        // Lista de emails para verificar si los usuarios ya existen
        $emails = [
            'admin@aura.com',
            'supervisor@aura.com',
            'merchant@aura.com',
            'agent@aura.com',
            'messenger@aura.com',
            'customer@aura.com'
        ];
        
        $existingUsers = DB::table('users')->whereIn('email', $emails)->pluck('email')->toArray();

        // 1. Admin User
        if (!in_array('admin@aura.com', $existingUsers)) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@aura.com',
                'phone_number' => '+249123456788',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'admin',
                'profile_image' => null,
                'birth_date' => '1990-01-01',
                'gender' => 'male',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => false,
                'kyc_status' => 'approved',
                'kyc_verified_at' => Carbon::now(),
                'role' => json_encode(['admin']),
                'permissions' => json_encode(['users.view', 'users.edit', 'users.delete', 'content.moderate']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create Admin Profile
            DB::table('admin_profiles')->insert([
                'user_id' => $adminId,
                'position' => 'Administrator',
                'department' => 'User Management',
                'office_address' => 'Khartoum, Sudan',
                'office_phone' => '+249123456788',
                'emergency_contact' => '+249123456788',
                'permissions' => json_encode(['users.view', 'users.edit', 'users.delete', 'content.moderate']),
                'last_active_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Admin user created successfully.');
        } else {
            $this->command->info('Admin user already exists.');
        }

        // 2. Supervisor User
        if (!in_array('supervisor@aura.com', $existingUsers)) {
            $supervisorId = DB::table('users')->insertGetId([
                'name' => 'Supervisor User',
                'username' => 'supervisor',
                'email' => 'supervisor@aura.com',
                'phone_number' => '+249123456787',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'admin', // supervisor es un tipo de admin
                'profile_image' => null,
                'birth_date' => '1988-05-15',
                'gender' => 'female',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => false,
                'kyc_status' => 'approved',
                'kyc_verified_at' => Carbon::now(),
                'role' => json_encode(['supervisor']),
                'permissions' => json_encode(['messengers.view', 'messengers.edit', 'orders.view', 'orders.update']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Supervisor user created successfully.');
        } else {
            $this->command->info('Supervisor user already exists.');
        }

        // 3. Merchant User
        if (!in_array('merchant@aura.com', $existingUsers)) {
            $merchantId = DB::table('users')->insertGetId([
                'name' => 'Merchant User',
                'username' => 'merchant',
                'email' => 'merchant@aura.com',
                'phone_number' => '+249123456786',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'merchant',
                'profile_image' => null,
                'birth_date' => '1985-10-20',
                'gender' => 'male',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => true,
                'kyc_status' => 'approved',
                'kyc_verified_at' => Carbon::now(),
                'role' => json_encode(['merchant']),
                'permissions' => json_encode(['products.create', 'products.edit', 'products.delete', 'orders.view', 'orders.update']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create Merchant Profile
            DB::table('merchant_profiles')->insert([
                'user_id' => $merchantId,
                'business_name' => 'Aura Shop',
                'business_type' => 'retail',
                'business_description' => 'A retail shop selling electronics and accessories',
                'business_logo' => null,
                'business_cover' => null,
                'business_address' => 'Khartoum, Sudan',
                'business_phone' => '+249123456000',
                'business_email' => 'shop@aura.com',
                'business_website' => 'https://aurashop.com',
                'is_verified' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Merchant user created successfully.');
        } else {
            $this->command->info('Merchant user already exists.');
        }

        // 4. Agent User
        if (!in_array('agent@aura.com', $existingUsers)) {
            $agentId = DB::table('users')->insertGetId([
                'name' => 'Agent User',
                'username' => 'agent',
                'email' => 'agent@aura.com',
                'phone_number' => '+249123456785',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'agent',
                'profile_image' => null,
                'birth_date' => '1982-03-10',
                'gender' => 'male',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => true,
                'kyc_status' => 'approved',
                'kyc_verified_at' => Carbon::now(),
                'role' => json_encode(['agent']),
                'permissions' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Agent user created successfully.');
        } else {
            $this->command->info('Agent user already exists.');
        }

        // 5. Messenger User
        if (!in_array('messenger@aura.com', $existingUsers)) {
            $messengerId = DB::table('users')->insertGetId([
                'name' => 'Messenger User',
                'username' => 'messenger',
                'email' => 'messenger@aura.com',
                'phone_number' => '+249123456784',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'messenger',
                'profile_image' => null,
                'birth_date' => '1995-07-15',
                'gender' => 'male',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => true,
                'kyc_status' => 'approved',
                'kyc_verified_at' => Carbon::now(),
                'role' => json_encode(['messenger']),
                'permissions' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Messenger user created successfully.');
        } else {
            $this->command->info('Messenger user already exists.');
        }

        // 6. Customer User
        if (!in_array('customer@aura.com', $existingUsers)) {
            $customerId = DB::table('users')->insertGetId([
                'name' => 'Customer User',
                'username' => 'customer',
                'email' => 'customer@aura.com',
                'phone_number' => '+249123456783',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'customer',
                'profile_image' => null,
                'birth_date' => '1990-01-01',
                'gender' => 'female',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => false,
                'kyc_status' => 'pending',
                'role' => json_encode(['customer']),
                'permissions' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create Customer Profile
            DB::table('user_profiles')->insert([
                'user_id' => $customerId,
                'bio' => 'Regular customer from Khartoum',
                'birth_date' => '1990-01-01',
                'gender' => 'female',
                'profile_image' => null,
                'cover_image' => null,
                'website' => null,
                'social_links' => null,
                'language' => 'ar',
                'theme' => 'light',
                'notification_preferences' => json_encode([
                    'email' => true,
                    'push' => true,
                    'sms' => false
                ]),
                'privacy_settings' => json_encode([
                    'profile_visibility' => 'public',
                    'message_privacy' => 'everyone'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $this->command->info('Customer user created successfully.');
        } else {
            $this->command->info('Customer user already exists.');
        }
    }
}
