<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\VoiceCall;
use App\Events\MessageSentEvent;
use App\Events\MessageStatusUpdatedEvent;
use App\Events\VoiceCallEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast;
use Tests\TestCase;

class BroadcastingTest extends TestCase
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
     * اختبار بث حدث إرسال رسالة
     */
    public function test_message_sent_event_is_broadcasted()
    {
        Broadcast::shouldReceive('event')
            ->once()
            ->withArgs(function ($event) {
                return $event instanceof MessageSentEvent &&
                       $event->message->conversation_id === $this->conversation->id &&
                       $event->message->sender_id === $this->sender->id;
            });
        
        // إنشاء رسالة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'type' => 'text',
            'message' => 'Hello, this is a test message',
        ]);
        
        // إطلاق حدث إرسال الرسالة
        event(new MessageSentEvent($message));
    }

    /**
     * اختبار بث حدث تحديث حالة الرسالة
     */
    public function test_message_status_updated_event_is_broadcasted()
    {
        Broadcast::shouldReceive('event')
            ->once()
            ->withArgs(function ($event) {
                return $event instanceof MessageStatusUpdatedEvent &&
                       $event->message->conversation_id === $this->conversation->id &&
                       $event->user->id === $this->receiver->id &&
                       $event->status === 'read';
            });
        
        // إنشاء رسالة
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
    }

    /**
     * اختبار بث حدث المكالمة الصوتية
     */
    public function test_voice_call_event_is_broadcasted()
    {
        Broadcast::shouldReceive('event')
            ->once()
            ->withArgs(function ($event) {
                return $event instanceof VoiceCallEvent &&
                       $event->voiceCall->conversation_id === $this->conversation->id &&
                       $event->action === 'initiated';
            });
        
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
        
        // إطلاق حدث المكالمة الصوتية
        event(new VoiceCallEvent($voiceCall, 'initiated'));
    }

    /**
     * اختبار قناة المستخدم الخاصة
     */
    public function test_private_user_channel_authorization()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/broadcasting/auth', [
                'channel_name' => 'private-user.' . $this->sender->id,
                'socket_id' => '123.456',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['auth']);
    }

    /**
     * اختبار قناة المحادثة الخاصة
     */
    public function test_private_conversation_channel_authorization()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/broadcasting/auth', [
                'channel_name' => 'private-conversation.' . $this->conversation->id,
                'socket_id' => '123.456',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['auth']);
    }

    /**
     * اختبار عدم السماح بالوصول إلى قناة محادثة غير مشارك فيها
     */
    public function test_unauthorized_conversation_channel_access()
    {
        // إنشاء مستخدم غير مشارك في المحادثة
        $nonParticipant = User::factory()->create([
            'name' => 'Non Participant',
            'email' => 'non_participant@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $token = $nonParticipant->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/broadcasting/auth', [
                'channel_name' => 'private-conversation.' . $this->conversation->id,
                'socket_id' => '123.456',
            ]);

        $response->assertStatus(403);
    }

    /**
     * اختبار قناة المكالمة الصوتية الخاصة
     */
    public function test_private_voice_call_channel_authorization()
    {
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
        
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/broadcasting/auth', [
                'channel_name' => 'private-voice-call.' . $voiceCall->id,
                'socket_id' => '123.456',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['auth']);
    }

    /**
     * اختبار قناة البث العامة
     */
    public function test_public_channel_access()
    {
        $response = $this->getJson('/api/broadcasting/public-channels');

        $response->assertStatus(200)
            ->assertJsonStructure(['channels']);
    }

    /**
     * اختبار التوصيل بخادم البث
     */
    public function test_connect_to_broadcast_server()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/broadcasting/socket');

        $response->assertStatus(200)
            ->assertJsonStructure(['socket_id']);
    }

    /**
     * اختبار تسجيل حضور المستخدم
     */
    public function test_user_presence_channel()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/broadcasting/auth', [
                'channel_name' => 'presence-online',
                'socket_id' => '123.456',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'auth',
                'channel_data',
            ]);
    }

    /**
     * اختبار الاستماع إلى الأحداث
     */
    public function test_listen_to_events()
    {
        $token = $this->sender->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/broadcasting/events');

        $response->assertStatus(200)
            ->assertJsonStructure(['events']);
    }
}
