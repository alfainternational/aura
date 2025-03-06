<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ConversationService
{
    /**
     * إنشاء محادثة جديدة
     *
     * @param User $creator منشئ المحادثة
     * @param array $participantIds معرفات المشاركين
     * @param string|null $title عنوان المحادثة (للمجموعات)
     * @param string|null $avatar صورة المحادثة (للمجموعات)
     * @param bool $isGroup هل هي محادثة جماعية
     * @return Conversation
     */
    public function createConversation(User $creator, array $participantIds, ?string $title = null, ?string $avatar = null, bool $isGroup = false)
    {
        // التأكد من أن منشئ المحادثة موجود في قائمة المشاركين
        if (!in_array($creator->id, $participantIds)) {
            $participantIds[] = $creator->id;
        }
        
        // التحقق من وجود محادثة فردية بين نفس المستخدمين
        if (!$isGroup && count($participantIds) === 2) {
            $existingConversation = $this->findDirectConversation($participantIds[0], $participantIds[1]);
            if ($existingConversation) {
                return $existingConversation;
            }
        }
        
        DB::beginTransaction();
        
        try {
            // إنشاء المحادثة
            $conversation = new Conversation();
            $conversation->creator_id = $creator->id;
            $conversation->title = $title;
            $conversation->avatar = $avatar;
            $conversation->is_group = $isGroup;
            $conversation->uuid = Str::uuid();
            $conversation->save();
            
            // إضافة المشاركين
            foreach ($participantIds as $userId) {
                $participant = new ConversationParticipant();
                $participant->conversation_id = $conversation->id;
                $participant->user_id = $userId;
                $participant->role = $userId === $creator->id ? 'admin' : 'member';
                $participant->save();
            }
            
            DB::commit();
            
            return $conversation;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * البحث عن محادثة مباشرة بين مستخدمين
     *
     * @param int $userId1 معرف المستخدم الأول
     * @param int $userId2 معرف المستخدم الثاني
     * @return Conversation|null
     */
    public function findDirectConversation(int $userId1, int $userId2)
    {
        // الحصول على محادثات المستخدم الأول
        $user1Conversations = ConversationParticipant::where('user_id', $userId1)
            ->pluck('conversation_id');
            
        // البحث عن محادثة مشتركة مع المستخدم الثاني
        $commonConversation = ConversationParticipant::whereIn('conversation_id', $user1Conversations)
            ->where('user_id', $userId2)
            ->join('conversations', 'conversation_participants.conversation_id', '=', 'conversations.id')
            ->where('conversations.is_group', false)
            ->select('conversations.*')
            ->first();
            
        return $commonConversation;
    }
    
    /**
     * إضافة مشاركين إلى محادثة
     *
     * @param Conversation $conversation المحادثة
     * @param array $userIds معرفات المستخدمين
     * @param User $addedBy المستخدم الذي أضاف المشاركين
     * @return bool
     */
    public function addParticipants(Conversation $conversation, array $userIds, User $addedBy)
    {
        // التحقق من أن المحادثة جماعية
        if (!$conversation->is_group) {
            throw new \Exception('لا يمكن إضافة مشاركين إلى محادثة فردية');
        }
        
        // التحقق من أن المستخدم الذي يضيف المشاركين هو مشرف في المحادثة
        $addedByParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $addedBy->id)
            ->first();
            
        if (!$addedByParticipant || $addedByParticipant->role !== 'admin') {
            throw new \Exception('ليس لديك صلاحية إضافة مشاركين إلى هذه المحادثة');
        }
        
        // الحصول على المشاركين الحاليين
        $existingParticipantIds = ConversationParticipant::where('conversation_id', $conversation->id)
            ->pluck('user_id')
            ->toArray();
            
        // إضافة المشاركين الجدد فقط
        $newParticipants = array_diff($userIds, $existingParticipantIds);
        
        DB::beginTransaction();
        
        try {
            foreach ($newParticipants as $userId) {
                $participant = new ConversationParticipant();
                $participant->conversation_id = $conversation->id;
                $participant->user_id = $userId;
                $participant->role = 'member';
                $participant->save();
            }
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * إزالة مشارك من محادثة
     *
     * @param Conversation $conversation المحادثة
     * @param int $userId معرف المستخدم
     * @param User $removedBy المستخدم الذي قام بالإزالة
     * @return bool
     */
    public function removeParticipant(Conversation $conversation, int $userId, User $removedBy)
    {
        // التحقق من أن المحادثة جماعية
        if (!$conversation->is_group) {
            throw new \Exception('لا يمكن إزالة مشاركين من محادثة فردية');
        }
        
        // التحقق من أن المستخدم الذي يزيل المشاركين هو مشرف في المحادثة أو يزيل نفسه
        $removedByParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $removedBy->id)
            ->first();
            
        if (!$removedByParticipant) {
            throw new \Exception('أنت لست مشاركًا في هذه المحادثة');
        }
        
        // إذا كان المستخدم يزيل نفسه، فهذا مسموح
        // وإلا، يجب أن يكون مشرفًا
        if ($userId !== $removedBy->id && $removedByParticipant->role !== 'admin') {
            throw new \Exception('ليس لديك صلاحية إزالة مشاركين من هذه المحادثة');
        }
        
        // إزالة المشارك
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$participant) {
            throw new \Exception('المستخدم ليس مشاركًا في هذه المحادثة');
        }
        
        // لا يمكن إزالة منشئ المحادثة
        if ($conversation->creator_id === $userId) {
            throw new \Exception('لا يمكن إزالة منشئ المحادثة');
        }
        
        $participant->delete();
        
        return true;
    }
    
    /**
     * تحديث معلومات المحادثة
     *
     * @param Conversation $conversation المحادثة
     * @param array $data البيانات المراد تحديثها
     * @param User $updatedBy المستخدم الذي قام بالتحديث
     * @return Conversation
     */
    public function updateConversation(Conversation $conversation, array $data, User $updatedBy)
    {
        // التحقق من أن المحادثة جماعية
        if (!$conversation->is_group) {
            throw new \Exception('لا يمكن تحديث معلومات محادثة فردية');
        }
        
        // التحقق من أن المستخدم الذي يحدث المحادثة هو مشرف فيها
        $updatedByParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $updatedBy->id)
            ->first();
            
        if (!$updatedByParticipant || $updatedByParticipant->role !== 'admin') {
            throw new \Exception('ليس لديك صلاحية تحديث معلومات هذه المحادثة');
        }
        
        // تحديث البيانات
        if (isset($data['title'])) {
            $conversation->title = $data['title'];
        }
        
        if (isset($data['avatar'])) {
            // إذا كانت هناك صورة قديمة، قم بحذفها
            if ($conversation->avatar) {
                \Storage::disk('public')->delete($conversation->avatar);
            }
            
            $conversation->avatar = $data['avatar'];
        }
        
        $conversation->save();
        
        return $conversation;
    }
    
    /**
     * تغيير دور مشارك في محادثة
     *
     * @param Conversation $conversation المحادثة
     * @param int $userId معرف المستخدم
     * @param string $role الدور الجديد (admin, member)
     * @param User $changedBy المستخدم الذي قام بالتغيير
     * @return bool
     */
    public function changeParticipantRole(Conversation $conversation, int $userId, string $role, User $changedBy)
    {
        // التحقق من أن المحادثة جماعية
        if (!$conversation->is_group) {
            throw new \Exception('لا يمكن تغيير أدوار المشاركين في محادثة فردية');
        }
        
        // التحقق من أن الدور صالح
        if (!in_array($role, ['admin', 'member'])) {
            throw new \Exception('الدور غير صالح');
        }
        
        // التحقق من أن المستخدم الذي يغير الدور هو مشرف في المحادثة
        $changedByParticipant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $changedBy->id)
            ->first();
            
        if (!$changedByParticipant || $changedByParticipant->role !== 'admin') {
            throw new \Exception('ليس لديك صلاحية تغيير أدوار المشاركين في هذه المحادثة');
        }
        
        // تغيير دور المشارك
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->first();
            
        if (!$participant) {
            throw new \Exception('المستخدم ليس مشاركًا في هذه المحادثة');
        }
        
        $participant->role = $role;
        $participant->save();
        
        return true;
    }
}
