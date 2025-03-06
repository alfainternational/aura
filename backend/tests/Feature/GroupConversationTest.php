<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GroupConversationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $creator;
    protected $participants = [];
    protected $groupConversation;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء منشئ المجموعة
        $this->creator = User::factory()->create([
            'name' => 'Group Creator',
            'email' => 'creator@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء مشاركين
        for ($i = 0; $i < 5; $i++) {
            $this->participants[] = User::factory()->create([
                'name' => "Participant {$i}",
                'email' => "participant{$i}@example.com",
                'user_type' => 'user',
                'email_verified_at' => now(),
            ]);
        }
    }

    /**
     * اختبار إنشاء محادثة جماعية
     */
    public function test_user_can_create_group_conversation()
    {
        $participantIds = array_map(function ($participant) {
            return $participant->id;
        }, $this->participants);
        
        $groupData = [
            'type' => 'group',
            'title' => 'Test Group',
            'description' => 'This is a test group',
            'participants' => $participantIds,
        ];
        
        $response = $this->actingAs($this->creator)
            ->postJson('/api/conversations', $groupData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'type',
                'title',
                'description',
                'created_by',
                'created_at',
            ]);
            
        $this->groupConversation = Conversation::find($response->json('id'));
        
        // التحقق من إنشاء المجموعة
        $this->assertDatabaseHas('conversations', [
            'id' => $this->groupConversation->id,
            'type' => 'group',
            'title' => 'Test Group',
            'description' => 'This is a test group',
            'created_by' => $this->creator->id,
        ]);
        
        // التحقق من إضافة المشاركين
        foreach ($participantIds as $participantId) {
            $this->assertDatabaseHas('conversation_participants', [
                'conversation_id' => $this->groupConversation->id,
                'user_id' => $participantId,
                'is_admin' => false,
            ]);
        }
        
        // التحقق من إضافة المنشئ كمسؤول
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $this->groupConversation->id,
            'user_id' => $this->creator->id,
            'is_admin' => true,
        ]);
    }

    /**
     * اختبار إضافة مشاركين إلى المجموعة
     */
    public function test_admin_can_add_participants_to_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // إنشاء مستخدمين جدد للإضافة
        $newParticipants = [];
        for ($i = 0; $i < 3; $i++) {
            $newParticipants[] = User::factory()->create([
                'name' => "New Participant {$i}",
                'email' => "new_participant{$i}@example.com",
                'user_type' => 'user',
                'email_verified_at' => now(),
            ]);
        }
        
        $newParticipantIds = array_map(function ($participant) {
            return $participant->id;
        }, $newParticipants);
        
        $response = $this->actingAs($this->creator)
            ->postJson("/api/conversations/{$this->groupConversation->id}/participants", [
                'participants' => $newParticipantIds,
            ]);

        $response->assertStatus(200);
            
        // التحقق من إضافة المشاركين الجدد
        foreach ($newParticipantIds as $participantId) {
            $this->assertDatabaseHas('conversation_participants', [
                'conversation_id' => $this->groupConversation->id,
                'user_id' => $participantId,
            ]);
        }
    }

    /**
     * اختبار إزالة مشاركين من المجموعة
     */
    public function test_admin_can_remove_participants_from_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك لإزالته
        $participantToRemove = $this->participants[0];
        
        $response = $this->actingAs($this->creator)
            ->deleteJson("/api/conversations/{$this->groupConversation->id}/participants/{$participantToRemove->id}");

        $response->assertStatus(200);
            
        // التحقق من إزالة المشارك
        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $this->groupConversation->id,
            'user_id' => $participantToRemove->id,
        ]);
    }

    /**
     * اختبار تعيين مشارك كمسؤول
     */
    public function test_admin_can_make_participant_admin()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك لتعيينه كمسؤول
        $participantToPromote = $this->participants[0];
        
        $response = $this->actingAs($this->creator)
            ->putJson("/api/conversations/{$this->groupConversation->id}/participants/{$participantToPromote->id}/admin", [
                'is_admin' => true,
            ]);

        $response->assertStatus(200);
            
        // التحقق من تعيين المشارك كمسؤول
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $this->groupConversation->id,
            'user_id' => $participantToPromote->id,
            'is_admin' => true,
        ]);
    }

    /**
     * اختبار إزالة صلاحية المسؤول من مشارك
     */
    public function test_admin_can_remove_admin_privileges()
    {
        // إنشاء محادثة جماعية وتعيين مشارك كمسؤول أولاً
        $this->test_admin_can_make_participant_admin();
        
        // اختيار المشارك المسؤول
        $participantToDowngrade = $this->participants[0];
        
        $response = $this->actingAs($this->creator)
            ->putJson("/api/conversations/{$this->groupConversation->id}/participants/{$participantToDowngrade->id}/admin", [
                'is_admin' => false,
            ]);

        $response->assertStatus(200);
            
        // التحقق من إزالة صلاحية المسؤول
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $this->groupConversation->id,
            'user_id' => $participantToDowngrade->id,
            'is_admin' => false,
        ]);
    }

    /**
     * اختبار تحديث معلومات المجموعة
     */
    public function test_admin_can_update_group_info()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        $updatedData = [
            'title' => 'Updated Group Title',
            'description' => 'Updated group description',
        ];
        
        $response = $this->actingAs($this->creator)
            ->putJson("/api/conversations/{$this->groupConversation->id}", $updatedData);

        $response->assertStatus(200);
            
        // التحقق من تحديث معلومات المجموعة
        $this->assertDatabaseHas('conversations', [
            'id' => $this->groupConversation->id,
            'title' => 'Updated Group Title',
            'description' => 'Updated group description',
        ]);
    }

    /**
     * اختبار مغادرة المجموعة
     */
    public function test_participant_can_leave_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك لمغادرة المجموعة
        $participantToLeave = $this->participants[1];
        
        $response = $this->actingAs($participantToLeave)
            ->deleteJson("/api/conversations/{$this->groupConversation->id}/leave");

        $response->assertStatus(200);
            
        // التحقق من مغادرة المشارك
        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $this->groupConversation->id,
            'user_id' => $participantToLeave->id,
        ]);
    }

    /**
     * اختبار إرسال رسالة إلى المجموعة
     */
    public function test_participant_can_send_message_to_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك لإرسال رسالة
        $sender = $this->participants[2];
        
        $messageData = [
            'type' => 'text',
            'message' => 'Hello everyone in the group!',
        ];
        
        $response = $this->actingAs($sender)
            ->postJson("/api/conversations/{$this->groupConversation->id}/messages", $messageData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'conversation_id',
                'sender_id',
                'type',
                'message',
                'created_at',
            ]);
            
        // التحقق من إنشاء الرسالة
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->groupConversation->id,
            'sender_id' => $sender->id,
            'type' => 'text',
            'message' => 'Hello everyone in the group!',
        ]);
    }

    /**
     * اختبار عرض قائمة المشاركين في المجموعة
     */
    public function test_participant_can_view_group_members()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        $response = $this->actingAs($this->participants[0])
            ->getJson("/api/conversations/{$this->groupConversation->id}/participants");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'conversation_id',
                        'is_admin',
                        'joined_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'profile_picture',
                        ],
                    ],
                ],
            ]);
            
        // التحقق من عدد المشاركين (المنشئ + 5 مشاركين)
        $this->assertEquals(6, count($response->json('data')));
    }

    /**
     * اختبار عدم قدرة غير المشاركين على إرسال رسائل إلى المجموعة
     */
    public function test_non_participant_cannot_send_message_to_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // إنشاء مستخدم غير مشارك
        $nonParticipant = User::factory()->create([
            'name' => 'Non Participant',
            'email' => 'non_participant@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $messageData = [
            'type' => 'text',
            'message' => 'Can I join your group?',
        ];
        
        $response = $this->actingAs($nonParticipant)
            ->postJson("/api/conversations/{$this->groupConversation->id}/messages", $messageData);

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم قدرة المشاركين العاديين على إضافة مشاركين جدد
     */
    public function test_regular_participant_cannot_add_new_members()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك عادي
        $regularParticipant = $this->participants[3];
        
        // إنشاء مستخدم جديد للإضافة
        $newUser = User::factory()->create([
            'name' => 'New User',
            'email' => 'new_user@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $response = $this->actingAs($regularParticipant)
            ->postJson("/api/conversations/{$this->groupConversation->id}/participants", [
                'participants' => [$newUser->id],
            ]);

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم قدرة المشاركين العاديين على إزالة مشاركين آخرين
     */
    public function test_regular_participant_cannot_remove_other_members()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك عادي
        $regularParticipant = $this->participants[3];
        
        // اختيار مشارك آخر لإزالته
        $otherParticipant = $this->participants[4];
        
        $response = $this->actingAs($regularParticipant)
            ->deleteJson("/api/conversations/{$this->groupConversation->id}/participants/{$otherParticipant->id}");

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم قدرة المشاركين العاديين على تحديث معلومات المجموعة
     */
    public function test_regular_participant_cannot_update_group_info()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك عادي
        $regularParticipant = $this->participants[3];
        
        $updatedData = [
            'title' => 'Unauthorized Update',
            'description' => 'This update should not be allowed',
        ];
        
        $response = $this->actingAs($regularParticipant)
            ->putJson("/api/conversations/{$this->groupConversation->id}", $updatedData);

        $response->assertStatus(403);
    }

    /**
     * اختبار حذف المجموعة
     */
    public function test_admin_can_delete_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        $response = $this->actingAs($this->creator)
            ->deleteJson("/api/conversations/{$this->groupConversation->id}");

        $response->assertStatus(200);
            
        // التحقق من حذف المجموعة
        $this->assertDatabaseMissing('conversations', [
            'id' => $this->groupConversation->id,
        ]);
        
        // التحقق من حذف المشاركين
        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $this->groupConversation->id,
        ]);
    }

    /**
     * اختبار عدم قدرة المشاركين العاديين على حذف المجموعة
     */
    public function test_regular_participant_cannot_delete_group()
    {
        // إنشاء محادثة جماعية أولاً
        $this->test_user_can_create_group_conversation();
        
        // اختيار مشارك عادي
        $regularParticipant = $this->participants[3];
        
        $response = $this->actingAs($regularParticipant)
            ->deleteJson("/api/conversations/{$this->groupConversation->id}");

        $response->assertStatus(403);
    }
}
