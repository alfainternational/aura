<?php

namespace App\Services\AI\Contracts;

interface ReplyGeneratorServiceInterface
{
    /**
     * توليد اقتراحات ردود ذكية
     * 
     * @param array $conversationContext سياق المحادثة
     * @param int $suggestionsCount عدد الاقتراحات المطلوبة
     * @return array اقتراحات الردود
     */
    public function generateSmartReplySuggestions(array $conversationContext, int $suggestionsCount = 3): array;

    /**
     * توليد رد تلقائي بناءً على سياق المحادثة
     * 
     * @param array $conversationContext سياق المحادثة
     * @return string الرد التلقائي
     */
    public function generateAutomaticReply(array $conversationContext): string;

    /**
     * تخصيص اقتراحات الردود حسب المستخدم
     * 
     * @param array $conversationContext سياق المحادثة
     * @param array $userPreferences تفضيلات المستخدم
     * @return array اقتراحات الردود المخصصة
     */
    public function personalizeReplySuggestions(array $conversationContext, array $userPreferences): array;
}
