<?php

namespace App\Services\AI;

interface AIServiceInterface
{
    /**
     * البحث الذكي في الرسائل باستخدام معالجة اللغة الطبيعية
     *
     * @param string $query نص البحث
     * @param int $conversationId معرف المحادثة (اختياري)
     * @param array $options خيارات إضافية للبحث
     * @return array نتائج البحث
     */
    public function searchMessages(string $query, ?int $conversationId = null, array $options = []): array;

    /**
     * ترجمة نص إلى لغة أخرى
     *
     * @param string $text النص المراد ترجمته
     * @param string $targetLanguage اللغة المستهدفة
     * @param string|null $sourceLanguage اللغة المصدر (اختياري، يمكن اكتشافها تلقائيًا)
     * @return string النص المترجم
     */
    public function translateText(string $text, string $targetLanguage, ?string $sourceLanguage = null): string;

    /**
     * معالجة استفسار المستخدم وإنشاء رد من روبوت المحادثة
     *
     * @param string $query استفسار المستخدم
     * @param int|null $conversationId معرف المحادثة للسياق
     * @param array $context معلومات سياقية إضافية
     * @return string رد روبوت المحادثة
     */
    public function generateChatbotResponse(string $query, ?int $conversationId = null, array $context = []): string;

    /**
     * اقتراح ردود ذكية بناءً على سياق المحادثة
     *
     * @param int $conversationId معرف المحادثة
     * @param int $messageId معرف آخر رسالة (اختياري)
     * @param int $count عدد الاقتراحات المطلوبة
     * @return array قائمة باقتراحات الردود
     */
    public function suggestReplies(int $conversationId, ?int $messageId = null, int $count = 3): array;

    /**
     * تلخيص محادثة أو جزء منها
     *
     * @param int $conversationId معرف المحادثة
     * @param array $options خيارات التلخيص (مثل عدد الرسائل، الفترة الزمنية)
     * @return string ملخص المحادثة
     */
    public function summarizeConversation(int $conversationId, array $options = []): string;

    /**
     * فحص محتوى للكشف عن المحتوى غير اللائق
     *
     * @param string $content المحتوى المراد فحصه
     * @param string $contentType نوع المحتوى (نص، صورة، صوت)
     * @param array $options خيارات إضافية للفحص
     * @return array نتائج الفحص مع التصنيفات والثقة
     */
    public function moderateContent(string $content, string $contentType = 'text', array $options = []): array;

    /**
     * تحويل تسجيل صوتي إلى نص
     *
     * @param string $audioPath مسار ملف الصوت
     * @param string $language لغة التسجيل (اختياري)
     * @param array $options خيارات إضافية
     * @return array النص المستخرج مع البيانات الوصفية
     */
    public function transcribeAudio(string $audioPath, ?string $language = null, array $options = []): array;

    /**
     * التحقق من هوية المستخدم باستخدام الذكاء الاصطناعي
     *
     * @param array $documentData بيانات الوثيقة (مسارات الصور، البيانات المدخلة)
     * @param string $verificationType نوع التحقق (هوية، جواز سفر، إلخ)
     * @return array نتائج التحقق مع درجة الثقة
     */
    public function verifyIdentity(array $documentData, string $verificationType): array;

    /**
     * كشف محاولات الاختراق أو النشاط المشبوه
     *
     * @param array $activityData بيانات النشاط للتحليل
     * @return array نتائج التحليل مع مستوى التهديد والتوصيات
     */
    public function detectSuspiciousActivity(array $activityData): array;
}
