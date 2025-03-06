<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $creator;
    protected $participant1;
    protected $participant2;
    protected $nonParticipant;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدمين
        $this->creator = User::factory()->create([
            'name' => 'Group Creator',
            'email' => 'creator@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->participant1 = User::factory()->create([
            'name' => 'Participant 1',
            'email' => 'participant1@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->participant2 = User::factory()->create([
            'name' => 'Participant 2',
            'email' => 'participant2@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->nonParticipant = User::factory()->create([
            'name' => 'Non Participant',
            'email' => 'nonparticipant@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * اختبار إنشاء مجموعة جديدة
     */
    public function test_create_group()
    {
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $groupData = [
            'title' => 'Test Group',
            'type' => 'group',
            'participants' => [$this->participant1->id, $this->participant2->id],
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/conversations', $groupData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'type',
                'created_by',
                'created_at',
            ]);
        
        $groupId = $response->json('id');
        
        // التحقق من إنشاء المجموعة
        $this->assertDatabaseHas('conversations', [
            'id' => $groupId,
            'title' => 'Test Group',
            'type' => 'group',
            'created_by' => $this->creator->id,
        ]);
        
        // التحقق من إضافة المشاركين
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $groupId,
            'user_id' => $this->creator->id,
            'is_admin' => true,
        ]);
        
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $groupId,
            'user_id' => $this->participant1->id,
            'is_admin' => false,
        ]);
        
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $groupId,
            'user_id' => $this->participant2->id,
            'is_admin' => false,
        ]);
    }

    /**
     * اختبار جلب معلومات المجموعة
     */
    public function test_get_group_info()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/conversations/' . $group->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'type',
                'created_by',
                'created_at',
                'participants' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'is_admin',
                        'joined_at',
                    ],
                ],
            ]);
    }

    /**
     * اختبار تحديث معلومات المجموعة
     */
    public function test_update_group_info()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $updateData = [
            'title' => 'Updated Group Title',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/conversations/' . $group->id, $updateData);

        $response->assertStatus(200);
        
        // التحقق من تحديث المجموعة
        $this->assertDatabaseHas('conversations', [
            'id' => $group->id,
            'title' => 'Updated Group Title',
        ]);
    }

    /**
     * اختبار إضافة مشاركين جدد للمجموعة
     */
    public function test_add_participants_to_group()
    {
        // إنشاء مجموعة بدون المشارك الثاني
        $group = $this->createGroup([$this->participant1->id]);
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $participantData = [
            'user_id' => $this->participant2->id,
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/conversations/' . $group->id . '/participants', $participantData);

        $response->assertStatus(200);
        
        // التحقق من إضافة المشارك
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $group->id,
            'user_id' => $this->participant2->id,
            'is_admin' => false,
        ]);
    }

    /**
     * اختبار إزالة مشارك من المجموعة
     */
    public function test_remove_participant_from_group()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/conversations/' . $group->id . '/participants/' . $this->participant1->id);

        $response->assertStatus(200);
        
        // التحقق من إزالة المشارك
        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $group->id,
            'user_id' => $this->participant1->id,
        ]);
    }

    /**
     * اختبار مغادرة المجموعة
     */
    public function test_leave_group()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->participant1->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/conversations/' . $group->id . '/leave');

        $response->assertStatus(200);
        
        // التحقق من مغادرة المشارك
        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $group->id,
            'user_id' => $this->participant1->id,
        ]);
    }

    /**
     * اختبار ترقية مشارك إلى مسؤول
     */
    public function test_promote_participant_to_admin()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $adminData = [
            'is_admin' => true,
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/conversations/' . $group->id . '/participants/' . $this->participant1->id, $adminData);

        $response->assertStatus(200);
        
        // التحقق من ترقية المشارك
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $group->id,
            'user_id' => $this->participant1->id,
            'is_admin' => true,
        ]);
    }

    /**
     * اختبار إرسال رسالة في المجموعة
     */
    public function test_send_message_to_group()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $messageData = [
            'type' => 'text',
            'message' => 'Hello, this is a group message',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/conversations/' . $group->id . '/messages', $messageData);

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
            'conversation_id' => $group->id,
            'sender_id' => $this->creator->id,
            'type' => 'text',
            'message' => 'Hello, this is a group message',
        ]);
    }

    /**
     * اختبار حذف المجموعة
     */
    public function test_delete_group()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/conversations/' . $group->id);

        $response->assertStatus(200);
        
        // التحقق من حذف المجموعة
        $this->assertDatabaseMissing('conversations', [
            'id' => $group->id,
        ]);
        
        // التحقق من حذف المشاركين
        $this->assertDatabaseMissing('conversation_participants', [
            'conversation_id' => $group->id,
        ]);
    }

    /**
     * اختبار عدم السماح لغير المسؤول بتحديث المجموعة
     */
    public function test_non_admin_cannot_update_group()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->participant1->createToken('auth_token')->plainTextToken;
        
        $updateData = [
            'title' => 'Updated by Non-Admin',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/conversations/' . $group->id, $updateData);

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم السماح لغير المسؤول بإضافة مشاركين
     */
    public function test_non_admin_cannot_add_participants()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->participant1->createToken('auth_token')->plainTextToken;
        
        $participantData = [
            'user_id' => $this->nonParticipant->id,
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/conversations/' . $group->id . '/participants', $participantData);

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم السماح لغير المسؤول بإزالة مشاركين
     */
    public function test_non_admin_cannot_remove_participants()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->participant1->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/conversations/' . $group->id . '/participants/' . $this->participant2->id);

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم السماح لغير المشاركين بالوصول إلى المجموعة
     */
    public function test_non_participant_cannot_access_group()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->nonParticipant->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/conversations/' . $group->id);

        $response->assertStatus(403);
    }

    /**
     * اختبار عدم السماح لغير المشاركين بإرسال رسائل
     */
    public function test_non_participant_cannot_send_messages()
    {
        // إنشاء مجموعة
        $group = $this->createGroup();
        
        $token = $this->nonParticipant->createToken('auth_token')->plainTextToken;
        
        $messageData = [
            'type' => 'text',
            'message' => 'Hello from non-participant',
        ];
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/conversations/' . $group->id . '/messages', $messageData);

        $response->assertStatus(403);
    }

    /**
     * اختبار جلب قائمة المجموعات
     */
    public function test_get_user_groups()
    {
        // إنشاء عدة مجموعات
        $this->createGroup([$this->participant1->id, $this->participant2->id], 'Group 1');
        $this->createGroup([$this->participant1->id], 'Group 2');
        $this->createGroup([$this->participant2->id], 'Group 3');
        
        $token = $this->creator->createToken('auth_token')->plainTextToken;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/conversations?type=group');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /**
     * إنشاء مجموعة للاختبار
     */
    private function createGroup($participants = null, $title = 'Test Group')
    {
        if ($participants === null) {
            $participants = [$this->participant1->id, $this->participant2->id];
        }
        
        // إنشاء المجموعة
        $group = Conversation::create([
            'title' => $title,
            'type' => 'group',
            'created_by' => $this->creator->id,
        ]);
        
        // إضافة المنشئ كمسؤول
        ConversationParticipant::create([
            'conversation_id' => $group->id,
            'user_id' => $this->creator->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        // إضافة المشاركين
        foreach ($participants as $participantId) {
            ConversationParticipant::create([
                'conversation_id' => $group->id,
                'user_id' => $participantId,
                'is_admin' => false,
                'joined_at' => now(),
            ]);
        }
        
        return $group;
    }
}
