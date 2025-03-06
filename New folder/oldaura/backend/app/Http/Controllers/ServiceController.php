<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * عرض صفحة جميع الخدمات
     */
    public function index()
    {
        return view('services.index');
    }

    /**
     * عرض صفحة المحفظة الإلكترونية
     */
    public function wallet()
    {
        return view('services.wallet');
    }

    /**
     * عرض صفحة التجارة الإلكترونية
     */
    public function commerce()
    {
        return view('services.commerce');
    }

    /**
     * عرض صفحة المراسلة والاتصالات
     */
    public function messaging()
    {
        return view('services.messaging');
    }

    /**
     * عرض صفحة خدمات التوصيل
     */
    public function delivery()
    {
        return view('services.delivery');
    }

    /**
     * عرض صفحة المساعد الذكي
     */
    public function aiAssistant()
    {
        return view('services.ai-assistant');
    }

    /**
     * عرض صفحة الوكلاء
     */
    public function agents()
    {
        return view('services.agents');
    }
}
