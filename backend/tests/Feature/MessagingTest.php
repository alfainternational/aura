<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSentEvent;
use App\Events\MessageStatusUpdatedEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $sender;
    protected $receiver;
    protected $conversation;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // تعطيل الأحداث خلال الاختبارات
        Event::fake([
            MessageSentEvent::class,
            MessageStatusUpdatedEvent::class,
        ]);
        
        // تعطيل المهاجرة للجدول personal_access_tokens
        $this->artisan('config:clear');
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('config:clear');
        });
        
        // إنشاء مستخدمين للاختبار
        $this->sender = User::factory()->create([
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->receiver = User::factory()->create([
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء محادثة بين المستخدمين
        $this->conversation = Conversation::create([
            'type' => 'individual',
            'created_by' => $this->sender->id,
        ]);
        
        // إضافة المشاركين في المحادثة
        $this->conversation->participants()->createMany([
            [
                'user_id' => $this->sender->id,
                'status' => 'active',
            ],
            [
                'user_id' => $this->receiver->id,
                'status' => 'active',
            ],
        ]);
    }

    /**
     * اختبار إنشاء محادثة جديدة
     */
    public function test_user_can_create_new_conversation()
    {
        $response = $this->actingAs($this->sender)
            ->postJson('/api/conversations', [
                'type' => 'individual',
                'participants' => [$this->receiver->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('conversations', [
            'type' => 'individual',
            'created_by' => $this->sender->id,
        ]);
    }

    /**
     * اختبار إرسال رسالة نصية
     */
    public function test_user_can_send_text_message()
    {
        $messageText = $this->faker->sentence;
        
        $response = $this->actingAs($this->sender)
            ->postJson("/api/conversations/{$this->conversation->id}/messages", [
                'type' => 'text',
                'message' => $messageText,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'message',
                    'sender',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => $messageText,
        ]);
        
        Event::assertDispatched(MessageSentEvent::class);
    }

    /**
     * اختبار إرسال رسالة صورة
     */
    public function test_user_can_send_image_message()
    {
        // تخطي هذا الاختبار إذا كان امتداد GD غير موجود
        if (!function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension is not installed.');
        }

        $response = $this->actingAs($this->sender)
            ->postJson("/api/conversations/{$this->conversation->id}/messages", [
                'type' => 'image',
                'file' => $this->createTestImage(),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'message',
                    'attachment_path',
                    'sender',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'image',
        ]);
        
        Event::assertDispatched(MessageSentEvent::class);
    }

    /**
     * اختبار تحديث حالة الرسالة
     */
    public function test_message_status_can_be_updated()
    {
        // إنشاء رسالة جديدة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => $this->faker->sentence,
        ]);
        
        // إنشاء حالة رسالة للمستلم
        $message->statuses()->create([
            'user_id' => $this->receiver->id,
            'status' => 'sent',
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->patchJson("/api/messages/{$message->id}/status", [
                'status' => 'delivered',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('message_statuses', [
            'message_id' => $message->id,
            'user_id' => $this->receiver->id,
            'status' => 'delivered',
        ]);
        
        Event::assertDispatched(MessageStatusUpdatedEvent::class);
        
        // تحديث الحالة إلى مقروءة
        $response = $this->actingAs($this->receiver)
            ->patchJson("/api/messages/{$message->id}/status", [
                'status' => 'read',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('message_statuses', [
            'message_id' => $message->id,
            'user_id' => $this->receiver->id,
            'status' => 'read',
        ]);
        
        Event::assertDispatched(MessageStatusUpdatedEvent::class);
    }

    /**
     * اختبار حذف رسالة
     */
    public function test_user_can_delete_own_message()
    {
        // إنشاء رسالة جديدة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => $this->faker->sentence,
        ]);
        
        $response = $this->actingAs($this->sender)
            ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_deleted' => true,
        ]);
    }

    /**
     * اختبار أن المستخدم لا يمكنه حذف رسالة مستخدم آخر
     */
    public function test_user_cannot_delete_others_message()
    {
        // إنشاء رسالة جديدة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => $this->faker->sentence,
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(403);
            
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_deleted' => false,
        ]);
    }

    /**
     * إنشاء صورة اختبار وهمية
     */
    protected function createTestImage()
    {
        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg', 100, 100);
        return $file;
    }
}
