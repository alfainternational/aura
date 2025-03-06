<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserSetting;
use App\Models\NotificationSetting;
use App\Models\PrivacySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدم
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * اختبار عرض إعدادات المستخدم
     */
    public function test_user_can_view_settings()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'notification_settings',
                    'privacy_settings',
                    'language',
                    'theme',
                ],
            ]);
    }

    /**
     * اختبار تحديث إعدادات الإشعارات
     */
    public function test_user_can_update_notification_settings()
    {
        $notificationSettings = [
            'new_message' => true,
            'voice_call' => true,
            'message_read' => false,
            'new_user_joined' => false,
            'email_notifications' => true,
            'push_notifications' => true,
        ];
        
        $response = $this->actingAs($this->user)
            ->putJson('/api/settings/notifications', $notificationSettings);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('notification_settings', [
            'user_id' => $this->user->id,
            'new_message' => true,
            'voice_call' => true,
            'message_read' => false,
            'new_user_joined' => false,
            'email_notifications' => true,
            'push_notifications' => true,
        ]);
    }

    /**
     * اختبار تحديث إعدادات الخصوصية
     */
    public function test_user_can_update_privacy_settings()
    {
        $privacySettings = [
            'profile_visibility' => 'contacts_only',
            'last_seen' => 'nobody',
            'read_receipts' => true,
            'typing_indicator' => false,
            'voice_call_permission' => 'contacts_only',
        ];
        
        $response = $this->actingAs($this->user)
            ->putJson('/api/settings/privacy', $privacySettings);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('privacy_settings', [
            'user_id' => $this->user->id,
            'profile_visibility' => 'contacts_only',
            'last_seen' => 'nobody',
            'read_receipts' => true,
            'typing_indicator' => false,
            'voice_call_permission' => 'contacts_only',
        ]);
    }

    /**
     * اختبار تحديث إعدادات اللغة
     */
    public function test_user_can_update_language_setting()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/settings/language', [
                'language' => 'ar',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $this->user->id,
            'language' => 'ar',
        ]);
    }

    /**
     * اختبار تحديث إعدادات السمة
     */
    public function test_user_can_update_theme_setting()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/settings/theme', [
                'theme' => 'dark',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $this->user->id,
            'theme' => 'dark',
        ]);
    }

    /**
     * اختبار تحديث إعدادات الإشعارات الصوتية
     */
    public function test_user_can_update_sound_settings()
    {
        $response = $this->actingAs($this->user)
            ->putJson('/api/settings/sounds', [
                'message_sound' => true,
                'call_sound' => true,
                'notification_sound' => false,
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $this->user->id,
            'message_sound' => true,
            'call_sound' => true,
            'notification_sound' => false,
        ]);
    }

    /**
     * اختبار تحديث إعدادات الحظر
     */
    public function test_user_can_view_blocked_users()
    {
        // إنشاء مستخدمين محظورين
        $blockedUser1 = User::factory()->create();
        $blockedUser2 = User::factory()->create();
        
        // إضافة المستخدمين إلى قائمة الحظر
        \App\Models\UserBlock::create([
            'user_id' => $this->user->id,
            'blocked_user_id' => $blockedUser1->id,
        ]);
        
        \App\Models\UserBlock::create([
            'user_id' => $this->user->id,
            'blocked_user_id' => $blockedUser2->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->getJson('/api/settings/blocked-users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'profile_picture',
                        'blocked_at',
                    ],
                ],
            ]);
            
        $this->assertCount(2, $response->json('data'));
    }

    /**
     * اختبار حظر مستخدم
     */
    public function test_user_can_block_another_user()
    {
        $userToBlock = User::factory()->create();
        
        $response = $this->actingAs($this->user)
            ->postJson("/api/users/{$userToBlock->id}/block");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('user_blocks', [
            'user_id' => $this->user->id,
            'blocked_user_id' => $userToBlock->id,
        ]);
    }

    /**
     * اختبار إلغاء حظر مستخدم
     */
    public function test_user_can_unblock_another_user()
    {
        $userToUnblock = User::factory()->create();
        
        // حظر المستخدم أولاً
        \App\Models\UserBlock::create([
            'user_id' => $this->user->id,
            'blocked_user_id' => $userToUnblock->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/users/{$userToUnblock->id}/block");

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('user_blocks', [
            'user_id' => $this->user->id,
            'blocked_user_id' => $userToUnblock->id,
        ]);
    }

    /**
     * اختبار تحديث إعدادات الإشعارات لمحادثة محددة
     */
    public function test_user_can_update_conversation_notification_settings()
    {
        // إنشاء محادثة
        $conversation = \App\Models\Conversation::create([
            'type' => 'individual',
            'created_by' => $this->user->id,
        ]);
        
        // إضافة المستخدم كمشارك
        \App\Models\ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $this->user->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        $response = $this->actingAs($this->user)
            ->putJson("/api/conversations/{$conversation->id}/settings", [
                'muted' => true,
                'mute_until' => now()->addDays(7)->toDateTimeString(),
                'custom_notification_sound' => 'sound1.mp3',
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('conversation_settings', [
            'conversation_id' => $conversation->id,
            'user_id' => $this->user->id,
            'muted' => true,
            'custom_notification_sound' => 'sound1.mp3',
        ]);
    }

    /**
     * اختبار تصدير بيانات المستخدم
     */
    public function test_user_can_export_data()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/settings/export-data');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'export_id',
                    'status',
                    'estimated_completion_time',
                ],
            ]);
    }

    /**
     * اختبار حذف حساب المستخدم
     */
    public function test_user_can_delete_account()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson('/api/settings/account', [
                'password' => 'password', // كلمة المرور الافتراضية في factory
            ]);

        $response->assertStatus(200);
            
        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);
        
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
    }
}
