<?php

namespace App\Services\AI\Contracts;

interface BusinessInsightServiceInterface
{
    /**
     * تحليل بيانات المبيعات واستخراج رؤى تجارية
     * 
     * @param array $salesData بيانات المبيعات
     * @param array $options خيارات التحليل
     * @return array رؤى وتوصيات
     */
    public function analyzeSalesData(array $salesData, array $options = []): array;
    
    /**
     * التنبؤ بالمبيعات المستقبلية
     * 
     * @param array $historicalData البيانات التاريخية
     * @param int $forecastPeriod فترة التنبؤ بالأيام
     * @return array توقعات المبيعات
     */
    public function forecastSales(array $historicalData, int $forecastPeriod = 30): array;
    
    /**
     * تحليل سلوك العملاء واكتشاف الأنماط
     * 
     * @param array $customerData بيانات العملاء
     * @return array أنماط سلوك العملاء
     */
    public function analyzeCustomerBehavior(array $customerData): array;
    
    /**
     * تحديد الاتجاهات والأنماط في بيانات المنتجات
     * 
     * @param array $productData بيانات المنتجات
     * @return array اتجاهات وأنماط
     */
    public function identifyProductTrends(array $productData): array;
}
