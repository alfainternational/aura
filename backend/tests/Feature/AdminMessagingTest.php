<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\VoiceCall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminMessagingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user1;
    protected $user2;
    protected $conversation;
    protected $message;
    protected $voiceCall;

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
        
        // إنشاء محادثة
        $this->conversation = Conversation::create([
            'type' => 'individual',
            'created_by' => $this->user1->id,
        ]);
        
        // إضافة المشاركين
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user1->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user2->id,
            'is_admin' => false,
            'joined_at' => now(),
        ]);
        
        // إنشاء رسالة
        $this->message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user1->id,
            'type' => 'text',
            'message' => 'Hello, this is a test message',
        ]);
        
        // إنشاء مكالمة صوتية
        $this->voiceCall = VoiceCall::create([
            'conversation_id' => $this->conversation->id,
            'initiated_by' => $this->user1->id,
            'status' => 'ended',
            'started_at' => now()->subMinutes(5),
            'ended_at' => now(),
            'duration' => 300, // 5 دقائق
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة المحادثات
     */
    public function test_admin_can_view_conversations()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'created_by',
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
     * اختبار أن المسؤول يمكنه عرض تفاصيل محادثة محددة
     */
    public function test_admin_can_view_conversation_details()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/conversations/{$this->conversation->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'created_by',
                    'participants' => [
                        '*' => [
                            'id',
                            'user_id',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ],
                            'is_admin',
                            'joined_at',
                        ],
                    ],
                    'messages' => [
                        '*' => [
                            'id',
                            'type',
                            'message',
                            'sender_id',
                            'created_at',
                        ],
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة الرسائل
     */
    public function test_admin_can_view_messages()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/messages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'conversation_id',
                        'sender_id',
                        'type',
                        'message',
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
     * اختبار أن المسؤول يمكنه عرض تفاصيل رسالة محددة
     */
    public function test_admin_can_view_message_details()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/messages/{$this->message->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'conversation_id',
                    'sender' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'type',
                    'message',
                    'statuses' => [
                        '*' => [
                            'user_id',
                            'status',
                            'updated_at',
                        ],
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه حذف رسالة
     */
    public function test_admin_can_delete_message()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/messages/{$this->message->id}");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('messages', [
            'id' => $this->message->id,
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض قائمة المكالمات الصوتية
     */
    public function test_admin_can_view_voice_calls()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/voice-calls');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'conversation_id',
                        'initiated_by',
                        'status',
                        'started_at',
                        'ended_at',
                        'duration',
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
     * اختبار أن المسؤول يمكنه عرض تفاصيل مكالمة صوتية محددة
     */
    public function test_admin_can_view_voice_call_details()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/voice-calls/{$this->voiceCall->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'conversation_id',
                    'initiator' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'participants' => [
                        '*' => [
                            'user_id',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ],
                            'status',
                            'joined_at',
                            'left_at',
                        ],
                    ],
                    'status',
                    'started_at',
                    'ended_at',
                    'duration',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض إحصائيات المراسلة
     */
    public function test_admin_can_view_messaging_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/messaging');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_conversations',
                    'total_messages',
                    'messages_per_day',
                    'active_conversations',
                    'message_types',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض إحصائيات المكالمات الصوتية
     */
    public function test_admin_can_view_voice_call_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/voice-calls');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_calls',
                    'total_duration',
                    'calls_per_day',
                    'average_duration',
                    'call_statuses',
                ],
            ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض تقارير المحتوى المبلغ عنه
     */
    public function test_admin_can_view_content_reports()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/reports/content');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'reporter_id',
                        'content_type',
                        'content_id',
                        'reason',
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
     * اختبار أن المسؤول يمكنه تحديث حالة تقرير محتوى
     */
    public function test_admin_can_update_content_report_status()
    {
        // إنشاء تقرير محتوى
        $contentReport = \App\Models\ContentReport::create([
            'reporter_id' => $this->user2->id,
            'content_type' => 'message',
            'content_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/reports/content/{$contentReport->id}", [
                'status' => 'resolved',
                'resolution_note' => 'Content removed',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('content_reports', [
            'id' => $contentReport->id,
            'status' => 'resolved',
        ]);
    }

    /**
     * اختبار أن المسؤول يمكنه عرض تقارير المستخدمين المبلغ عنهم
     */
    public function test_admin_can_view_user_reports()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/reports/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'reporter_id',
                        'reported_user_id',
                        'reason',
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
     * اختبار أن المسؤول يمكنه تحديث حالة تقرير مستخدم
     */
    public function test_admin_can_update_user_report_status()
    {
        // إنشاء تقرير مستخدم
        $userReport = \App\Models\UserReport::create([
            'reporter_id' => $this->user2->id,
            'reported_user_id' => $this->user1->id,
            'reason' => 'harassment',
            'status' => 'pending',
        ]);
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/reports/users/{$userReport->id}", [
                'status' => 'resolved',
                'resolution_note' => 'User warned',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('user_reports', [
            'id' => $userReport->id,
            'status' => 'resolved',
        ]);
    }
}
