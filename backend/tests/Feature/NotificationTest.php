<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\Notification;
use App\Events\MessageSentEvent;
use App\Events\MessageStatusUpdatedEvent;
use App\Events\VoiceCallEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationTest extends TestCase
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
        
        // إنشاء مستخدمين
        $this->sender = User::factory()->create([
            'name' => 'Sender User',
            'email' => 'sender@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->receiver = User::factory()->create([
            'name' => 'Receiver User',
            'email' => 'receiver@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء محادثة
        $this->conversation = Conversation::create([
            'type' => 'individual',
            'created_by' => $this->sender->id,
        ]);
        
        // إضافة المشاركين
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->sender->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->receiver->id,
            'is_admin' => false,
            'joined_at' => now(),
        ]);
        
        // إنشاء إعدادات الإشعارات للمستقبل
        \App\Models\NotificationSetting::create([
            'user_id' => $this->receiver->id,
            'new_message' => true,
            'voice_call' => true,
            'message_read' => true,
            'new_user_joined' => true,
            'email_notifications' => true,
            'push_notifications' => true,
        ]);
    }

    /**
     * اختبار إنشاء إشعار عند إرسال رسالة جديدة
     */
    public function test_notification_created_when_message_sent()
    {
        Event::fake([
            MessageSentEvent::class,
        ]);
        
        // إرسال رسالة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => 'Hello, this is a test message',
        ]);
        
        // إطلاق حدث إرسال الرسالة
        event(new MessageSentEvent($message));
        
        Event::assertDispatched(MessageSentEvent::class);
        
        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->receiver->id,
            'type' => 'new_message',
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => $message->id,
            'read' => false,
        ]);
    }

    /**
     * اختبار إنشاء إشعار عند تحديث حالة الرسالة
     */
    public function test_notification_created_when_message_status_updated()
    {
        Event::fake([
            MessageStatusUpdatedEvent::class,
        ]);
        
        // إرسال رسالة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => 'Hello, this is a test message',
        ]);
        
        // تحديث حالة الرسالة
        $message->statuses()->create([
            'user_id' => $this->receiver->id,
            'status' => 'read',
        ]);
        
        // إطلاق حدث تحديث حالة الرسالة
        event(new MessageStatusUpdatedEvent($message, $this->receiver, 'read'));
        
        Event::assertDispatched(MessageStatusUpdatedEvent::class);
        
        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->sender->id,
            'type' => 'message_read',
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => $message->id,
            'read' => false,
        ]);
    }

    /**
     * اختبار إنشاء إشعار عند بدء مكالمة صوتية
     */
    public function test_notification_created_when_voice_call_initiated()
    {
        Event::fake([
            VoiceCallEvent::class,
        ]);
        
        // إنشاء مكالمة صوتية
        $voiceCall = \App\Models\VoiceCall::create([
            'conversation_id' => $this->conversation->id,
            'initiated_by' => $this->sender->id,
            'status' => 'ringing',
            'started_at' => now(),
        ]);
        
        // إضافة المشاركين
        $voiceCall->participants()->createMany([
            [
                'user_id' => $this->sender->id,
                'status' => 'joined',
            ],
            [
                'user_id' => $this->receiver->id,
                'status' => 'invited',
            ],
        ]);
        
        // إطلاق حدث المكالمة الصوتية
        event(new VoiceCallEvent($voiceCall, 'initiated'));
        
        Event::assertDispatched(VoiceCallEvent::class);
        
        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->receiver->id,
            'type' => 'voice_call',
            'notifiable_type' => 'App\Models\VoiceCall',
            'notifiable_id' => $voiceCall->id,
            'read' => false,
        ]);
    }

    /**
     * اختبار عرض قائمة الإشعارات
     */
    public function test_user_can_view_notifications()
    {
        // إنشاء بعض الإشعارات
        Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'new_message',
            'title' => 'رسالة جديدة',
            'body' => 'لديك رسالة جديدة من ' . $this->sender->name,
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'voice_call',
            'title' => 'مكالمة صوتية',
            'body' => $this->sender->name . ' يتصل بك',
            'notifiable_type' => 'App\Models\VoiceCall',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'title',
                        'body',
                        'read',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'unread_count',
                ],
            ]);
            
        $this->assertEquals(2, $response->json('meta.unread_count'));
    }

    /**
     * اختبار تحديث حالة الإشعار إلى مقروء
     */
    public function test_user_can_mark_notification_as_read()
    {
        // إنشاء إشعار
        $notification = Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'new_message',
            'title' => 'رسالة جديدة',
            'body' => 'لديك رسالة جديدة من ' . $this->sender->name,
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->putJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read' => true,
        ]);
    }

    /**
     * اختبار تحديث جميع الإشعارات إلى مقروءة
     */
    public function test_user_can_mark_all_notifications_as_read()
    {
        // إنشاء بعض الإشعارات
        Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'new_message',
            'title' => 'رسالة جديدة',
            'body' => 'لديك رسالة جديدة من ' . $this->sender->name,
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'voice_call',
            'title' => 'مكالمة صوتية',
            'body' => $this->sender->name . ' يتصل بك',
            'notifiable_type' => 'App\Models\VoiceCall',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->putJson("/api/notifications/read-all");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $this->receiver->id,
            'read' => false,
        ]);
    }

    /**
     * اختبار حذف إشعار
     */
    public function test_user_can_delete_notification()
    {
        // إنشاء إشعار
        $notification = Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'new_message',
            'title' => 'رسالة جديدة',
            'body' => 'لديك رسالة جديدة من ' . $this->sender->name,
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    /**
     * اختبار حذف جميع الإشعارات
     */
    public function test_user_can_delete_all_notifications()
    {
        // إنشاء بعض الإشعارات
        Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'new_message',
            'title' => 'رسالة جديدة',
            'body' => 'لديك رسالة جديدة من ' . $this->sender->name,
            'notifiable_type' => 'App\Models\Message',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        Notification::create([
            'user_id' => $this->receiver->id,
            'type' => 'voice_call',
            'title' => 'مكالمة صوتية',
            'body' => $this->sender->name . ' يتصل بك',
            'notifiable_type' => 'App\Models\VoiceCall',
            'notifiable_id' => 1,
            'read' => false,
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->deleteJson("/api/notifications");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $this->receiver->id,
        ]);
    }

    /**
     * اختبار تعطيل الإشعارات لمحادثة محددة
     */
    public function test_user_can_mute_conversation_notifications()
    {
        $response = $this->actingAs($this->receiver)
            ->putJson("/api/conversations/{$this->conversation->id}/mute", [
                'muted' => true,
                'mute_until' => now()->addDays(7)->toDateTimeString(),
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('conversation_settings', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->receiver->id,
            'muted' => true,
        ]);
        
        // التحقق من عدم إنشاء إشعار عند إرسال رسالة لمحادثة مكتومة
        Event::fake([
            MessageSentEvent::class,
        ]);
        
        // إرسال رسالة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => 'This message should not create a notification',
        ]);
        
        // إطلاق حدث إرسال الرسالة
        event(new MessageSentEvent($message));
        
        Event::assertDispatched(MessageSentEvent::class);
        
        // لا يجب إنشاء إشعار للمستخدم الذي كتم المحادثة
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $this->receiver->id,
            'notifiable_id' => $message->id,
        ]);
    }
}
