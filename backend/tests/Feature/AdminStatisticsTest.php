<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\VoiceCall;
use App\Models\VoiceCallParticipant;
use App\Models\Country;
use App\Models\City;
use App\Models\ContentReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class AdminStatisticsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $users = [];
    protected $conversations = [];

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مسؤول
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء بلدان ومدن
        $sudan = Country::create([
            'name' => 'Sudan',
            'code' => 'SD',
            'phone_code' => '+249',
        ]);
        
        $khartoum = City::create([
            'name' => 'Khartoum',
            'country_id' => $sudan->id,
        ]);
        
        $omdurman = City::create([
            'name' => 'Omdurman',
            'country_id' => $sudan->id,
        ]);
        
        // إنشاء مستخدمين
        for ($i = 0; $i < 20; $i++) {
            $createdAt = now()->subDays(rand(0, 30));
            $city = rand(0, 1) ? $khartoum->id : $omdurman->id;
            
            $user = User::factory()->create([
                'name' => "Test User {$i}",
                'email' => "user{$i}@example.com",
                'user_type' => 'user',
                'email_verified_at' => $createdAt,
                'country_id' => $sudan->id,
                'city_id' => $city,
                'created_at' => $createdAt,
            ]);
            
            $this->users[] = $user;
        }
        
        // إنشاء محادثات
        for ($i = 0; $i < 10; $i++) {
            $createdAt = now()->subDays(rand(0, 30));
            $createdBy = $this->users[rand(0, count($this->users) - 1)]->id;
            $type = rand(0, 1) ? 'individual' : 'group';
            
            $conversation = Conversation::create([
                'type' => $type,
                'created_by' => $createdBy,
                'title' => $type === 'group' ? "Group {$i}" : null,
                'created_at' => $createdAt,
            ]);
            
            // إضافة المشاركين
            $participantCount = $type === 'individual' ? 2 : rand(3, 8);
            $participants = array_rand(array_flip(array_column($this->users, 'id')), $participantCount);
            
            if (!is_array($participants)) {
                $participants = [$participants];
            }
            
            foreach ($participants as $participantId) {
                ConversationParticipant::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $participantId,
                    'is_admin' => $participantId === $createdBy,
                    'joined_at' => $createdAt,
                ]);
            }
            
            // إنشاء رسائل
            $messageCount = rand(5, 20);
            for ($j = 0; $j < $messageCount; $j++) {
                $messageCreatedAt = $createdAt->copy()->addHours(rand(1, 24 * 30));
                if ($messageCreatedAt->isAfter(now())) {
                    $messageCreatedAt = now();
                }
                
                $sender = $participants[rand(0, count($participants) - 1)];
                $messageType = rand(0, 10) < 8 ? 'text' : 'image';
                
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender,
                    'type' => $messageType,
                    'message' => $messageType === 'text' ? "Test message {$j}" : 'test_image.jpg',
                    'created_at' => $messageCreatedAt,
                ]);
            }
            
            // إنشاء مكالمات صوتية
            if (rand(0, 1)) {
                $callCreatedAt = $createdAt->copy()->addHours(rand(1, 24 * 30));
                if ($callCreatedAt->isAfter(now())) {
                    $callCreatedAt = now();
                }
                
                $initiator = $participants[rand(0, count($participants) - 1)];
                $duration = rand(30, 600); // 30 ثانية إلى 10 دقائق
                
                $voiceCall = VoiceCall::create([
                    'conversation_id' => $conversation->id,
                    'initiated_by' => $initiator,
                    'status' => 'ended',
                    'started_at' => $callCreatedAt,
                    'ended_at' => $callCreatedAt->copy()->addSeconds($duration),
                    'created_at' => $callCreatedAt,
                ]);
                
                // إضافة مشاركي المكالمة
                foreach ($participants as $participantId) {
                    $status = rand(0, 10) < 8 ? 'joined' : 'declined';
                    
                    VoiceCallParticipant::create([
                        'voice_call_id' => $voiceCall->id,
                        'user_id' => $participantId,
                        'status' => $status,
                        'joined_at' => $status === 'joined' ? $callCreatedAt->copy()->addSeconds(rand(1, 10)) : null,
                        'left_at' => $status === 'joined' ? $callCreatedAt->copy()->addSeconds($duration - rand(0, 30)) : null,
                    ]);
                }
            }
            
            $this->conversations[] = $conversation;
        }
        
        // إنشاء تقارير محتوى
        for ($i = 0; $i < 5; $i++) {
            $reportCreatedAt = now()->subDays(rand(0, 30));
            $reporter = $this->users[rand(0, count($this->users) - 1)]->id;
            
            // تقارير عن رسائل
            $message = Message::inRandomOrder()->first();
            ContentReport::create([
                'reporter_id' => $reporter,
                'reportable_type' => 'App\Models\Message',
                'reportable_id' => $message->id,
                'reason' => ['inappropriate_content', 'harassment', 'spam'][rand(0, 2)],
                'details' => "Report details {$i}",
                'status' => ['pending', 'resolved', 'dismissed'][rand(0, 2)],
                'created_at' => $reportCreatedAt,
            ]);
            
            // تقارير عن مستخدمين
            $reportedUser = $this->users[rand(0, count($this->users) - 1)]->id;
            ContentReport::create([
                'reporter_id' => $reporter,
                'reportable_type' => 'App\Models\User',
                'reportable_id' => $reportedUser,
                'reason' => ['inappropriate_behavior', 'impersonation', 'spam'][rand(0, 2)],
                'details' => "User report details {$i}",
                'status' => ['pending', 'resolved', 'dismissed'][rand(0, 2)],
                'created_at' => $reportCreatedAt,
            ]);
        }
    }

    /**
     * اختبار عرض إحصائيات عامة
     */
    public function test_admin_can_view_general_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/general');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_users',
                'active_users',
                'total_conversations',
                'total_messages',
                'total_voice_calls',
                'total_reports',
                'pending_reports',
            ]);
            
        $this->assertEquals(count($this->users) + 1, $response->json('total_users')); // +1 للمسؤول
        $this->assertEquals(count($this->conversations), $response->json('total_conversations'));
    }

    /**
     * اختبار عرض إحصائيات المستخدمين
     */
    public function test_admin_can_view_user_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_users',
                'new_users',
                'active_users',
                'inactive_users',
                'verified_users',
                'unverified_users',
                'users_by_country',
                'users_by_city',
                'registration_trend',
            ]);
            
        $this->assertEquals(count($this->users) + 1, $response->json('total_users')); // +1 للمسؤول
    }

    /**
     * اختبار عرض إحصائيات المحادثات والرسائل
     */
    public function test_admin_can_view_messaging_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/messaging');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_conversations',
                'individual_conversations',
                'group_conversations',
                'total_messages',
                'text_messages',
                'image_messages',
                'average_messages_per_conversation',
                'message_trend',
            ]);
            
        $this->assertEquals(count($this->conversations), $response->json('total_conversations'));
        $individualCount = Conversation::where('type', 'individual')->count();
        $this->assertEquals($individualCount, $response->json('individual_conversations'));
    }

    /**
     * اختبار عرض إحصائيات المكالمات الصوتية
     */
    public function test_admin_can_view_voice_call_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/voice-calls');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_calls',
                'completed_calls',
                'missed_calls',
                'average_duration',
                'total_duration',
                'calls_trend',
            ]);
            
        $totalCalls = VoiceCall::count();
        $this->assertEquals($totalCalls, $response->json('total_calls'));
    }

    /**
     * اختبار عرض إحصائيات التقارير
     */
    public function test_admin_can_view_reports_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_reports',
                'pending_reports',
                'resolved_reports',
                'dismissed_reports',
                'reports_by_type',
                'reports_by_reason',
                'reports_trend',
            ]);
            
        $totalReports = ContentReport::count();
        $this->assertEquals($totalReports, $response->json('total_reports'));
    }

    /**
     * اختبار عرض إحصائيات حسب فترة زمنية محددة
     */
    public function test_admin_can_view_statistics_by_date_range()
    {
        $startDate = now()->subDays(15)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/statistics/general?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200);
        
        // التحقق من أن الإحصائيات تعكس الفترة الزمنية المحددة
        $usersInRange = User::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ])->count();
        
        $this->assertEquals($usersInRange, $response->json('new_users'));
    }

    /**
     * اختبار عرض إحصائيات النشاط اليومي
     */
    public function test_admin_can_view_daily_activity_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/daily-activity');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'messages_by_hour',
                'voice_calls_by_hour',
                'active_users_by_hour',
            ]);
    }

    /**
     * اختبار عرض إحصائيات المستخدمين الأكثر نشاطاً
     */
    public function test_admin_can_view_most_active_users_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/most-active-users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'most_messages_sent',
                'most_conversations',
                'most_voice_calls',
            ]);
    }

    /**
     * اختبار عرض إحصائيات المحادثات الأكثر نشاطاً
     */
    public function test_admin_can_view_most_active_conversations_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/most-active-conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'most_messages',
                'most_participants',
                'most_voice_calls',
            ]);
    }

    /**
     * اختبار عرض إحصائيات حسب المدينة والبلد
     */
    public function test_admin_can_view_location_based_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/location');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users_by_country',
                'users_by_city',
                'activity_by_country',
                'activity_by_city',
            ]);
    }

    /**
     * اختبار عرض تحليلات الاتجاهات
     */
    public function test_admin_can_view_trend_analysis()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/trends');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user_growth',
                'message_volume',
                'voice_call_usage',
                'report_volume',
            ]);
    }

    /**
     * اختبار عدم قدرة المستخدم العادي على الوصول إلى الإحصائيات
     */
    public function test_regular_user_cannot_access_statistics()
    {
        $regularUser = $this->users[0];
        
        $response = $this->actingAs($regularUser)
            ->getJson('/api/admin/statistics/general');

        $response->assertStatus(403);
    }

    /**
     * اختبار تصدير الإحصائيات
     */
    public function test_admin_can_export_statistics()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/statistics/export');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json');
    }

    /**
     * اختبار عرض لوحة المعلومات
     */
    public function test_admin_can_view_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'general_stats',
                'recent_users',
                'recent_reports',
                'activity_chart',
            ]);
    }
}
