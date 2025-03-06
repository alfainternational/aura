<?php

namespace App\Helpers;

use App\Models\Country;

class CurrencyHelper
{
    /**
     * تنسيق المبلغ بعملة معينة
     *
     * @param float $amount المبلغ المراد تنسيقه
     * @param string|null $currency العملة المستخدمة (إذا لم يتم تحديدها، سيتم استخدام العملة الافتراضية)
     * @param bool $includeSymbol ما إذا كان سيتم تضمين رمز العملة
     * @return string
     */
    public static function formatAmount($amount, $currency = null, $includeSymbol = true)
    {
        if ($currency === null) {
            $currency = Country::getDefaultCurrency();
        }
        
        // تنسيق المبلغ برقمين عشريين
        $formattedAmount = number_format($amount, 2);
        
        // إذا كانت العملة هي الجنيه السوداني (SDG) أو إذا كان مطلوبًا رمز العملة
        if ($includeSymbol) {
            $symbol = self::getCurrencySymbol($currency);
            return $formattedAmount . ' ' . $symbol;
        }
        
        return $formattedAmount . ' ' . $currency;
    }
    
    /**
     * الحصول على رمز العملة
     *
     * @param string $currency رمز العملة (مثل SDG, SAR, USD)
     * @return string
     */
    public static function getCurrencySymbol($currency = null)
    {
        if ($currency === null) {
            return Country::getDefaultCurrencySymbol();
        }
        
        $symbols = [
            'SDG' => 'ج.س', // الجنيه السوداني
            'SAR' => 'ر.س', // الريال السعودي
            'USD' => '$',    // الدولار الأمريكي
            'EUR' => '€',    // اليورو
            'GBP' => '£',    // الجنيه الإسترليني
            'EGP' => 'ج.م',  // الجنيه المصري
            'AED' => 'د.إ',  // الدرهم الإماراتي
            'QAR' => 'ر.ق',  // الريال القطري
        ];
        
        return $symbols[$currency] ?? $currency;
    }
    
    /**
     * التحويل بين العملات (للاستخدام المستقبلي)
     *
     * @param float $amount المبلغ المراد تحويله
     * @param string $fromCurrency العملة المصدر
     * @param string $toCurrency العملة الهدف
     * @return float
     */
    public static function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        // هذه الوظيفة مستقبلية وتتطلب خدمة تحويل عملات
        // يمكن استخدام API خارجي لأسعار العملات
        
        // في الوقت الحالي، نعيد المبلغ كما هو
        return $amount;
    }
}
