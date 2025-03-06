<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RouteTest extends TestCase
{
    /**
     * اختبار المسارات العامة
     *
     * @return void
     */
    public function test_public_routes()
    {
        // الصفحة الرئيسية
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // صفحة تسجيل الدخول
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // صفحة التسجيل
        $response = $this->get('/register');
        $response->assertStatus(200);
        
        // صفحة نسيت كلمة المرور
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }
    
    /**
     * اختبار مسارات المستخدم المسجل
     *
     * @return void
     */
    public function test_authenticated_routes()
    {
        // إنشاء مستخدم للاختبار
        $user = User::factory()->create();
        
        // لوحة التحكم
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        
        // الملف الشخصي
        $response = $this->actingAs($user)->get('/user/profile');
        $response->assertStatus(200);
        
        // الإعدادات
        $response = $this->actingAs($user)->get('/user/settings');
        $response->assertStatus(200);
        
        // الأمان
        $response = $this->actingAs($user)->get('/user/security');
        $response->assertStatus(200);
        
        // المراسلة
        $response = $this->actingAs($user)->get('/messaging');
        $response->assertStatus(200);
    }
    
    /**
     * اختبار مسارات العميل
     *
     * @return void
     */
    public function test_customer_routes()
    {
        // إنشاء مستخدم عميل للاختبار
        $user = User::factory()->create(['role' => 'customer']);
        
        // لوحة تحكم العميل
        $response = $this->actingAs($user)->get('/customer/dashboard');
        $response->assertStatus(200);
        
        // طلبات العميل
        $response = $this->actingAs($user)->get('/customer/dashboard/orders');
        $response->assertStatus(200);
        
        // محفظة العميل
        $response = $this->actingAs($user)->get('/customer/dashboard/wallet');
        $response->assertStatus(200);
    }
    
    /**
     * اختبار مسارات التاجر
     *
     * @return void
     */
    public function test_merchant_routes()
    {
        // إنشاء مستخدم تاجر للاختبار
        $user = User::factory()->create(['role' => 'merchant']);
        
        // لوحة تحكم التاجر
        $response = $this->actingAs($user)->get('/merchant/dashboard');
        $response->assertStatus(200);
        
        // المنتجات
        $response = $this->actingAs($user)->get('/merchant/products');
        $response->assertStatus(200);
        
        // الطلبات
        $response = $this->actingAs($user)->get('/merchant/orders');
        $response->assertStatus(200);
    }
    
    /**
     * اختبار مسارات المندوب
     *
     * @return void
     */
    public function test_messenger_routes()
    {
        // إنشاء مستخدم مندوب للاختبار
        $user = User::factory()->create(['role' => 'messenger']);
        
        // لوحة تحكم المندوب
        $response = $this->actingAs($user)->get('/messenger/dashboard');
        $response->assertStatus(200);
        
        // الملف الشخصي للمندوب
        $response = $this->actingAs($user)->get('/messenger/profile');
        $response->assertStatus(200);
        
        // التوصيلات
        $response = $this->actingAs($user)->get('/messenger/deliveries');
        $response->assertStatus(200);
    }
    
    /**
     * اختبار مسارات المشرف
     *
     * @return void
     */
    public function test_admin_routes()
    {
        // إنشاء مستخدم مشرف للاختبار
        $user = User::factory()->create(['role' => 'admin']);
        
        // لوحة تحكم المشرف
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
        
        // إدارة المستخدمين
        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(200);
        
        // إدارة التجار
        $response = $this->actingAs($user)->get('/admin/merchants');
        $response->assertStatus(200);
    }
    
    /**
     * اختبار مسارات المراسلة
     *
     * @return void
     */
    public function test_messaging_routes()
    {
        // إنشاء مستخدم للاختبار
        $user = User::factory()->create();
        
        // صفحة المراسلة الرئيسية
        $response = $this->actingAs($user)->get('/messaging');
        $response->assertStatus(200);
        
        // جهات الاتصال
        $response = $this->actingAs($user)->get('/messaging/contacts');
        $response->assertStatus(200);
        
        // المكالمات الصوتية
        $response = $this->actingAs($user)->get('/messaging/voice-calls');
        $response->assertStatus(200);
    }
}
