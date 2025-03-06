<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\ContentReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentReportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $reporter;
    protected $reportedUser;
    protected $admin;
    protected $conversation;
    protected $message;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء المستخدمين
        $this->reporter = User::factory()->create([
            'name' => 'Reporter User',
            'email' => 'reporter@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->reportedUser = User::factory()->create([
            'name' => 'Reported User',
            'email' => 'reported@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء محادثة
        $this->conversation = Conversation::create([
            'type' => 'individual',
            'created_by' => $this->reportedUser->id,
        ]);
        
        // إضافة المشاركين
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->reporter->id,
            'is_admin' => false,
            'joined_at' => now(),
        ]);
        
        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->reportedUser->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        // إنشاء رسالة
        $this->message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->reportedUser->id,
            'type' => 'text',
            'message' => 'This is a message that will be reported',
        ]);
    }

    /**
     * اختبار إنشاء تقرير عن رسالة
     */
    public function test_user_can_report_message()
    {
        $reportData = [
            'reportable_type' => 'message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
        ];
        
        $response = $this->actingAs($this->reporter)
            ->postJson('/api/reports', $reportData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'reporter_id',
                'reportable_type',
                'reportable_id',
                'reason',
                'details',
                'status',
                'created_at',
            ]);
            
        $this->assertDatabaseHas('content_reports', [
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
    }

    /**
     * اختبار إنشاء تقرير عن مستخدم
     */
    public function test_user_can_report_another_user()
    {
        $reportData = [
            'reportable_type' => 'user',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'details' => 'This user is sending harassing messages',
        ];
        
        $response = $this->actingAs($this->reporter)
            ->postJson('/api/reports', $reportData);

        $response->assertStatus(201);
            
        $this->assertDatabaseHas('content_reports', [
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\User',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'status' => 'pending',
        ]);
    }

    /**
     * اختبار عرض تقارير المستخدم
     */
    public function test_user_can_view_own_reports()
    {
        // إنشاء بعض التقارير
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
            'status' => 'pending',
        ]);
        
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\User',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'details' => 'This user is sending harassing messages',
            'status' => 'pending',
        ]);
        
        $response = $this->actingAs($this->reporter)
            ->getJson('/api/reports/my-reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'reportable_type',
                        'reportable_id',
                        'reason',
                        'details',
                        'status',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'total',
                ],
            ]);
            
        $this->assertEquals(2, $response->json('meta.total'));
    }

    /**
     * اختبار عرض جميع التقارير من قبل المسؤول
     */
    public function test_admin_can_view_all_reports()
    {
        // إنشاء بعض التقارير
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
            'status' => 'pending',
        ]);
        
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\User',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'details' => 'This user is sending harassing messages',
            'status' => 'pending',
        ]);
        
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/reports');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'reporter',
                        'reportable_type',
                        'reportable_id',
                        'reason',
                        'details',
                        'status',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'total',
                    'pending',
                    'resolved',
                    'dismissed',
                ],
            ]);
            
        $this->assertEquals(2, $response->json('meta.total'));
        $this->assertEquals(2, $response->json('meta.pending'));
    }

    /**
     * اختبار تحديث حالة التقرير من قبل المسؤول
     */
    public function test_admin_can_update_report_status()
    {
        // إنشاء تقرير
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
            'status' => 'pending',
        ]);
        
        $updateData = [
            'status' => 'resolved',
            'admin_notes' => 'This report has been reviewed and resolved',
        ];
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/reports/{$report->id}", $updateData);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('content_reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'admin_notes' => 'This report has been reviewed and resolved',
        ]);
    }

    /**
     * اختبار رفض التقرير من قبل المسؤول
     */
    public function test_admin_can_dismiss_report()
    {
        // إنشاء تقرير
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
            'status' => 'pending',
        ]);
        
        $updateData = [
            'status' => 'dismissed',
            'admin_notes' => 'This report was reviewed and found to be invalid',
        ];
        
        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/reports/{$report->id}", $updateData);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('content_reports', [
            'id' => $report->id,
            'status' => 'dismissed',
            'admin_notes' => 'This report was reviewed and found to be invalid',
        ]);
    }

    /**
     * اختبار اتخاذ إجراء على تقرير من قبل المسؤول
     */
    public function test_admin_can_take_action_on_report()
    {
        // إنشاء تقرير عن مستخدم
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\User',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'details' => 'This user is sending harassing messages',
            'status' => 'pending',
        ]);
        
        $actionData = [
            'action' => 'suspend',
            'duration' => 7, // أيام
            'notes' => 'User suspended for 7 days due to harassment',
        ];
        
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/reports/{$report->id}/action", $actionData);

        $response->assertStatus(200);
            
        // التحقق من تحديث حالة التقرير
        $this->assertDatabaseHas('content_reports', [
            'id' => $report->id,
            'status' => 'resolved',
        ]);
        
        // التحقق من تعليق المستخدم
        $this->assertDatabaseHas('users', [
            'id' => $this->reportedUser->id,
            'status' => 'suspended',
        ]);
        
        // التحقق من إنشاء سجل الإجراء
        $this->assertDatabaseHas('admin_actions', [
            'admin_id' => $this->admin->id,
            'user_id' => $this->reportedUser->id,
            'action_type' => 'suspend',
            'reason' => 'harassment',
        ]);
    }

    /**
     * اختبار حذف محتوى تم الإبلاغ عنه
     */
    public function test_admin_can_delete_reported_content()
    {
        // إنشاء تقرير عن رسالة
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
            'status' => 'pending',
        ]);
        
        $actionData = [
            'action' => 'delete_content',
            'notes' => 'Message deleted due to inappropriate content',
        ];
        
        $response = $this->actingAs($this->admin)
            ->postJson("/api/admin/reports/{$report->id}/action", $actionData);

        $response->assertStatus(200);
            
        // التحقق من تحديث حالة التقرير
        $this->assertDatabaseHas('content_reports', [
            'id' => $report->id,
            'status' => 'resolved',
        ]);
        
        // التحقق من حذف الرسالة
        $this->assertDatabaseMissing('messages', [
            'id' => $this->message->id,
        ]);
        
        // التحقق من إنشاء سجل الإجراء
        $this->assertDatabaseHas('admin_actions', [
            'admin_id' => $this->admin->id,
            'action_type' => 'delete_content',
            'reason' => 'inappropriate_content',
        ]);
    }

    /**
     * اختبار تصفية التقارير حسب النوع
     */
    public function test_admin_can_filter_reports_by_type()
    {
        // إنشاء تقارير متنوعة
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
        
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\User',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'status' => 'pending',
        ]);
        
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/reports?type=message');

        $response->assertStatus(200);
            
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('App\Models\Message', $response->json('data.0.reportable_type'));
    }

    /**
     * اختبار تصفية التقارير حسب الحالة
     */
    public function test_admin_can_filter_reports_by_status()
    {
        // إنشاء تقارير بحالات مختلفة
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
        
        ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\User',
            'reportable_id' => $this->reportedUser->id,
            'reason' => 'harassment',
            'status' => 'resolved',
        ]);
        
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/reports?status=pending');

        $response->assertStatus(200);
            
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('pending', $response->json('data.0.status'));
    }

    /**
     * اختبار عدم قدرة المستخدم العادي على الوصول إلى تقارير المسؤول
     */
    public function test_regular_user_cannot_access_admin_reports()
    {
        $response = $this->actingAs($this->reporter)
            ->getJson('/api/admin/reports');

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم قدرة المستخدم العادي على تحديث حالة التقرير
     */
    public function test_regular_user_cannot_update_report_status()
    {
        // إنشاء تقرير
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
        
        $updateData = [
            'status' => 'resolved',
        ];
        
        $response = $this->actingAs($this->reporter)
            ->putJson("/api/admin/reports/{$report->id}", $updateData);

        $response->assertStatus(403);
    }

    /**
     * اختبار إلغاء تقرير من قبل المبلغ
     */
    public function test_reporter_can_cancel_own_report()
    {
        // إنشاء تقرير
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
        
        $response = $this->actingAs($this->reporter)
            ->deleteJson("/api/reports/{$report->id}");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('content_reports', [
            'id' => $report->id,
        ]);
    }

    /**
     * اختبار عدم قدرة المستخدم على إلغاء تقرير شخص آخر
     */
    public function test_user_cannot_cancel_others_report()
    {
        // إنشاء تقرير
        $report = ContentReport::create([
            'reporter_id' => $this->reporter->id,
            'reportable_type' => 'App\Models\Message',
            'reportable_id' => $this->message->id,
            'reason' => 'inappropriate_content',
            'status' => 'pending',
        ]);
        
        $otherUser = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'user_type' => 'user',
        ]);
        
        $response = $this->actingAs($otherUser)
            ->deleteJson("/api/reports/{$report->id}");

        $response->assertStatus(403);
            
        $this->assertDatabaseHas('content_reports', [
            'id' => $report->id,
        ]);
    }
}
