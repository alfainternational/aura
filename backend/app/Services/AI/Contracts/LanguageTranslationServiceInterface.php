<?php

namespace App\Services\AI\Contracts;

interface LanguageTranslationServiceInterface
{
    /**
     * ترجمة نص من لغة إلى أخرى
     * 
     * @param string $text النص المراد ترجمته
     * @param string $targetLanguage اللغة المستهدفة
     * @param string $sourceLanguage اللغة المصدر (اختياري، يمكن اكتشافها تلقائيًا)
     * @return string النص المترجم
     */
    public function translateText(string $text, string $targetLanguage, string $sourceLanguage = null): string;
    
    /**
     * اكتشاف لغة النص
     * 
     * @param string $text النص المراد اكتشاف لغته
     * @return string رمز اللغة المكتشفة
     */
    public function detectLanguage(string $text): string;
    
    /**
     * الحصول على قائمة اللغات المدعومة
     * 
     * @return array قائمة اللغات المدعومة
     */
    public function getSupportedLanguages(): array;
}
