<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدم مسؤول
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء مستخدم عادي
        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * اختبار تسجيل مستخدم جديد
     */
    public function test_user_can_register()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone_number' => '123456789',
            'country_id' => 1,
            'city_id' => 1,
        ];
        
        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'user_type' => 'user', // نوع المستخدم الافتراضي
        ]);
    }

    /**
     * اختبار تسجيل الدخول
     */
    public function test_user_can_login()
    {
        $loginData = [
            'email' => $this->user->email,
            'password' => 'password', // كلمة المرور الافتراضية في factory
        ];
        
        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'token',
                ],
            ]);
    }

    /**
     * اختبار فشل تسجيل الدخول بكلمة مرور خاطئة
     */
    public function test_user_cannot_login_with_wrong_password()
    {
        $loginData = [
            'email' => $this->user->email,
            'password' => 'wrong_password',
        ];
        
        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(401);
    }

    /**
     * اختبار تحديث معلومات المستخدم
     */
    public function test_user_can_update_profile()
    {
        $updatedData = [
            'name' => 'Updated Name',
            'phone_number' => '987654321',
            'city_id' => 2,
        ];
        
        $response = $this->actingAs($this->user)
            ->putJson('/api/profile', $updatedData);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Updated Name',
            'phone_number' => '987654321',
            'city_id' => 2,
        ]);
    }

    /**
     * اختبار تغيير كلمة المرور
     */
    public function test_user_can_change_password()
    {
        $passwordData = [
            'current_password' => 'password', // كلمة المرور الافتراضية في factory
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];
        
        $response = $this->actingAs($this->user)
            ->putJson('/api/password', $passwordData);

        $response->assertStatus(200);
        
        // إعادة تحميل المستخدم من قاعدة البيانات
        $updatedUser = User::find($this->user->id);
        
        // التحقق من أن كلمة المرور الجديدة تعمل
        $this->assertTrue(Hash::check('NewPassword123!', $updatedUser->password));
    }

    /**
     * اختبار تفعيل المصادقة الثنائية
     */
    public function test_user_can_enable_two_factor_auth()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/two-factor-authentication');

        $response->assertStatus(200);
        
        // إعادة تحميل المستخدم من قاعدة البيانات
        $updatedUser = User::find($this->user->id);
        
        // التحقق من تفعيل المصادقة الثنائية
        $this->assertNotNull($updatedUser->two_factor_secret);
        $this->assertNotNull($updatedUser->two_factor_recovery_codes);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة المستخدمين
     */
    public function test_admin_can_view_users_list()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'user_type',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه تغيير نوع المستخدم
     */
    public function test_admin_can_change_user_type()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$this->user->id}/type", [
                'user_type' => 'merchant',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'user_type' => 'merchant',
        ]);
    }

    /**
     * اختبار أن المستخدم العادي لا يمكنه الوصول إلى لوحة تحكم المسؤول
     */
    public function test_regular_user_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/users');

        $response->assertStatus(403);
    }

    /**
     * اختبار أن المسؤول يمكنه تعطيل حساب مستخدم
     */
    public function test_admin_can_disable_user_account()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$this->user->id}/status", [
                'is_active' => false,
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'is_active' => false,
        ]);
    }
}
