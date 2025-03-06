<?php

namespace App\Services;

use App\Models\User;
use App\Models\VoiceCall;
use App\Models\VoiceCallParticipant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoiceCallService
{
    /**
     * بدء مكالمة صوتية جديدة
     *
     * @param User $caller المتصل
     * @param array $participantIds معرفات المشاركين
     * @param bool $isGroup هل هي مكالمة جماعية
     * @return VoiceCall
     */
    public function startCall(User $caller, array $participantIds, bool $isGroup = false)
    {
        // التأكد من أن المتصل موجود في قائمة المشاركين
        if (!in_array($caller->id, $participantIds)) {
            $participantIds[] = $caller->id;
        }
        
        DB::beginTransaction();
        
        try {
            // إنشاء المكالمة
            $voiceCall = new VoiceCall();
            $voiceCall->caller_id = $caller->id;
            $voiceCall->is_group = $isGroup;
            $voiceCall->status = 'ringing';
            $voiceCall->started_at = now();
            $voiceCall->uuid = Str::uuid();
            $voiceCall->save();
            
            // إضافة المشاركين
            foreach ($participantIds as $userId) {
                $participant = new VoiceCallParticipant();
                $participant->voice_call_id = $voiceCall->id;
                $participant->user_id = $userId;
                $participant->status = $userId === $caller->id ? 'connected' : 'ringing';
                $participant->joined_at = $userId === $caller->id ? now() : null;
                $participant->save();
            }
            
            DB::commit();
            
            return $voiceCall;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * الانضمام إلى مكالمة صوتية
     *
     * @param VoiceCall $voiceCall المكالمة
     * @param User $user المستخدم
     * @return bool
     */
    public function joinCall(VoiceCall $voiceCall, User $user)
    {
        // التحقق من أن المكالمة نشطة
        if ($voiceCall->status !== 'ringing' && $voiceCall->status !== 'ongoing') {
            throw new \Exception('المكالمة غير نشطة');
        }
        
        // التحقق من أن المستخدم مشارك في المكالمة
        $participant = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$participant) {
            throw new \Exception('المستخدم ليس مشاركًا في هذه المكالمة');
        }
        
        // تحديث حالة المشارك
        $participant->status = 'connected';
        $participant->joined_at = now();
        $participant->save();
        
        // إذا كانت المكالمة في حالة رنين، قم بتحديثها إلى جارية
        if ($voiceCall->status === 'ringing') {
            $voiceCall->status = 'ongoing';
            $voiceCall->save();
        }
        
        return true;
    }
    
    /**
     * رفض مكالمة صوتية
     *
     * @param VoiceCall $voiceCall المكالمة
     * @param User $user المستخدم
     * @return bool
     */
    public function rejectCall(VoiceCall $voiceCall, User $user)
    {
        // التحقق من أن المكالمة في حالة رنين
        if ($voiceCall->status !== 'ringing') {
            throw new \Exception('لا يمكن رفض المكالمة في هذه الحالة');
        }
        
        // التحقق من أن المستخدم مشارك في المكالمة
        $participant = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$participant) {
            throw new \Exception('المستخدم ليس مشاركًا في هذه المكالمة');
        }
        
        // تحديث حالة المشارك
        $participant->status = 'rejected';
        $participant->save();
        
        // التحقق مما إذا كان جميع المشاركين قد رفضوا المكالمة
        $allRejected = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
            ->where('user_id', '!=', $voiceCall->caller_id)
            ->where('status', '!=', 'rejected')
            ->count() === 0;
            
        if ($allRejected) {
            // إنهاء المكالمة
            $voiceCall->status = 'rejected';
            $voiceCall->ended_at = now();
            $voiceCall->save();
        }
        
        return true;
    }
    
    /**
     * إنهاء مكالمة صوتية
     *
     * @param VoiceCall $voiceCall المكالمة
     * @param User $user المستخدم
     * @return bool
     */
    public function endCall(VoiceCall $voiceCall, User $user)
    {
        // التحقق من أن المكالمة نشطة
        if ($voiceCall->status !== 'ringing' && $voiceCall->status !== 'ongoing') {
            throw new \Exception('المكالمة غير نشطة');
        }
        
        // التحقق من أن المستخدم مشارك في المكالمة
        $participant = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$participant) {
            throw new \Exception('المستخدم ليس مشاركًا في هذه المكالمة');
        }
        
        // إذا كان المستخدم هو المتصل أو إذا كانت مكالمة جماعية وكان المستخدم هو آخر مشارك متصل
        $isLastConnected = false;
        
        if ($voiceCall->is_group) {
            $connectedCount = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
                ->where('status', 'connected')
                ->count();
                
            $isLastConnected = $connectedCount <= 1;
        }
        
        if ($user->id === $voiceCall->caller_id || $isLastConnected) {
            // إنهاء المكالمة للجميع
            $voiceCall->status = 'ended';
            $voiceCall->ended_at = now();
            $voiceCall->save();
            
            // تحديث حالة جميع المشاركين
            VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
                ->where('status', 'connected')
                ->update(['status' => 'ended', 'left_at' => now()]);
        } else {
            // المستخدم يغادر المكالمة فقط
            $participant->status = 'left';
            $participant->left_at = now();
            $participant->save();
        }
        
        return true;
    }
    
    /**
     * كتم/إلغاء كتم الصوت في مكالمة
     *
     * @param VoiceCall $voiceCall المكالمة
     * @param User $user المستخدم
     * @param bool $mute كتم أم إلغاء كتم
     * @return bool
     */
    public function toggleMute(VoiceCall $voiceCall, User $user, bool $mute = true)
    {
        // التحقق من أن المكالمة جارية
        if ($voiceCall->status !== 'ongoing') {
            throw new \Exception('المكالمة غير جارية');
        }
        
        // التحقق من أن المستخدم مشارك في المكالمة
        $participant = VoiceCallParticipant::where('voice_call_id', $voiceCall->id)
            ->where('user_id', $user->id)
            ->where('status', 'connected')
            ->first();
            
        if (!$participant) {
            throw new \Exception('المستخدم ليس مشاركًا نشطًا في هذه المكالمة');
        }
        
        // تحديث حالة الكتم
        $participant->is_muted = $mute;
        $participant->save();
        
        return true;
    }
}
