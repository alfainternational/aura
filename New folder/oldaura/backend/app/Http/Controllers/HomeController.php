<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        return view('home');
    }

    /**
     * عرض صفحة "عن التطبيق"
     */
    public function about()
    {
        return view('about');
    }

    /**
     * عرض صفحة المميزات
     */
    public function features()
    {
        return view('features');
    }

    /**
     * عرض صفحة التطبيق
     */
    public function app()
    {
        return view('app');
    }

    /**
     * عرض صفحة آراء العملاء
     */
    public function testimonials()
    {
        return view('testimonials');
    }
    
    /**
     * عرض صفحة تواصل معنا
     */
    public function contact()
    {
        return view('contact');
    }
    
    /**
     * عرض سياسة الخصوصية
     */
    public function privacy()
    {
        return view('privacy');
    }
    
    /**
     * عرض شروط الاستخدام
     */
    public function terms()
    {
        return view('terms');
    }
    
    /**
     * عرض سياسة ملفات تعريف الارتباط
     */
    public function cookies()
    {
        return view('cookies');
    }
    
    /**
     * عرض صفحة اختبار الألوان والقوالب
     */
    public function testTheme()
    {
        return view('test-theme');
    }
}
