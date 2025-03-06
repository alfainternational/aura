<?php

namespace App\Repositories\AI;

use App\Models\AI\AIConversation;
use Illuminate\Support\Facades\DB;

class AIConversationRepository
{
    /**
     * حفظ محادثة ذكية جديدة
     * 
     * @param array $conversationData بيانات المحادثة
     * @return AIConversation نموذج المحادثة المحفوظة
     */
    public function create(array $conversationData): AIConversation
    {
        return AIConversation::create($conversationData);
    }

    /**
     * البحث عن محادثة ذكية حسب المعرف
     * 
     * @param int $conversationId معرف المحادثة
     * @return AIConversation|null المحادثة
     */
    public function findById(int $conversationId)
    {
        return AIConversation::findOrFail($conversationId);
    }

    /**
     * استرجاع المحادثات الذكية للمستخدم
     * 
     * @param int $userId معرف المستخدم
     * @param array $filters فلاتر إضافية
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserConversations(int $userId, array $filters = [])
    {
        $query = AIConversation::where('user_id', $userId);

        // تطبيق الفلاتر الإضافية
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * تحليل إحصائيات المحادثات الذكية
     * 
     * @param int $userId معرف المستخدم
     * @return array إحصائيات المحادثات
     */
    public function getConversationAnalytics(int $userId): array
    {
        return [
            'total_conversations' => AIConversation::where('user_id', $userId)->count(),
            'conversations_by_type' => AIConversation::where('user_id', $userId)
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'average_conversation_duration' => AIConversation::where('user_id', $userId)->avg('duration'),
        ];
    }
}
