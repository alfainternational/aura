<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
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
            'superadmin@aura.com',
            'admin@aura.com',
            'merchant@aura.com',
            'user1@example.com',
            'user2@example.com'
        ];
        
        $existingUsers = DB::table('users')->whereIn('email', $emails)->pluck('email')->toArray();

        // Super Admin User
        if (!in_array('superadmin@aura.com', $existingUsers)) {
            $superAdminId = DB::table('users')->insertGetId([
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@aura.com',
                'phone_number' => '+249123456789',
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
                'role' => json_encode(['super_admin']),
                'permissions' => json_encode(['*']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create Super Admin Profile
            DB::table('admin_profiles')->insert([
                'user_id' => $superAdminId,
                'position' => 'Super Administrator',
                'department' => 'Management',
                'office_address' => 'Khartoum, Sudan',
                'office_phone' => '+249123456789',
                'emergency_contact' => '+249123456789',
                'permissions' => json_encode(['*']),
                'last_active_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Admin User
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
                'birth_date' => '1992-05-15',
                'gender' => 'female',
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
        }

        // Merchant User
        if (!in_array('merchant@aura.com', $existingUsers)) {
            $merchantId = DB::table('users')->insertGetId([
                'name' => 'Merchant User',
                'username' => 'merchant',
                'email' => 'merchant@aura.com',
                'phone_number' => '+249123456787',
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
        }

        // Regular User 1
        if (!in_array('user1@example.com', $existingUsers)) {
            $userId1 = DB::table('users')->insertGetId([
                'name' => 'Regular User 1',
                'username' => 'user1',
                'email' => 'user1@example.com',
                'phone_number' => '+249123456786',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'customer',
                'profile_image' => null,
                'birth_date' => '1990-01-01',
                'gender' => 'male',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => false,
                'kyc_status' => 'pending',
                'role' => json_encode(['user']),
                'permissions' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create User Profile
            DB::table('user_profiles')->insert([
                'user_id' => $userId1,
                'bio' => 'Regular user from Khartoum',
                'birth_date' => '1990-01-01',
                'gender' => 'male',
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
                    'message_privacy' => 'contacts_only'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Regular User 2
        if (!in_array('user2@example.com', $existingUsers)) {
            $userId2 = DB::table('users')->insertGetId([
                'name' => 'Regular User 2',
                'username' => 'user2',
                'email' => 'user2@example.com',
                'phone_number' => '+249123456785',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
                'phone_verified_at' => Carbon::now(),
                'country_id' => $sudan->id,
                'city_id' => $khartoum->id,
                'user_type' => 'customer',
                'profile_image' => null,
                'birth_date' => '1992-05-15',
                'gender' => 'female',
                'is_active' => true,
                'is_verified' => true,
                'requires_kyc' => false,
                'kyc_status' => 'pending',
                'role' => json_encode(['user']),
                'permissions' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create User Profile
            DB::table('user_profiles')->insert([
                'user_id' => $userId2,
                'bio' => 'Another regular user from Khartoum',
                'birth_date' => '1992-05-15',
                'gender' => 'female',
                'profile_image' => null,
                'cover_image' => null,
                'website' => null,
                'social_links' => null,
                'language' => 'ar',
                'theme' => 'dark',
                'notification_preferences' => json_encode([
                    'email' => true,
                    'push' => true,
                    'sms' => true
                ]),
                'privacy_settings' => json_encode([
                    'profile_visibility' => 'private',
                    'message_privacy' => 'everyone'
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
