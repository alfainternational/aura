<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $country;
    protected $city;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء بلد ومدينة
        $this->country = Country::create([
            'name' => 'Sudan',
            'code' => 'SD',
            'phone_code' => '+249',
        ]);
        
        $this->city = City::create([
            'name' => 'Khartoum',
            'country_id' => $this->country->id,
        ]);
        
        // إنشاء مستخدم
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'user_type' => 'user',
            'email_verified_at' => now(),
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
        ]);
    }

    /**
     * اختبار تسجيل مستخدم جديد
     */
    public function test_user_can_register()
    {
        Event::fake([Registered::class]);
        
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'phone' => '123456789',
        ];
        
        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                    'created_at',
                ],
                'token',
            ]);
            
        Event::assertDispatched(Registered::class);
        
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'user_type' => 'user',
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'phone' => '123456789',
        ]);
    }

    /**
     * اختبار تسجيل الدخول
     */
    public function test_user_can_login()
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
        
        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                ],
                'token',
            ]);
    }

    /**
     * اختبار تسجيل الدخول بمعلومات غير صحيحة
     */
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];
        
        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401);
    }

    /**
     * اختبار تسجيل الخروج
     */
    public function test_user_can_logout()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200);
        
        // التحقق من حذف التوكن
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * اختبار طلب إعادة تعيين كلمة المرور
     */
    public function test_user_can_request_password_reset()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => $this->user->email,
        ]);

        $response->assertStatus(200);
    }

    /**
     * اختبار إعادة تعيين كلمة المرور
     */
    public function test_user_can_reset_password()
    {
        // إنشاء توكن إعادة تعيين كلمة المرور
        $token = Password::createToken($this->user);
        
        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $this->user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        
        // التحقق من تحديث كلمة المرور
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->user->password));
    }

    /**
     * اختبار التحقق من البريد الإلكتروني
     */
    public function test_user_can_verify_email()
    {
        // إنشاء مستخدم غير مؤكد البريد
        $unverifiedUser = User::factory()->create([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);
        
        // إنشاء توكن التحقق
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $unverifiedUser->id, 'hash' => sha1($unverifiedUser->email)]
        );
        
        // استخراج المسار من URL الكامل
        $parsedUrl = parse_url($verificationUrl);
        $routePath = $parsedUrl['path'] . '?' . $parsedUrl['query'];
        
        $response = $this->get($routePath);

        // قد يكون هناك إعادة توجيه أو استجابة ناجحة
        $this->assertTrue($response->status() == 302 || $response->status() == 200);
        
        // التحقق من تحديث حالة التحقق
        $unverifiedUser->refresh();
        $this->assertNotNull($unverifiedUser->email_verified_at);
    }

    /**
     * اختبار إعادة إرسال رابط التحقق
     */
    public function test_user_can_resend_verification_email()
    {
        // إنشاء مستخدم غير مؤكد البريد
        $unverifiedUser = User::factory()->create([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);
        
        $token = $unverifiedUser->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/email/verification-notification');

        $response->assertStatus(200);
    }

    /**
     * اختبار تحديث الملف الشخصي
     */
    public function test_user_can_update_profile()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        $updatedData = [
            'name' => 'Updated Name',
            'phone' => '987654321',
            'bio' => 'This is my updated bio',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile', $updatedData);

        $response->assertStatus(200);
        
        // التحقق من تحديث البيانات
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'phone' => '987654321',
            'bio' => 'This is my updated bio',
        ]);
    }

    /**
     * اختبار تحديث كلمة المرور
     */
    public function test_user_can_update_password()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        $passwordData = [
            'current_password' => 'password123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/password', $passwordData);

        $response->assertStatus(200);
        
        // التحقق من تحديث كلمة المرور
        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $this->user->password));
    }

    /**
     * اختبار تفعيل المصادقة الثنائية
     */
    public function test_user_can_enable_two_factor_authentication()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/user/two-factor-authentication');

        $response->assertStatus(200);
        
        // التحقق من تفعيل المصادقة الثنائية
        $this->user->refresh();
        $this->assertTrue($this->user->two_factor_enabled);
    }

    /**
     * اختبار تعطيل المصادقة الثنائية
     */
    public function test_user_can_disable_two_factor_authentication()
    {
        // تفعيل المصادقة الثنائية أولاً
        $this->user->update(['two_factor_enabled' => true]);
        
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/user/two-factor-authentication');

        $response->assertStatus(200);
        
        // التحقق من تعطيل المصادقة الثنائية
        $this->user->refresh();
        $this->assertFalse($this->user->two_factor_enabled);
    }

    /**
     * اختبار تسجيل الدخول مع المصادقة الثنائية
     */
    public function test_login_with_two_factor_authentication()
    {
        // تفعيل المصادقة الثنائية
        $this->user->update(['two_factor_enabled' => true]);
        
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
        
        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'two_factor_required',
            ]);
        
        $this->assertTrue($response->json('two_factor_required'));
    }

    /**
     * اختبار تحقق KYC
     */
    public function test_user_can_submit_kyc_verification()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        $kycData = [
            'id_type' => 'national_id',
            'id_number' => '123456789',
            'full_name' => 'Test User',
            'date_of_birth' => '1990-01-01',
            'address' => 'Test Address, Khartoum, Sudan',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/kyc/verify', $kycData);

        $response->assertStatus(200);
        
        // التحقق من إنشاء طلب التحقق
        $this->assertDatabaseHas('kyc_verifications', [
            'user_id' => $this->user->id,
            'id_type' => 'national_id',
            'id_number' => '123456789',
            'status' => 'pending',
        ]);
    }

    /**
     * اختبار حماية المسارات
     */
    public function test_protected_routes_require_authentication()
    {
        // محاولة الوصول إلى مسار محمي بدون توثيق
        $response = $this->getJson('/api/profile');

        $response->assertStatus(401);
    }

    /**
     * اختبار حماية مسارات المسؤول
     */
    public function test_admin_routes_require_admin_role()
    {
        $token = $this->user->createToken('auth_token')->plainTextToken;
        
        // محاولة الوصول إلى مسار مسؤول بمستخدم عادي
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    /**
     * اختبار تسجيل الدخول كمسؤول
     */
    public function test_admin_can_login()
    {
        // إنشاء مستخدم مسؤول
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        $loginData = [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ];
        
        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'user_type' => 'admin',
                ],
            ]);
    }

    /**
     * اختبار وصول المسؤول إلى مسارات المسؤول
     */
    public function test_admin_can_access_admin_routes()
    {
        // إنشاء مستخدم مسؤول
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        $token = $admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }
}
