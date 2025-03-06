<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\VoiceCall;
use App\Notifications\MessageReceivedNotification;
use App\Notifications\VoiceCallNotification;
use App\Notifications\UserMentionNotification;
use App\Notifications\FriendRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationsTest extends TestCase
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
    }

    /**
     * اختبار إنشاء إشعار رسالة جديدة
     */
    public function test_create_message_notification()
    {
        NotificationFacade::fake();
        
        // إنشاء رسالة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => 'Hello, this is a test message',
        ]);
        
        // إرسال إشعار
        $this->receiver->notify(new MessageReceivedNotification($message));
        
        NotificationFacade::assertSentTo(
            $this->receiver,
            MessageReceivedNotification::class,
            function ($notification, $channels) use ($message) {
                return $notification->message->id === $message->id;
            }
        );
    }

    /**
     * اختبار إنشاء إشعار مكالمة صوتية
     */
    public function test_create_voice_call_notification()
    {
        NotificationFacade::fake();
        
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
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
        
        // إرسال إشعار
        $this->receiver->notify(new VoiceCallNotification($voiceCall));
        
        NotificationFacade::assertSentTo(
            $this->receiver,
            VoiceCallNotification::class,
            function ($notification, $channels) use ($voiceCall) {
                return $notification->voiceCall->id === $voiceCall->id;
            }
        );
    }

    /**
     * اختبار إنشاء إشعار ذكر مستخدم
     */
    public function test_create_user_mention_notification()
    {
        NotificationFacade::fake();
        
        // إنشاء رسالة تحتوي على ذكر مستخدم
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => 'Hello @' . $this->receiver->name . ', this is a test message',
        ]);
        
        // إرسال إشعار
        $this->receiver->notify(new UserMentionNotification($message, $this->sender));
        
        NotificationFacade::assertSentTo(
            $this->receiver,
            UserMentionNotification::class,
            function ($notification, $channels) use ($message) {
                return $notification->message->id === $message->id;
            }
        );
    }

    /**
     * اختبار إنشاء إشعار طلب صداقة
     */
    public function test_create_friend_request_notification()
    {
        NotificationFacade::fake();
        
        // إرسال إشعار
        $this->receiver->notify(new FriendRequestNotification($this->sender));
        
        NotificationFacade::assertSentTo(
            $this->receiver,
            FriendRequestNotification::class,
            function ($notification, $channels) {
                return $notification->sender->id === $this->sender->id;
            }
        );
    }

    /**
     * اختبار جلب قائمة الإشعارات
     */
    public function test_get_notifications_list()
    {
        // إنشاء إشعارات للمستخدم
        $this->createDatabaseNotifications($this->receiver, 5);
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /**
     * اختبار تحديد إشعار كمقروء
     */
    public function test_mark_notification_as_read()
    {
        // إنشاء إشعار
        $notification = $this->createDatabaseNotifications($this->receiver, 1)[0];
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/' . $notification->id . '/read');

        $response->assertStatus(200);
        
        // التحقق من تحديث حالة الإشعار
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => now()->format('Y-m-d H:i'),
        ]);
    }

    /**
     * اختبار تحديد جميع الإشعارات كمقروءة
     */
    public function test_mark_all_notifications_as_read()
    {
        // إنشاء إشعارات
        $this->createDatabaseNotifications($this->receiver, 5);
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/read-all');

        $response->assertStatus(200);
        
        // التحقق من تحديث حالة جميع الإشعارات
        $this->assertEquals(0, $this->receiver->unreadNotifications()->count());
    }

    /**
     * اختبار حذف إشعار
     */
    public function test_delete_notification()
    {
        // إنشاء إشعار
        $notification = $this->createDatabaseNotifications($this->receiver, 1)[0];
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/notifications/' . $notification->id);

        $response->assertStatus(200);
        
        // التحقق من حذف الإشعار
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    /**
     * اختبار حذف جميع الإشعارات
     */
    public function test_delete_all_notifications()
    {
        // إنشاء إشعارات
        $this->createDatabaseNotifications($this->receiver, 5);
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/notifications');

        $response->assertStatus(200);
        
        // التحقق من حذف جميع الإشعارات
        $this->assertEquals(0, $this->receiver->notifications()->count());
    }

    /**
     * اختبار تفضيلات الإشعارات
     */
    public function test_notification_preferences()
    {
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $preferences = [
            'message_received' => true,
            'voice_call' => true,
            'user_mention' => false,
            'friend_request' => false,
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/settings/notifications', $preferences);

        $response->assertStatus(200);
        
        // التحقق من تحديث تفضيلات الإشعارات
        $this->receiver->refresh();
        $this->assertEquals($preferences, $this->receiver->notification_preferences);
    }

    /**
     * اختبار عدد الإشعارات غير المقروءة
     */
    public function test_unread_notifications_count()
    {
        // إنشاء إشعارات
        $this->createDatabaseNotifications($this->receiver, 5);
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJson([
                'count' => 5,
            ]);
    }

    /**
     * اختبار تصفية الإشعارات حسب النوع
     */
    public function test_filter_notifications_by_type()
    {
        // إنشاء إشعارات من أنواع مختلفة
        $this->createDatabaseNotifications($this->receiver, 3, 'message_received');
        $this->createDatabaseNotifications($this->receiver, 2, 'voice_call');
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications?type=message_received');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * اختبار تصفية الإشعارات حسب حالة القراءة
     */
    public function test_filter_notifications_by_read_status()
    {
        // إنشاء إشعارات
        $notifications = $this->createDatabaseNotifications($this->receiver, 5);
        
        // تحديد بعض الإشعارات كمقروءة
        $notifications[0]->markAsRead();
        $notifications[1]->markAsRead();
        
        $token = $this->receiver->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications?read=true');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * إنشاء إشعارات في قاعدة البيانات
     */
    private function createDatabaseNotifications($user, $count, $type = 'message_received')
    {
        $notifications = [];
        
        for ($i = 0; $i < $count; $i++) {
            $data = [
                'type' => $type,
                'sender_id' => $this->sender->id,
                'sender_name' => $this->sender->name,
            ];
            
            if ($type === 'message_received') {
                $message = Message::create([
                    'conversation_id' => $this->conversation->id,
                    'sender_id' => $this->sender->id,
                    'type' => 'text',
                    'message' => 'Test message ' . ($i + 1),
                ]);
                
                $data['message_id'] = $message->id;
                $data['conversation_id'] = $this->conversation->id;
                $data['message_preview'] = substr($message->message, 0, 50);
            } elseif ($type === 'voice_call') {
                $voiceCall = VoiceCall::create([
                    'conversation_id' => $this->conversation->id,
                    'initiated_by' => $this->sender->id,
                    'status' => 'ringing',
                    'started_at' => now(),
                ]);
                
                $data['voice_call_id'] = $voiceCall->id;
                $data['conversation_id'] = $this->conversation->id;
            }
            
            $notification = $user->notifications()->create([
                'id' => Str::uuid()->toString(),
                'type' => 'App\Notifications\\' . ucfirst(Str::camel($type)) . 'Notification',
                'data' => $data,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $notifications[] = $notification;
        }
        
        return $notifications;
    }
}
