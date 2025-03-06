<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Country;
use App\Models\City;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchAndFilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $otherUsers = [];
    protected $conversations = [];

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
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
        
        $bahri = City::create([
            'name' => 'Bahri',
            'country_id' => $sudan->id,
        ]);
        
        // إنشاء المستخدم الرئيسي
        $this->user = User::factory()->create([
            'name' => 'Main User',
            'email' => 'main@example.com',
            'user_type' => 'user',
            'email_verified_at' => now(),
            'country_id' => $sudan->id,
            'city_id' => $khartoum->id,
        ]);
        
        // إنشاء مستخدمين آخرين
        for ($i = 0; $i < 10; $i++) {
            $city = [$khartoum->id, $omdurman->id, $bahri->id][rand(0, 2)];
            $name = $i < 5 ? "Test User {$i}" : "Sample User {$i}";
            
            $this->otherUsers[] = User::factory()->create([
                'name' => $name,
                'email' => "user{$i}@example.com",
                'user_type' => 'user',
                'email_verified_at' => now(),
                'country_id' => $sudan->id,
                'city_id' => $city,
            ]);
        }
        
        // إنشاء محادثات
        for ($i = 0; $i < 5; $i++) {
            $conversation = Conversation::create([
                'type' => 'individual',
                'created_by' => $this->user->id,
                'title' => $i < 3 ? "Important Conversation {$i}" : "Regular Conversation {$i}",
            ]);
            
            // إضافة المشاركين
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $this->user->id,
                'is_admin' => true,
                'joined_at' => now(),
            ]);
            
            ConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $this->otherUsers[$i]->id,
                'is_admin' => false,
                'joined_at' => now(),
            ]);
            
            // إنشاء رسائل
            for ($j = 0; $j < 5; $j++) {
                $sender = $j % 2 == 0 ? $this->user->id : $this->otherUsers[$i]->id;
                $messageContent = $j < 3 ? 
                    "This is a message about project planning" : 
                    "Just saying hello";
                
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender,
                    'type' => 'text',
                    'message' => $messageContent,
                    'created_at' => now()->subHours($j),
                ]);
            }
            
            $this->conversations[] = $conversation;
        }
        
        // إنشاء محادثة جماعية
        $groupConversation = Conversation::create([
            'type' => 'group',
            'created_by' => $this->user->id,
            'title' => 'Test Group',
            'description' => 'This is a test group for search',
        ]);
        
        // إضافة المشاركين
        ConversationParticipant::create([
            'conversation_id' => $groupConversation->id,
            'user_id' => $this->user->id,
            'is_admin' => true,
            'joined_at' => now(),
        ]);
        
        for ($i = 5; $i < 10; $i++) {
            ConversationParticipant::create([
                'conversation_id' => $groupConversation->id,
                'user_id' => $this->otherUsers[$i]->id,
                'is_admin' => false,
                'joined_at' => now(),
            ]);
        }
        
        // إنشاء رسائل في المجموعة
        for ($j = 0; $j < 10; $j++) {
            $sender = $j % 2 == 0 ? $this->user->id : $this->otherUsers[rand(5, 9)]->id;
            $messageContent = $j < 5 ? 
                "This is a group message about team coordination" : 
                "General discussion in the group";
            
            Message::create([
                'conversation_id' => $groupConversation->id,
                'sender_id' => $sender,
                'type' => 'text',
                'message' => $messageContent,
                'created_at' => now()->subHours($j),
            ]);
        }
        
        $this->conversations[] = $groupConversation;
    }

    /**
     * اختبار البحث عن مستخدمين حسب الاسم
     */
    public function test_search_users_by_name()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/users/search?query=Test');

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
                'meta' => [
                    'total',
                ],
            ]);
            
        $this->assertEquals(5, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن مستخدمين حسب المدينة
     */
    public function test_filter_users_by_city()
    {
        $khartoumCity = City::where('name', 'Khartoum')->first();
        
        $response = $this->actingAs($this->user)
            ->getJson("/api/users/search?city_id={$khartoumCity->id}");

        $response->assertStatus(200);
        
        // عدد المستخدمين في الخرطوم (بما فيهم المستخدم الرئيسي)
        $khartoumUsersCount = User::where('city_id', $khartoumCity->id)->count();
        $this->assertEquals($khartoumUsersCount, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن محادثات حسب العنوان
     */
    public function test_search_conversations_by_title()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/conversations/search?query=Important');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'title',
                        'last_message',
                    ],
                ],
                'meta' => [
                    'total',
                ],
            ]);
            
        $this->assertEquals(3, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن محادثات حسب النوع
     */
    public function test_filter_conversations_by_type()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/conversations/search?type=group');

        $response->assertStatus(200);
            
        $this->assertEquals(1, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن رسائل حسب المحتوى
     */
    public function test_search_messages_by_content()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/messages/search?query=project');

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
                    'total',
                ],
            ]);
            
        // عدد الرسائل التي تحتوي على كلمة "project"
        $this->assertEquals(15, $response->json('meta.total')); // 3 رسائل × 5 محادثات
    }

    /**
     * اختبار البحث عن رسائل في محادثة محددة
     */
    public function test_search_messages_in_specific_conversation()
    {
        $conversation = $this->conversations[0];
        
        $response = $this->actingAs($this->user)
            ->getJson("/api/conversations/{$conversation->id}/messages/search?query=planning");

        $response->assertStatus(200);
            
        // عدد الرسائل في المحادثة الأولى التي تحتوي على كلمة "planning"
        $this->assertEquals(3, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن رسائل حسب المرسل
     */
    public function test_filter_messages_by_sender()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/messages/search?sender_id={$this->user->id}");

        $response->assertStatus(200);
            
        // عدد الرسائل المرسلة من قبل المستخدم الرئيسي
        $messagesCount = Message::where('sender_id', $this->user->id)->count();
        $this->assertEquals($messagesCount, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن رسائل حسب التاريخ
     */
    public function test_filter_messages_by_date()
    {
        $today = now()->format('Y-m-d');
        
        $response = $this->actingAs($this->user)
            ->getJson("/api/messages/search?date={$today}");

        $response->assertStatus(200);
            
        // عدد الرسائل المرسلة اليوم
        $messagesCount = Message::whereDate('created_at', $today)->count();
        $this->assertEquals($messagesCount, $response->json('meta.total'));
    }

    /**
     * اختبار البحث المتقدم عن رسائل
     */
    public function test_advanced_message_search()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/messages/search?query=team&type=text&conversation_type=group");

        $response->assertStatus(200);
            
        // عدد الرسائل في المجموعات التي تحتوي على كلمة "team"
        $this->assertEquals(5, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن مستخدمين حسب البلد
     */
    public function test_filter_users_by_country()
    {
        $sudan = Country::where('name', 'Sudan')->first();
        
        $response = $this->actingAs($this->user)
            ->getJson("/api/users/search?country_id={$sudan->id}");

        $response->assertStatus(200);
            
        // عدد المستخدمين في السودان
        $sudanUsersCount = User::where('country_id', $sudan->id)->count();
        $this->assertEquals($sudanUsersCount, $response->json('meta.total'));
    }

    /**
     * اختبار البحث عن محادثات نشطة
     */
    public function test_filter_active_conversations()
    {
        // تحديث آخر نشاط لبعض المحادثات
        $this->conversations[0]->update(['updated_at' => now()]);
        $this->conversations[1]->update(['updated_at' => now()->subDays(1)]);
        $this->conversations[2]->update(['updated_at' => now()->subDays(2)]);
        
        $response = $this->actingAs($this->user)
            ->getJson('/api/conversations/search?active=true');

        $response->assertStatus(200);
            
        // يجب أن تظهر المحادثات النشطة فقط (آخر يومين)
        $this->assertEquals(3, $response->json('meta.total'));
    }

    /**
     * اختبار ترتيب المحادثات حسب آخر رسالة
     */
    public function test_sort_conversations_by_latest_message()
    {
        // إنشاء رسالة جديدة في المحادثة الثالثة
        Message::create([
            'conversation_id' => $this->conversations[2]->id,
            'sender_id' => $this->user->id,
            'type' => 'text',
            'message' => 'This is the most recent message',
            'created_at' => now(),
        ]);
        
        $response = $this->actingAs($this->user)
            ->getJson('/api/conversations?sort=latest_message');

        $response->assertStatus(200);
            
        // يجب أن تكون المحادثة الثالثة هي الأولى في القائمة
        $this->assertEquals($this->conversations[2]->id, $response->json('data.0.id'));
    }

    /**
     * اختبار البحث الشامل
     */
    public function test_global_search()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/search?query=test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    'data',
                    'total',
                ],
                'conversations' => [
                    'data',
                    'total',
                ],
                'messages' => [
                    'data',
                    'total',
                ],
            ]);
    }
}
