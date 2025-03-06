<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VoiceCall;
use App\Models\VoiceCallParticipant;
use App\Events\VoiceCallEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VoiceCallTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $caller;
    protected $receiver;
    protected $voiceCall;

    /**
     * إعداد بيئة الاختبار
     */
    public function setUp(): void
    {
        parent::setUp();
        
        // تعطيل الأحداث خلال الاختبارات
        Event::fake([
            VoiceCallEvent::class,
        ]);
        
        // إنشاء مستخدمين للاختبار
        $this->caller = User::factory()->create([
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
        
        $this->receiver = User::factory()->create([
            'user_type' => 'user',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * اختبار بدء مكالمة صوتية
     */
    public function test_user_can_start_voice_call()
    {
        $response = $this->actingAs($this->caller)
            ->postJson('/api/voice-calls', [
                'participants' => [$this->receiver->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'participants',
                    'started_at',
                ],
            ]);
            
        $this->assertDatabaseHas('voice_calls', [
            'initiated_by' => $this->caller->id,
            'status' => 'ringing',
        ]);
        
        $callId = $response->json('data.id');
        
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $callId,
            'user_id' => $this->caller->id,
            'status' => 'joined',
        ]);
        
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $callId,
            'user_id' => $this->receiver->id,
            'status' => 'invited',
        ]);
        
        Event::assertDispatched(VoiceCallEvent::class);
    }

    /**
     * اختبار قبول مكالمة صوتية
     */
    public function test_user_can_accept_voice_call()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'initiated_by' => $this->caller->id,
            'status' => 'ringing',
            'started_at' => now(),
        ]);
        
        // إضافة المشاركين
        $voiceCall->participants()->createMany([
            [
                'user_id' => $this->caller->id,
                'status' => 'joined',
            ],
            [
                'user_id' => $this->receiver->id,
                'status' => 'invited',
            ],
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->postJson("/api/voice-calls/{$voiceCall->id}/accept");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'participants',
                ],
            ]);
            
        $this->assertDatabaseHas('voice_calls', [
            'id' => $voiceCall->id,
            'status' => 'active',
        ]);
        
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $voiceCall->id,
            'user_id' => $this->receiver->id,
            'status' => 'joined',
        ]);
        
        Event::assertDispatched(VoiceCallEvent::class);
    }

    /**
     * اختبار رفض مكالمة صوتية
     */
    public function test_user_can_decline_voice_call()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'initiated_by' => $this->caller->id,
            'status' => 'ringing',
            'started_at' => now(),
        ]);
        
        // إضافة المشاركين
        $voiceCall->participants()->createMany([
            [
                'user_id' => $this->caller->id,
                'status' => 'joined',
            ],
            [
                'user_id' => $this->receiver->id,
                'status' => 'invited',
            ],
        ]);
        
        $response = $this->actingAs($this->receiver)
            ->postJson("/api/voice-calls/{$voiceCall->id}/decline");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $voiceCall->id,
            'user_id' => $this->receiver->id,
            'status' => 'declined',
        ]);
        
        // التحقق من حالة المكالمة إذا كان هناك مشارك واحد فقط
        $this->assertDatabaseHas('voice_calls', [
            'id' => $voiceCall->id,
            'status' => 'ended',
        ]);
        
        Event::assertDispatched(VoiceCallEvent::class);
    }

    /**
     * اختبار إنهاء مكالمة صوتية
     */
    public function test_user_can_end_voice_call()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'initiated_by' => $this->caller->id,
            'status' => 'active',
            'started_at' => now()->subMinutes(5),
        ]);
        
        // إضافة المشاركين
        $voiceCall->participants()->createMany([
            [
                'user_id' => $this->caller->id,
                'status' => 'joined',
            ],
            [
                'user_id' => $this->receiver->id,
                'status' => 'joined',
            ],
        ]);
        
        $response = $this->actingAs($this->caller)
            ->postJson("/api/voice-calls/{$voiceCall->id}/end");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('voice_calls', [
            'id' => $voiceCall->id,
            'status' => 'ended',
            'ended_at' => now(),
        ]);
        
        $this->assertDatabaseHas('voice_call_participants', [
            'voice_call_id' => $voiceCall->id,
            'user_id' => $this->caller->id,
            'status' => 'left',
        ]);
        
        Event::assertDispatched(VoiceCallEvent::class);
    }

    /**
     * اختبار كتم صوت المشارك
     */
    public function test_user_can_mute_themselves()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'initiated_by' => $this->caller->id,
            'status' => 'active',
            'started_at' => now()->subMinutes(5),
        ]);
        
        // إضافة المشاركين
        $participant = $voiceCall->participants()->create([
            'user_id' => $this->caller->id,
            'status' => 'joined',
            'is_muted' => false,
        ]);
        
        $response = $this->actingAs($this->caller)
            ->postJson("/api/voice-calls/{$voiceCall->id}/mute");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('voice_call_participants', [
            'id' => $participant->id,
            'is_muted' => true,
        ]);
        
        Event::assertDispatched(VoiceCallEvent::class);
    }

    /**
     * اختبار إلغاء كتم صوت المشارك
     */
    public function test_user_can_unmute_themselves()
    {
        // إنشاء مكالمة صوتية
        $voiceCall = VoiceCall::create([
            'initiated_by' => $this->caller->id,
            'status' => 'active',
            'started_at' => now()->subMinutes(5),
        ]);
        
        // إضافة المشاركين
        $participant = $voiceCall->participants()->create([
            'user_id' => $this->caller->id,
            'status' => 'joined',
            'is_muted' => true,
        ]);
        
        $response = $this->actingAs($this->caller)
            ->postJson("/api/voice-calls/{$voiceCall->id}/unmute");

        $response->assertStatus(200);
            
        $this->assertDatabaseHas('voice_call_participants', [
            'id' => $participant->id,
            'is_muted' => false,
        ]);
        
        Event::assertDispatched(VoiceCallEvent::class);
    }
}
