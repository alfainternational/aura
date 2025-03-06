<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\VoiceCall;
use App\Events\MessageSentEvent;
use App\Events\VoiceCallEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user1;
    protected $user2;
    protected $admin;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // تعطيل الأحداث خلال الاختبارات
        Event::fake([
            MessageSentEvent::class,
            VoiceCallEvent::class,
        ]);
        
        // إنشاء مستخدمين عاديين
        $this->user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->user2 = User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
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
     * اختبار تكامل المراسلة والإشعارات
     */
    public function test_messaging_and_notifications_integration()
    {
        // إنشاء محادثة
        $response = $this->actingAs($this->user1)
            ->postJson('/api/conversations', [
                'type' => 'individual',
                'participants' => [$this->user2->id],
            ]);
            
        $response->assertStatus(201);
        $conversationId = $response->json('data.id');
        
        // إرسال رسالة
        $response = $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$conversationId}/messages", [
                'type' => 'text',
                'content' => 'Hello, this is a test message!',
            ]);
            
        $response->assertStatus(201);
        $messageId = $response->json('data.id');
        
        // التحقق من إرسال إشعار
        Event::assertDispatched(MessageSentEvent::class, function ($event) use ($messageId) {
            return $event->message->id === $messageId;
        });
        
        // التحقق من وجود الإشعار في قاعدة البيانات
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user2->id,
            'data->message_id' => $messageId,
        ]);
        
        // تحديث حالة الرسالة
        $response = $this->actingAs($this->user2)
            ->patchJson("/api/messages/{$messageId}/status", [
                'status' => 'read',
            ]);
            
        $response->assertStatus(200);
        
        // التحقق من تحديث حالة الرسالة
        $this->assertDatabaseHas('messages', [
            'id' => $messageId,
            'status' => 'read',
        ]);
    }

    /**
     * اختبار تكامل المكالمات الصوتية والإشعارات
     */
    public function test_voice_calls_and_notifications_integration()
    {
        // بدء مكالمة صوتية
        $response = $this->actingAs($this->user1)
            ->postJson('/api/voice-calls', [
                'participants' => [$this->user2->id],
            ]);
            
        $response->assertStatus(201);
        $callId = $response->json('data.id');
        
        // التحقق من إرسال إشعار
        Event::assertDispatched(VoiceCallEvent::class, function ($event) use ($callId) {
            return $event->voiceCall->id === $callId;
        });
        
        // التحقق من وجود الإشعار في قاعدة البيانات
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->user2->id,
            'data->voice_call_id' => $callId,
        ]);
        
        // قبول المكالمة
        $response = $this->actingAs($this->user2)
            ->postJson("/api/voice-calls/{$callId}/accept");
            
        $response->assertStatus(200);
        
        // التحقق من تحديث حالة المكالمة
        $this->assertDatabaseHas('voice_calls', [
            'id' => $callId,
            'status' => 'active',
        ]);
        
        // التحقق من تحديث حالة المشارك
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $callId,
            'user_id' => $this->user2->id,
            'status' => 'joined',
        ]);
    }

    /**
     * اختبار تكامل إدارة المستخدمين والأدوار
     */
    public function test_user_management_and_roles_integration()
    {
        // إنشاء مستخدم جديد
        $userData = [
            'name' => 'New Merchant',
            'email' => 'merchant@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone_number' => '123456789',
            'country_id' => 1,
            'city_id' => 1,
        ];
        
        $response = $this->postJson('/api/register', $userData);
        $response->assertStatus(201);
        $userId = $response->json('data.id');
        
        // تغيير نوع المستخدم إلى تاجر
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/users/{$userId}/type", [
                'user_type' => 'merchant',
            ]);
            
        $response->assertStatus(200);
        
        // التحقق من تحديث نوع المستخدم
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'user_type' => 'merchant',
        ]);
        
        // تسجيل دخول المستخدم
        $response = $this->postJson('/api/login', [
            'email' => 'merchant@example.com',
            'password' => 'Password123!',
        ]);
        
        $response->assertStatus(200);
        $token = $response->json('data.token');
        
        // محاولة الوصول إلى لوحة تحكم التاجر
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->getJson('/api/merchant/dashboard');
            
        $response->assertStatus(200);
        
        // محاولة الوصول إلى لوحة تحكم المسؤول
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->getJson('/api/admin/dashboard');
            
        $response->assertStatus(403);
    }

    /**
     * اختبار تكامل المصادقة والمراسلة
     */
    public function test_authentication_and_messaging_integration()
    {
        // محاولة إرسال رسالة بدون تسجيل دخول
        $response = $this->postJson('/api/conversations', [
            'type' => 'individual',
            'participants' => [$this->user2->id],
        ]);
        
        $response->assertStatus(401);
        
        // تسجيل دخول
        $response = $this->postJson('/api/login', [
            'email' => $this->user1->email,
            'password' => 'password', // كلمة المرور الافتراضية في factory
        ]);
        
        $response->assertStatus(200);
        $token = $response->json('data.token');
        
        // إنشاء محادثة بعد تسجيل الدخول
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->postJson('/api/conversations', [
                'type' => 'individual',
                'participants' => [$this->user2->id],
            ]);
            
        $response->assertStatus(201);
        $conversationId = $response->json('data.id');
        
        // إرسال رسالة
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->postJson("/api/conversations/{$conversationId}/messages", [
                'type' => 'text',
                'content' => 'Hello after authentication!',
            ]);
            
        $response->assertStatus(201);
    }

    /**
     * اختبار تكامل المصادقة والمكالمات الصوتية
     */
    public function test_authentication_and_voice_calls_integration()
    {
        // محاولة بدء مكالمة صوتية بدون تسجيل دخول
        $response = $this->postJson('/api/voice-calls', [
            'participants' => [$this->user2->id],
        ]);
        
        $response->assertStatus(401);
        
        // تسجيل دخول
        $response = $this->postJson('/api/login', [
            'email' => $this->user1->email,
            'password' => 'password', // كلمة المرور الافتراضية في factory
        ]);
        
        $response->assertStatus(200);
        $token = $response->json('data.token');
        
        // بدء مكالمة صوتية بعد تسجيل الدخول
        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->postJson('/api/voice-calls', [
                'participants' => [$this->user2->id],
            ]);
            
        $response->assertStatus(201);
    }

    /**
     * اختبار تكامل الإشعارات والمستخدمين
     */
    public function test_notifications_and_users_integration()
    {
        // إنشاء محادثة
        $conversation = Conversation::create([
            'type' => 'individual',
            'created_by' => $this->user1->id,
        ]);
        
        // إضافة المشاركين
        $conversation->participants()->createMany([
            [
                'user_id' => $this->user1->id,
                'status' => 'active',
            ],
            [
                'user_id' => $this->user2->id,
                'status' => 'active',
            ],
        ]);
        
        // إرسال رسالة
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $this->user1->id,
            'type' => 'text',
            'content' => 'Test notification message',
            'status' => 'sent',
        ]);
        
        // إرسال إشعار
        event(new MessageSentEvent($message));
        
        // التحقق من تفضيلات الإشعارات
        $response = $this->actingAs($this->user2)
            ->putJson('/api/notification-preferences', [
                'message_notifications' => false,
            ]);
            
        $response->assertStatus(200);
        
        // التحقق من تحديث تفضيلات الإشعارات
        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $this->user2->id,
            'message_notifications' => false,
        ]);
        
        // إرسال رسالة أخرى
        $message2 = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $this->user1->id,
            'type' => 'text',
            'content' => 'Another test message',
            'status' => 'sent',
        ]);
        
        // إرسال إشعار
        event(new MessageSentEvent($message2));
        
        // التحقق من عدم إرسال إشعار
        // هذا يعتمد على تنفيذ المستمع للحدث وكيفية التعامل مع تفضيلات الإشعارات
    }
}
