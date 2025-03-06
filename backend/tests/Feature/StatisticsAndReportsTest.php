<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\VoiceCall;
use App\Models\ContentReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatisticsAndReportsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $user1;
    protected $user2;
    protected $conversation;

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
        
        // إنشاء رسائل
        for ($i = 0; $i < 5; $i++) {
            Message::create([
                'conversation_id' => $this->conversation->id,
                'sender_id' => $this->user1->id,
                'type' => 'text',
                'message' => 'Message from user1 ' . ($i + 1),
            ]);
            
            Message::create([
                'conversation_id' => $this->conversation->id,
                'sender_id' => $this->user2->id,
                'type' => 'text',
                'message' => 'Message from user2 ' . ($i + 1),
            ]);
        }
        
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'conversation_id' => $this->conversation->id,
            'initiated_by' => $this->user1->id,
            'status' => 'ended',
            'started_at' => now()->subMinutes(10),
            'ended_at' => now(),
        ]);
        
        // إضافة المشاركين
        $voiceCall->participants()->createMany([
            [
                'user_id' => $this->user1->id,
                'status' => 'joined',
            ],
            [
                'user_id' => $this->user2->id,
                'status' => 'joined',
            ],
        ]);
        
        // إنشاء تقارير محتوى
        ContentReport::create([
            'reporter_id' => $this->user1->id,
            'reported_user_id' => $this->user2->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => Message::where('sender_id', $this->user2->id)->first()->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
            'status' => 'pending',
        ]);
    }

    /**
     * اختبار إحصائيات عامة
     */
    public function test_admin_can_view_general_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/general');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_users',
                'total_conversations',
                'total_messages',
                'total_voice_calls',
                'total_reports',
            ]);
        
        // التحقق من الإحصائيات
        $this->assertEquals(3, $response->json('total_users'));
        $this->assertEquals(1, $response->json('total_conversations'));
        $this->assertEquals(10, $response->json('total_messages'));
        $this->assertEquals(1, $response->json('total_voice_calls'));
        $this->assertEquals(1, $response->json('total_reports'));
    }

    /**
     * اختبار إحصائيات المستخدمين
     */
    public function test_admin_can_view_user_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_users',
                'new_users_today',
                'new_users_this_week',
                'new_users_this_month',
                'active_users_today',
                'active_users_this_week',
                'active_users_this_month',
                'user_types' => [
                    'admin',
                    'user',
                ],
            ]);
    }

    /**
     * اختبار إحصائيات المراسلة
     */
    public function test_admin_can_view_messaging_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/messaging');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_conversations',
                'total_messages',
                'messages_today',
                'messages_this_week',
                'messages_this_month',
                'conversation_types' => [
                    'individual',
                    'group',
                ],
                'message_types' => [
                    'text',
                    'image',
                    'file',
                ],
            ]);
        
        // التحقق من الإحصائيات
        $this->assertEquals(1, $response->json('total_conversations'));
        $this->assertEquals(10, $response->json('total_messages'));
    }

    /**
     * اختبار إحصائيات المكالمات الصوتية
     */
    public function test_admin_can_view_voice_call_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/voice-calls');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_voice_calls',
                'voice_calls_today',
                'voice_calls_this_week',
                'voice_calls_this_month',
                'average_call_duration',
                'total_call_duration',
            ]);
        
        // التحقق من الإحصائيات
        $this->assertEquals(1, $response->json('total_voice_calls'));
    }

    /**
     * اختبار إحصائيات التقارير
     */
    public function test_admin_can_view_report_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_reports',
                'reports_today',
                'reports_this_week',
                'reports_this_month',
                'report_statuses' => [
                    'pending',
                    'resolved',
                    'rejected',
                ],
                'report_types' => [
                    'message',
                    'user',
                ],
                'report_reasons' => [
                    'inappropriate_content',
                    'spam',
                    'harassment',
                    'other',
                ],
            ]);
        
        // التحقق من الإحصائيات
        $this->assertEquals(1, $response->json('total_reports'));
    }

    /**
     * اختبار إحصائيات المستخدم الفردي
     */
    public function test_admin_can_view_individual_user_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/users/' . $this->user1->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'total_conversations',
                'total_messages_sent',
                'total_voice_calls_initiated',
                'total_voice_calls_participated',
                'total_reports_submitted',
                'total_reports_received',
                'account_created_at',
                'last_active_at',
            ]);
        
        // التحقق من الإحصائيات
        $this->assertEquals(1, $response->json('total_conversations'));
        $this->assertEquals(5, $response->json('total_messages_sent'));
        $this->assertEquals(1, $response->json('total_voice_calls_initiated'));
        $this->assertEquals(1, $response->json('total_voice_calls_participated'));
        $this->assertEquals(1, $response->json('total_reports_submitted'));
        $this->assertEquals(0, $response->json('total_reports_received'));
    }

    /**
     * اختبار إحصائيات المحادثة الفردية
     */
    public function test_admin_can_view_individual_conversation_statistics()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/conversations/' . $this->conversation->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'conversation' => [
                    'id',
                    'type',
                    'created_by',
                ],
                'total_participants',
                'total_messages',
                'total_voice_calls',
                'total_reports',
                'created_at',
                'last_message_at',
            ]);
        
        // التحقق من الإحصائيات
        $this->assertEquals(2, $response->json('total_participants'));
        $this->assertEquals(10, $response->json('total_messages'));
        $this->assertEquals(1, $response->json('total_voice_calls'));
        $this->assertEquals(1, $response->json('total_reports'));
    }

    /**
     * اختبار تقرير المستخدمين النشطين
     */
    public function test_admin_can_generate_active_users_report()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/active-users?period=month');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period',
                'total_active_users',
                'users' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'message_count',
                        'voice_call_count',
                        'last_active_at',
                    ],
                ],
            ]);
    }

    /**
     * اختبار تقرير المحادثات النشطة
     */
    public function test_admin_can_generate_active_conversations_report()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/active-conversations?period=month');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period',
                'total_active_conversations',
                'conversations' => [
                    '*' => [
                        'id',
                        'type',
                        'participant_count',
                        'message_count',
                        'voice_call_count',
                        'last_active_at',
                    ],
                ],
            ]);
    }

    /**
     * اختبار تقرير التقارير المعلقة
     */
    public function test_admin_can_generate_pending_reports_report()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/pending-reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_pending_reports',
                'reports' => [
                    '*' => [
                        'id',
                        'reporter' => [
                            'id',
                            'name',
                        ],
                        'reported_user' => [
                            'id',
                            'name',
                        ],
                        'reportable_type',
                        'reason',
                        'details',
                        'created_at',
                    ],
                ],
            ]);
        
        // التحقق من التقرير
        $this->assertEquals(1, $response->json('total_pending_reports'));
    }

    /**
     * اختبار تقرير نمو المستخدمين
     */
    public function test_admin_can_generate_user_growth_report()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/user-growth?period=month');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period',
                'total_users',
                'new_users',
                'growth_percentage',
                'data_points' => [
                    '*' => [
                        'date',
                        'count',
                    ],
                ],
            ]);
    }

    /**
     * اختبار تقرير نشاط المراسلة
     */
    public function test_admin_can_generate_messaging_activity_report()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/messaging-activity?period=month');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period',
                'total_messages',
                'average_messages_per_day',
                'data_points' => [
                    '*' => [
                        'date',
                        'count',
                    ],
                ],
            ]);
    }

    /**
     * اختبار تقرير نشاط المكالمات الصوتية
     */
    public function test_admin_can_generate_voice_call_activity_report()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/voice-call-activity?period=month');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'period',
                'total_voice_calls',
                'average_voice_calls_per_day',
                'average_duration',
                'data_points' => [
                    '*' => [
                        'date',
                        'count',
                        'duration',
                    ],
                ],
            ]);
    }

    /**
     * اختبار تصدير البيانات
     */
    public function test_admin_can_export_data()
    {
        $token = $this->admin->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/export/users');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertHeader('Content-Disposition', 'attachment; filename=users.csv');
    }

    /**
     * اختبار عدم السماح للمستخدم العادي بالوصول إلى الإحصائيات
     */
    public function test_regular_user_cannot_access_statistics()
    {
        $token = $this->user1->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/general');

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم السماح للمستخدم العادي بالوصول إلى التقارير
     */
    public function test_regular_user_cannot_access_reports()
    {
        $token = $this->user1->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/reports/active-users');

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم السماح للمستخدم العادي بتصدير البيانات
     */
    public function test_regular_user_cannot_export_data()
    {
        $token = $this->user1->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/export/users');

        $response->assertStatus(403);
    }
}
