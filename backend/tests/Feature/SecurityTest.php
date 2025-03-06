<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\KycVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدم عادي
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
            'password' => Hash::make('Password123!'),
        ]);
        
        // إنشاء مستخدم مسؤول
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * اختبار التحقق من البريد الإلكتروني
     */
    public function test_email_verification()
    {
        // إنشاء مستخدم بدون تحقق من البريد
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);
        
        // محاولة الوصول إلى مسار محمي
        $response = $this->actingAs($unverifiedUser)
            ->getJson('/api/profile');
            
        // يجب أن يتم توجيه المستخدم للتحقق من البريد
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Your email address is not verified.',
            ]);
            
        // تحقق من البريد
        $unverifiedUser->email_verified_at = now();
        $unverifiedUser->save();
        
        // محاولة الوصول مرة أخرى
        $response = $this->actingAs($unverifiedUser)
            ->getJson('/api/profile');
            
        // يجب أن يتم السماح بالوصول
        $response->assertStatus(200);
    }

    /**
     * اختبار المصادقة الثنائية
     */
    public function test_two_factor_authentication()
    {
        // تفعيل المصادقة الثنائية للمستخدم
        $this->actingAs($this->user)
            ->postJson('/api/two-factor-authentication');
            
        // إعادة تحميل المستخدم
        $this->user->refresh();
        
        // التحقق من تفعيل المصادقة الثنائية
        $this->assertNotNull($this->user->two_factor_secret);
        $this->assertNotNull($this->user->two_factor_recovery_codes);
        
        // تسجيل الخروج
        Auth::logout();
        
        // محاولة تسجيل الدخول
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'Password123!',
        ]);
        
        // يجب أن يتم طلب رمز المصادقة الثنائية
        $response->assertStatus(200)
            ->assertJson([
                'two_factor_required' => true,
            ]);
            
        // محاولة الوصول إلى مسار محمي بدون رمز المصادقة
        $response = $this->getJson('/api/profile', [
            'Authorization' => 'Bearer ' . $response->json('token'),
        ]);
        
        // يجب رفض الوصول
        $response->assertStatus(403);
    }

    /**
     * اختبار التحقق من هوية المستخدم (KYC)
     */
    public function test_kyc_verification()
    {
        // إنشاء طلب تحقق من الهوية
        $response = $this->actingAs($this->user)
            ->postJson('/api/kyc/verify', [
                'id_type' => 'national_id',
                'id_number' => '1234567890',
                'full_name' => $this->user->name,
                'date_of_birth' => '1990-01-01',
                'address' => $this->faker->address,
                'id_front_image' => $this->createTestImage('id_front.jpg'),
                'id_back_image' => $this->createTestImage('id_back.jpg'),
                'selfie_image' => $this->createTestImage('selfie.jpg'),
            ]);
            
        $response->assertStatus(201);
        
        // التحقق من إنشاء طلب التحقق
        $this->assertDatabaseHas('kyc_verifications', [
            'user_id' => $this->user->id,
            'id_type' => 'national_id',
            'id_number' => '1234567890',
            'status' => 'pending',
        ]);
        
        // محاولة الوصول إلى مسار يتطلب التحقق من الهوية
        $response = $this->actingAs($this->user)
            ->getJson('/api/protected-kyc-route');
            
        // يجب رفض الوصول
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'KYC verification required.',
            ]);
            
        // الموافقة على طلب التحقق من قبل المسؤول
        $kycVerification = KycVerification::where('user_id', $this->user->id)->first();
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/kyc/{$kycVerification->id}", [
                'status' => 'approved',
                'notes' => 'Verification approved',
            ]);
            
        $response->assertStatus(200);
        
        // التحقق من تحديث حالة التحقق
        $this->assertDatabaseHas('kyc_verifications', [
            'id' => $kycVerification->id,
            'status' => 'approved',
        ]);
        
        // تحديث حالة التحقق للمستخدم
        $this->user->kyc_verified_at = now();
        $this->user->save();
        
        // محاولة الوصول مرة أخرى
        $response = $this->actingAs($this->user)
            ->getJson('/api/protected-kyc-route');
            
        // يجب السماح بالوصول
        $response->assertStatus(200);
    }

    /**
     * اختبار حماية CSRF
     */
    public function test_csrf_protection()
    {
        // محاولة إرسال طلب POST بدون رمز CSRF
        $response = $this->withHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post('/login', [
                'email' => $this->user->email,
                'password' => 'Password123!',
            ]);
            
        // يجب رفض الطلب
        $response->assertStatus(419);
    }

    /**
     * اختبار حماية الجلسة
     */
    public function test_session_security()
    {
        // تسجيل الدخول
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'Password123!',
            '_token' => csrf_token(),
        ]);
        
        // التحقق من إنشاء جلسة آمنة
        $this->assertAuthenticated();
        
        // التحقق من أن الجلسة تحتوي على معرف المستخدم
        $this->assertEquals($this->user->id, session('auth.id'));
        
        // تسجيل الخروج
        $response = $this->post('/logout', [
            '_token' => csrf_token(),
        ]);
        
        // التحقق من إنهاء الجلسة
        $this->assertGuest();
    }

    /**
     * اختبار حماية كلمة المرور
     */
    public function test_password_security()
    {
        // محاولة تسجيل مستخدم بكلمة مرور ضعيفة
        $response = $this->postJson('/api/register', [
            'name' => 'Weak Password User',
            'email' => 'weak@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);
        
        // يجب رفض الطلب
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
            
        // محاولة تسجيل مستخدم بكلمة مرور قوية
        $response = $this->postJson('/api/register', [
            'name' => 'Strong Password User',
            'email' => 'strong@example.com',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ]);
        
        // يجب قبول الطلب
        $response->assertStatus(201);
    }

    /**
     * اختبار حماية الوصول إلى المسارات
     */
    public function test_route_protection()
    {
        // محاولة الوصول إلى لوحة تحكم المسؤول كمستخدم عادي
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/dashboard');
            
        // يجب رفض الوصول
        $response->assertStatus(403);
        
        // محاولة الوصول إلى لوحة تحكم المسؤول كمسؤول
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');
            
        // يجب السماح بالوصول
        $response->assertStatus(200);
    }

    /**
     * إنشاء صورة اختبار وهمية
     */
    protected function createTestImage($filename)
    {
        $file = \Illuminate\Http\UploadedFile::fake()->image($filename, 100, 100);
        return $file;
    }
}
