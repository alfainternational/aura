<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Zone;
use App\Models\SupervisorProfile;

class SupervisorAuthController extends Controller
{
    /**
     * عرض صفحة استكمال بيانات المشرف
     */
    public function completeProfile()
    {
        $user = Auth::user();
        $profile = $user->supervisorProfile;
        
        if ($profile) {
            return redirect()->route('supervisor.dashboard');
        }
        
        // الحصول على قائمة المناطق لعرضها في الفورم
        $zones = Zone::all();
        
        return view('supervisor.complete-profile', compact('zones'));
    }

    /**
     * حفظ بيانات المشرف
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'department' => 'required|string',
            'position' => 'required|string|max:100',
            'zones' => 'required|array',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        
        // تحديث بيانات المستخدم
        $user->phone_number = $request->phone_number;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }
        
        $user->save();
        
        // إنشاء ملف تعريف المشرف
        $profile = new SupervisorProfile([
            'department' => $request->department,
            'position' => $request->position,
        ]);
        
        $user->supervisorProfile()->save($profile);
        
        // ربط المشرف بالمناطق المختارة
        $profile->zones()->sync($request->zones);
        
        return redirect()->route('supervisor.dashboard')
            ->with('success', 'تم استكمال البيانات بنجاح');
    }

    /**
     * عرض لوحة تحكم المشرف
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->supervisorProfile;
        $zones = $profile ? $profile->zones : collect([]);
        
        return view('supervisor.dashboard', compact('user', 'profile', 'zones'));
    }

    /**
     * عرض صفحة الملف الشخصي
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = $user->supervisorProfile;
        $zones = $profile ? $profile->zones : collect([]);
        
        return view('supervisor.profile', compact('user', 'profile', 'zones'));
    }

    /**
     * تحديث بيانات الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->supervisorProfile;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'department' => 'required|string',
            'position' => 'required|string|max:100',
            'zones' => 'required|array',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // تحديث بيانات المستخدم
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();
        
        // تحديث بيانات ملف تعريف المشرف
        $profile->department = $request->department;
        $profile->position = $request->position;
        $profile->save();
        
        // تحديث المناطق المرتبطة بالمشرف
        $profile->zones()->sync($request->zones);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}
