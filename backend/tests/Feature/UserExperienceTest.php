<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\VoiceCall;
use App\Models\Country;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserExperienceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user1;
    protected $user2;
    protected $conversation;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء بلد ومدينة
        $country = Country::create([
            'name' => 'Sudan',
            'code' => 'SD',
            'phone_code' => '+249',
        ]);
        
        $city = City::create([
            'name' => 'Khartoum',
            'country_id' => $country->id,
        ]);
        
        // إنشاء مستخدمين
        $this->user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);
        
        $this->user2 = User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
            'country_id' => $country->id,
            'city_id' => $city->id,
        ]);
        
        // إنشاء محادثة بين المستخدمين
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
    }

    /**
     * اختبار تحديث الملف الشخصي للمستخدم
     */
    public function test_user_can_update_profile()
    {
        $profileData = [
            'name' => 'Updated Name',
            'phone_number' => '123456789',
            'bio' => 'This is my updated bio',
            'profile_picture' => null, // يمكن إضافة اختبار لتحميل الصورة لاحقًا
        ];
        
        $response = $this->actingAs($this->user1)
            ->putJson('/api/profile', $profileData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone_number',
                    'bio',
                    'profile_picture',
                    'updated_at',
                ],
            ]);
            
        $this->assertDatabaseHas('users', [
            'id' => $this->user1->id,
            'name' => 'Updated Name',
            'phone_number' => '123456789',
            'bio' => 'This is my updated bio',
        ]);
    }

    /**
     * اختبار تغيير كلمة المرور
     */
    public function test_user_can_change_password()
    {
        $passwordData = [
            'current_password' => 'password', // كلمة المرور الافتراضية في factory
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];
        
        $response = $this->actingAs($this->user1)
            ->putJson('/api/profile/password', $passwordData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'تم تغيير كلمة المرور بنجاح',
            ]);
            
        // تسجيل الخروج
        $this->post('/api/logout');
        
        // محاولة تسجيل الدخول بكلمة المرور الجديدة
        $loginResponse = $this->postJson('/api/login', [
            'email' => $this->user1->email,
            'password' => 'NewPassword123!',
        ]);
        
        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                ],
            ]);
    }

    /**
     * اختبار تفعيل المصادقة الثنائية
     */
    public function test_user_can_enable_two_factor_auth()
    {
        $response = $this->actingAs($this->user1)
            ->postJson('/api/profile/two-factor-auth/enable');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'qr_code',
                    'recovery_codes',
                ],
            ]);
            
        $this->assertDatabaseHas('two_factor_authentications', [
            'user_id' => $this->user1->id,
            'is_enabled' => true,
        ]);
    }

    /**
     * اختبار البحث عن مستخدمين
     */
    public function test_user_can_search_for_users()
    {
        $response = $this->actingAs($this->user1)
            ->getJson('/api/users/search?query=User');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'profile_picture',
                    ],
                ],
            ]);
    }

    /**
     * اختبار عرض قائمة المحادثات
     */
    public function test_user_can_view_conversations()
    {
        $response = $this->actingAs($this->user1)
            ->getJson('/api/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'title',
                        'last_message',
                        'participants',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /**
     * اختبار إنشاء محادثة جديدة
     */
    public function test_user_can_create_new_conversation()
    {
        $response = $this->actingAs($this->user1)
            ->postJson('/api/conversations', [
                'type' => 'individual',
                'participants' => [$this->user2->id],
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
            'created_by' => $this->user1->id,
        ]);
    }

    /**
     * اختبار إنشاء محادثة جماعية
     */
    public function test_user_can_create_group_conversation()
    {
        // إنشاء مستخدم ثالث
        $user3 = User::factory()->create();
        
        $response = $this->actingAs($this->user1)
            ->postJson('/api/conversations', [
                'type' => 'group',
                'title' => 'Test Group',
                'description' => 'This is a test group',
                'participants' => [$this->user2->id, $user3->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'title',
                    'description',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('conversations', [
            'type' => 'group',
            'title' => 'Test Group',
            'description' => 'This is a test group',
            'created_by' => $this->user1->id,
        ]);
    }

    /**
     * اختبار إرسال رسالة نصية
     */
    public function test_user_can_send_text_message()
    {
        $response = $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$this->conversation->id}/messages", [
                'type' => 'text',
                'message' => 'Hello, this is a test message',
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
            'sender_id' => $this->user1->id,
            'type' => 'text',
            'message' => 'Hello, this is a test message',
        ]);
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

        $response = $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$this->conversation->id}/messages", [
                'type' => 'image',
                'file' => $this->createTestImage(),
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attachment_path',
                    'sender',
                    'created_at',
                ],
            ]);
            
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user1->id,
            'type' => 'image',
        ]);
    }

    /**
     * اختبار تحديث حالة الرسالة
     */
    public function test_message_status_can_be_updated()
    {
        // إنشاء رسالة جديدة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user1->id,
            'type' => 'text',
            'message' => 'Test message for status update',
        ]);
        
        $response = $this->actingAs($this->user2)
            ->patchJson("/api/messages/{$message->id}/status", [
                'status' => 'read',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('message_statuses', [
            'message_id' => $message->id,
            'user_id' => $this->user2->id,
            'status' => 'read',
        ]);
    }

    /**
     * اختبار حذف رسالة
     */
    public function test_user_can_delete_own_message()
    {
        // إنشاء رسالة جديدة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user1->id,
            'type' => 'text',
            'message' => 'Test message for deletion',
        ]);
        
        $response = $this->actingAs($this->user1)
            ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
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
            'sender_id' => $this->user1->id,
            'type' => 'text',
            'message' => 'Test message for deletion attempt',
        ]);
        
        $response = $this->actingAs($this->user2)
            ->deleteJson("/api/messages/{$message->id}");

        $response->assertStatus(403);
            
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
        ]);
    }

    /**
     * اختبار بدء مكالمة صوتية
     */
    public function test_user_can_start_voice_call()
    {
        $response = $this->actingAs($this->user1)
            ->postJson("/api/conversations/{$this->conversation->id}/voice-call");

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'conversation_id',
                    'initiated_by',
                    'status',
                    'started_at',
                    'call_token',
                ],
            ]);
            
        $this->assertDatabaseHas('voice_calls', [
            'conversation_id' => $this->conversation->id,
            'initiated_by' => $this->user1->id,
            'status' => 'initiated',
        ]);
    }

    /**
     * اختبار الانضمام إلى مكالمة صوتية
     */
    public function test_user_can_join_voice_call()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'conversation_id' => $this->conversation->id,
            'initiated_by' => $this->user1->id,
            'status' => 'initiated',
            'started_at' => now(),
        ]);
        
        $response = $this->actingAs($this->user2)
            ->postJson("/api/voice-calls/{$voiceCall->id}/join");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'conversation_id',
                    'status',
                    'call_token',
                ],
            ]);
            
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $voiceCall->id,
            'user_id' => $this->user2->id,
            'status' => 'joined',
        ]);
    }

    /**
     * اختبار إنهاء مكالمة صوتية
     */
    public function test_user_can_end_voice_call()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'conversation_id' => $this->conversation->id,
            'initiated_by' => $this->user1->id,
            'status' => 'active',
            'started_at' => now()->subMinutes(5),
        ]);
        
        $response = $this->actingAs($this->user1)
            ->postJson("/api/voice-calls/{$voiceCall->id}/end");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('voice_calls', [
            'id' => $voiceCall->id,
            'status' => 'ended',
        ]);
    }

    /**
     * اختبار الإبلاغ عن مستخدم
     */
    public function test_user_can_report_another_user()
    {
        $reportData = [
            'reason' => 'harassment',
            'details' => 'This user is sending inappropriate messages',
        ];
        
        $response = $this->actingAs($this->user1)
            ->postJson("/api/users/{$this->user2->id}/report", $reportData);

        $response->assertStatus(201);
            
        $this->assertDatabaseHas('user_reports', [
            'reporter_id' => $this->user1->id,
            'reported_user_id' => $this->user2->id,
            'reason' => 'harassment',
        ]);
    }

    /**
     * اختبار الإبلاغ عن محتوى
     */
    public function test_user_can_report_content()
    {
        // إنشاء رسالة
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user2->id,
            'type' => 'text',
            'message' => 'Test message for content report',
        ]);
        
        $reportData = [
            'content_type' => 'message',
            'content_id' => $message->id,
            'reason' => 'inappropriate_content',
            'details' => 'This message contains inappropriate content',
        ];
        
        $response = $this->actingAs($this->user1)
            ->postJson("/api/reports/content", $reportData);

        $response->assertStatus(201);
            
        $this->assertDatabaseHas('content_reports', [
            'reporter_id' => $this->user1->id,
            'content_type' => 'message',
            'content_id' => $message->id,
            'reason' => 'inappropriate_content',
        ]);
    }

    /**
     * اختبار حظر مستخدم
     */
    public function test_user_can_block_another_user()
    {
        $response = $this->actingAs($this->user1)
            ->postJson("/api/users/{$this->user2->id}/block");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('user_blocks', [
            'user_id' => $this->user1->id,
            'blocked_user_id' => $this->user2->id,
        ]);
    }

    /**
     * اختبار إلغاء حظر مستخدم
     */
    public function test_user_can_unblock_another_user()
    {
        // حظر المستخدم أولاً
        \App\Models\UserBlock::create([
            'user_id' => $this->user1->id,
            'blocked_user_id' => $this->user2->id,
        ]);
        
        $response = $this->actingAs($this->user1)
            ->deleteJson("/api/users/{$this->user2->id}/block");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('user_blocks', [
            'user_id' => $this->user1->id,
            'blocked_user_id' => $this->user2->id,
        ]);
    }

    /**
     * إنشاء صورة اختبار وهمية
     */
    protected function createTestImage()
    {
        $file = \Illuminate\Http\Testing\File::image('test.jpg', 400, 400);
        return $file;
    }
}
