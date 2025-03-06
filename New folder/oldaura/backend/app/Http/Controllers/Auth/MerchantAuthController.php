<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MerchantProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MerchantAuthController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        // لا نضيف check-role في البناء لأن بعض الطرق تحتاج الوصول قبل تحديد النوع
    }

    /**
     * عرض صفحة استكمال بيانات التاجر
     */
    public function completeProfile()
    {
        $user = Auth::user();
        $profile = $user->merchantProfile;
        
        if ($profile) {
            return redirect()->route('merchant.dashboard');
        }
        
        return view('merchant.complete-profile');
    }

    /**
     * حفظ بيانات التاجر
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'nullable|string',
            'business_address' => 'required|string|max:255',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'business_logo' => 'nullable|image|max:2048',
            'business_cover' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        
        // التحقق من عدم وجود ملف تعريف سابق
        if ($user->merchantProfile) {
            return redirect()->route('merchant.dashboard');
        }
        
        $profileData = $request->except(['business_logo', 'business_cover']);
        
        // معالجة الصور إذا تم تحميلها
        if ($request->hasFile('business_logo')) {
            $logoPath = $request->file('business_logo')->store('merchant_logos', 'public');
            $profileData['business_logo'] = $logoPath;
        }
        
        if ($request->hasFile('business_cover')) {
            $coverPath = $request->file('business_cover')->store('merchant_covers', 'public');
            $profileData['business_cover'] = $coverPath;
        }
        
        // إنشاء ملف تعريف التاجر
        $profile = new MerchantProfile($profileData);
        $user->merchantProfile()->save($profile);
        
        return redirect()->route('merchant.dashboard')
            ->with('success', 'تم إنشاء حساب التاجر بنجاح! يرجى انتظار التحقق من حسابك.');
    }

    /**
     * عرض لوحة تحكم التاجر
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->merchantProfile;
        
        if (!$profile) {
            return redirect()->route('merchant.complete-profile');
        }
        
        return view('merchant.dashboard', compact('profile'));
    }

    /**
     * عرض صفحة بيانات المتجر
     */
    public function storeSettings()
    {
        $user = Auth::user();
        $profile = $user->merchantProfile;
        
        if (!$profile) {
            return redirect()->route('merchant.complete-profile');
        }
        
        return view('merchant.store-settings', compact('profile'));
    }

    /**
     * تحديث بيانات المتجر
     */
    public function updateStore(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'nullable|string',
            'business_address' => 'required|string|max:255',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'business_website' => 'nullable|url|max:255',
            'business_logo' => 'nullable|image|max:2048',
            'business_cover' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $profile = $user->merchantProfile;
        
        if (!$profile) {
            return redirect()->route('merchant.complete-profile');
        }
        
        $profile->business_name = $request->business_name;
        $profile->business_type = $request->business_type;
        $profile->business_description = $request->business_description;
        $profile->business_address = $request->business_address;
        $profile->business_phone = $request->business_phone;
        $profile->business_email = $request->business_email;
        $profile->business_website = $request->business_website;
        
        // معالجة الصور إذا تم تحميلها
        if ($request->hasFile('business_logo')) {
            $logoPath = $request->file('business_logo')->store('merchant_logos', 'public');
            $profile->business_logo = $logoPath;
        }
        
        if ($request->hasFile('business_cover')) {
            $coverPath = $request->file('business_cover')->store('merchant_covers', 'public');
            $profile->business_cover = $coverPath;
        }
        
        $profile->save();
        
        return back()->with('success', 'تم تحديث بيانات المتجر بنجاح');
    }
}
