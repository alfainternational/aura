<?php

namespace App\Services\AI\Contracts;

interface ChatbotServiceInterface
{
    /**
     * معالجة استفسار المستخدم وإنتاج رد ذكي
     * 
     * @param string $query نص الاستعلام
     * @param array $context السياق الإضافي للمحادثة
     * @return string الرد الذكي
     */
    public function processQuery(string $query, array $context = []): string;

    /**
     * توليد اقتراحات للردود بناءً على سياق المحادثة
     * 
     * @param array $conversationContext سياق المحادثة
     * @param int $suggestionsCount عدد الاقتراحات المطلوبة
     * @return array قائمة اقتراحات الردود
     */
    public function generateReplySuggestions(array $conversationContext, int $suggestionsCount = 3): array;

    /**
     * تلخيص محادثة
     * 
     * @param array $conversationMessages رسائل المحادثة
     * @return string ملخص المحادثة
     */
    public function summarizeConversation(array $conversationMessages): string;
}
