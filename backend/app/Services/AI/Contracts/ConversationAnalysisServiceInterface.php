<?php

namespace App\Services\AI\Contracts;

interface ConversationAnalysisServiceInterface
{
    /**
     * تحليل سياق المحادثة واستخراج المعلومات الرئيسية
     * 
     * @param array $conversationMessages رسائل المحادثة
     * @return array المعلومات والرؤى المستخلصة
     */
    public function analyzeConversationContext(array $conversationMessages): array;

    /**
     * استخراج الكيانات والمفاهيم الرئيسية من المحادثة
     * 
     * @param array $conversationMessages رسائل المحادثة
     * @return array الكيانات والمفاهيم المستخرجة
     */
    public function extractEntities(array $conversationMessages): array;

    /**
     * تحليل المشاعر والنبرة في المحادثة
     * 
     * @param array $conversationMessages رسائل المحادثة
     * @return array تحليل المشاعر والنبرة
     */
    public function analyzeSentiment(array $conversationMessages): array;
}
