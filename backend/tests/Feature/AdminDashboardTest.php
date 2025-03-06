<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user;
    protected $merchant;

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
        
        // إنشاء مستخدم تاجر
        $this->merchant = User::factory()->create([
            'name' => 'Merchant User',
            'email' => 'merchant@example.com',
            'user_type' => 'merchant',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * اختبار الوصول إلى لوحة تحكم المسؤول
     */
    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'users_count',
                    'products_count',
                    'orders_count',
                    'revenue',
                ],
            ]);
    }

    /**
     * اختبار أن المستخدم العادي لا يمكنه الوصول إلى لوحة تحكم المسؤول
     */
    public function test_regular_user_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
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
     * اختبار أن المسؤول يمكنه عرض تفاصيل مستخدم محدد
     */
    public function test_admin_can_view_user_details()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                    'phone_number',
                    'city',
                    'country',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه تغيير نوع المستخدم
     */
    public function test_admin_can_change_user_type()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$this->user->id}", [
                'user_type' => 'merchant',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'user_type' => 'merchant',
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه تعطيل حساب مستخدم
     */
    public function test_admin_can_disable_user_account()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$this->user->id}/disable");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'is_active' => false,
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه تفعيل حساب مستخدم
     */
    public function test_admin_can_enable_user_account()
    {
        // تعطيل الحساب أولاً
        $this->user->update(['is_active' => false]);
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$this->user->id}/enable");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'is_active' => true,
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة الفئات
     */
    public function test_admin_can_view_categories()
    {
        // إنشاء بعض الفئات
        Category::factory()->count(5)->create();
        
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at',
                    ],
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه إنشاء فئة جديدة
     */
    public function test_admin_can_create_category()
    {
        $categoryData = [
            'name' => 'New Category',
            'description' => 'This is a new category',
        ];
        
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'description' => 'This is a new category',
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه تحديث فئة
     */
    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create();
        
        $updatedData = [
            'name' => 'Updated Category',
            'description' => 'This is an updated category',
        ];
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/categories/{$category->id}", $updatedData);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'description' => 'This is an updated category',
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه حذف فئة
     */
    public function test_admin_can_delete_category()
    {
        $category = Category::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/categories/{$category->id}");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة المنتجات
     */
    public function test_admin_can_view_products()
    {
        // إنشاء بعض المنتجات
        Product::factory()->count(5)->create();
        
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'category_id',
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
     * اختبار أن المسؤول يمكنه عرض تفاصيل منتج محدد
     */
    public function test_admin_can_view_product_details()
    {
        $product = Product::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'category',
                    'images',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة الطلبات
     */
    public function test_admin_can_view_orders()
    {
        // إنشاء بعض الطلبات
        Order::factory()->count(5)->create();
        
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'total',
                        'status',
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
     * اختبار أن المسؤول يمكنه عرض تفاصيل طلب محدد
     */
    public function test_admin_can_view_order_details()
    {
        $order = Order::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user',
                    'items',
                    'total',
                    'status',
                    'payment',
                    'delivery',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه تحديث حالة طلب
     */
    public function test_admin_can_update_order_status()
    {
        $order = Order::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/orders/{$order->id}", [
                'status' => 'shipped',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض إحصائيات المبيعات
     */
    public function test_admin_can_view_sales_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/sales');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'daily',
                    'weekly',
                    'monthly',
                    'yearly',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض إحصائيات المستخدمين
     */
    public function test_admin_can_view_user_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_users',
                    'new_users',
                    'active_users',
                    'user_types',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض سجل النشاط
     */
    public function test_admin_can_view_activity_logs()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/logs/activity');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'action',
                        'description',
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
     * اختبار أن المسؤول يمكنه عرض سجل الأخطاء
     */
    public function test_admin_can_view_error_logs()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/logs/errors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'error_type',
                        'message',
                        'file',
                        'line',
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
}
