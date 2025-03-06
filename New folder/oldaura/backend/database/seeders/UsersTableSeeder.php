<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء حساب الآدمن الرئيسي
        $admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@aura.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'role' => 'admin',
            'permissions' => json_encode(['all']),
            'phone_number' => '+249123456789',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
            'is_active' => true,
            'is_verified' => true,
            'requires_kyc' => false,
            'verification_code' => null,
        ]);
        
        // إنشاء الملف الشخصي للآدمن
        $admin->adminProfile()->create([
            'user_id' => $admin->id,
            'position' => 'System Administrator',
            'department' => 'IT',
            'office_address' => 'Khartoum, Sudan',
            'office_phone' => '+249123456789',
            'emergency_contact' => '+249123456789',
            'permissions' => json_encode(['all']),
        ]);
        
        // إنشاء حساب مشرف
        $supervisorUser = User::create([
            'name' => 'Supervisor User',
            'username' => 'supervisor',
            'email' => 'supervisor@aura.com',
            'user_type' => 'supervisor',
            'role' => json_encode(['supervisor']),
            'permissions' => json_encode(['view_dashboard', 'manage_messengers', 'manage_orders']),
            'phone_number' => '+249123456788',
            'profile_image' => null,
            'birth_date' => null,
            'gender' => 'male',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_verified' => true,
            'requires_kyc' => false,
        ]);

        // Create Supervisor Profile
        $supervisorProfile = $supervisorUser->supervisorProfile()->create([
            'department' => 'Operations',
            'position' => 'Zone Supervisor',
            'rating' => 0,
            'managed_orders_count' => 0,
            'managed_messengers_count' => 0,
        ]);
        
        // Create a zone
        $zone = DB::table('zones')->insertGetId([
            'name' => 'Khartoum Central',
            'code' => null,
            'city' => 'Khartoum',
            'country' => 'Sudan',
            'description' => 'Central area of Khartoum',
            'status' => 'active',
            'polygon' => '[[15.59,32.53],[15.59,32.54],[15.6,32.54],[15.6,32.53],[15.59,32.53]]',
            'base_delivery_fee' => 0,
            'per_km_charge' => 0,
            'minimum_delivery_fee' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Assign zone to supervisor
        DB::table('supervisor_zones')->insert([
            'supervisor_profile_id' => $supervisorProfile->id,
            'zone_id' => $zone,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // إنشاء حساب تاجر
        $merchant = User::create([
            'name' => 'Merchant User',
            'username' => 'merchant',
            'email' => 'merchant@aura.com',
            'password' => Hash::make('password'),
            'user_type' => 'merchant',
            'role' => 'merchant',
            'permissions' => json_encode(['store', 'products', 'orders']),
            'phone_number' => '+249123456787',
            'gender' => 'male',
            'birth_date' => '1988-08-12',
            'is_active' => true,
            'is_verified' => true,
            'requires_kyc' => false,
            'verification_code' => null,
        ]);
        
        // إنشاء الملف الشخصي للتاجر
        $merchant->merchantProfile()->create([
            'user_id' => $merchant->id,
            'store_name' => 'AURA Store',
            'store_logo' => null,
            'store_banner' => null,
            'store_description' => 'Electronics store with high quality products',
            'business_type' => 'Retail',
            'business_registration_number' => '12345678',
            'tax_number' => '12345678',
            'contact_email' => 'store@aura.com',
            'contact_phone' => '+249123456787',
            'address' => 'Khartoum, Sudan',
            'city' => 'Khartoum',
            'is_open' => true,
            'opening_hours' => '9:00 AM - 10:00 PM',
            'status' => 'approved',
        ]);
        
        // إنشاء حساب وكيل
        $agent = User::create([
            'name' => 'Agent User',
            'username' => 'agent',
            'email' => 'agent@aura.com',
            'password' => Hash::make('password'),
            'user_type' => 'agent',
            'role' => 'agent',
            'permissions' => json_encode(['transactions', 'customers']),
            'phone_number' => '+249123456786',
            'gender' => 'male',
            'birth_date' => '1995-02-10',
            'is_active' => true,
            'is_verified' => true,
            'requires_kyc' => false,
        ]);

        // إنشاء الملف الشخصي للوكيل
        $agent->agentProfile()->create([
            'user_id' => $agent->id,
            'agency_name' => 'AURA Agency',
            'agency_logo' => null,
            'agency_type' => 'Finance',
            'license_number' => '98765432',
            'license_document' => null,
            'national_id' => 'AG12345678',
            'id_document' => 'id_doc_agent.pdf',
            'contact_email' => 'agent@aura.com',
            'contact_phone' => '+249123456786',
            'address' => 'Omdurman, Sudan',
            'city' => 'Omdurman',
            'reference_code' => Str::random(8),
            'status' => 'approved',
        ]);
        
        // إنشاء حساب مندوب
        $messenger = User::create([
            'name' => 'Messenger User',
            'username' => 'messenger',
            'email' => 'messenger@aura.com',
            'password' => Hash::make('password'),
            'user_type' => 'messenger',
            'role' => 'messenger',
            'permissions' => json_encode(['deliveries']),
            'phone_number' => '+249123456785',
            'gender' => 'male',
            'birth_date' => '1997-07-22',
            'is_active' => true,
            'is_verified' => true,
            'requires_kyc' => false,
            'verification_code' => null,
        ]);
        
        // أولاً سنحتاج إلى إنشاء سجل للمركبة والمنطقة للمندوب
        $vehicle = \App\Models\Vehicle::create([
            'user_id' => $messenger->id,
            'type' => 'motorcycle',
            'make' => 'Honda',
            'model' => 'CBR',
            'year' => '2020',
            'color' => 'Black',
            'license_plate' => 'ABC123',
            'is_verified' => true,
        ]);
        
        $zone = \App\Models\Zone::create([
            'name' => 'Khartoum Central',
            'city' => 'Khartoum',
            'country' => 'Sudan',
            'description' => 'Central area of Khartoum',
            'status' => 'active',
            'polygon' => json_encode([
                [15.5900, 32.5300],
                [15.5900, 32.5400],
                [15.6000, 32.5400],
                [15.6000, 32.5300],
                [15.5900, 32.5300],
            ]),
        ]);
        
        // إنشاء الملف الشخصي للمندوب
        $messenger->messengerProfile()->create([
            'user_id' => $messenger->id,
            'national_id' => 'ME12345678',
            'id_document' => 'id_doc_messenger.pdf',
            'driving_license' => 'DL12345678',
            'license_document' => 'license_doc.pdf',
            'vehicle_id' => $vehicle->id,
            'zone_id' => $zone->id,
            'address' => 'Bahri, Sudan',
            'city' => 'Khartoum',
            'status' => 'approved',
            'is_online' => true,
            'delivery_preference' => 'both',
            'work_hours' => '9:00 AM - 9:00 PM',
            'reference_code' => Str::random(8),
        ]);
        
        // إنشاء حساب عميل/مستخدم
        $customer = User::create([
            'name' => 'Customer User',
            'username' => 'customer',
            'email' => 'customer@aura.com',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'role' => 'customer',
            'permissions' => json_encode(['profile', 'orders']),
            'phone_number' => '+249123456784',
            'gender' => 'female',
            'birth_date' => '1998-11-05',
            'is_active' => true,
            'is_verified' => true,
            'requires_kyc' => false,
            'verification_code' => null,
        ]);
        
        // إنشاء الملف الشخصي للعميل
        $customer->customerProfile()->create([
            'user_id' => $customer->id,
            'is_guest' => false,
            'referral_code' => Str::random(8),
        ]);
        
        // إضافة عنوان للعميل
        $customer->addresses()->create([
            'user_id' => $customer->id,
            'title' => 'Home',
            'address' => 'Khartoum Bahri, Sudan',
            'is_default' => true,
            'latitude' => 15.6335,
            'longitude' => 32.5340,
        ]);
    }
}
