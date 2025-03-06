<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AgentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AgentAuthController extends Controller
{
    /**
     * عرض صفحة استكمال بيانات الوكيل
     */
    public function completeProfile()
    {
        $user = Auth::user();
        $profile = $user->agentProfile;
        
        if ($profile) {
            return redirect()->route('agent.dashboard');
        }
        
        return view('agent.complete-profile');
    }

    /**
     * حفظ بيانات الوكيل
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'agent_type' => 'required|in:delivery,service,general',
            'area_of_operation' => 'required|string|max:255',
            'identification_number' => 'required|string|max:30',
            'identification_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'vehicle_type' => 'required_if:agent_type,delivery|string|max:50',
            'vehicle_number' => 'required_if:agent_type,delivery|string|max:20',
            'license_number' => 'required_if:agent_type,delivery|string|max:30',
        ]);

        $user = Auth::user();
        
        // التحقق من عدم وجود ملف تعريف سابق
        if ($user->agentProfile) {
            return redirect()->route('agent.dashboard');
        }
        
        $profileData = $request->except(['identification_document']);
        
        // إنشاء معرف فريد للوكيل
        $profileData['agent_id'] = 'AGT' . strtoupper(Str::random(8));
        
        // معالجة وثيقة الهوية
        if ($request->hasFile('identification_document')) {
            $docPath = $request->file('identification_document')->store('agent_documents', 'public');
            $profileData['identification_document'] = $docPath;
        }
        
        // إنشاء ملف تعريف الوكيل
        $profile = new AgentProfile($profileData);
        $user->agentProfile()->save($profile);
        
        return redirect()->route('agent.dashboard')
            ->with('success', 'تم إنشاء حساب الوكيل بنجاح! يرجى انتظار التحقق من حسابك.');
    }

    /**
     * عرض لوحة تحكم الوكيل
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->agentProfile;
        
        if (!$profile) {
            return redirect()->route('agent.complete-profile');
        }
        
        return view('agent.dashboard', compact('profile'));
    }

    /**
     * عرض حالة الوكيل
     */
    public function status()
    {
        $user = Auth::user();
        $profile = $user->agentProfile;
        
        if (!$profile) {
            return redirect()->route('agent.complete-profile');
        }
        
        return view('agent.status', compact('profile'));
    }

    /**
     * تغيير حالة نشاط الوكيل
     */
    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
        $profile = $user->agentProfile;
        
        if (!$profile) {
            return redirect()->route('agent.complete-profile');
        }
        
        $profile->is_active = !$profile->is_active;
        
        if ($profile->is_active) {
            $profile->last_active_at = now();
        }
        
        $profile->save();
        
        $status = $profile->is_active ? 'نشط' : 'غير نشط';
        
        return back()->with('success', "تم تغيير حالتك إلى: $status");
    }

    /**
     * عرض صفحة إعدادات الوكيل
     */
    public function settings()
    {
        $user = Auth::user();
        $profile = $user->agentProfile;
        
        if (!$profile) {
            return redirect()->route('agent.complete-profile');
        }
        
        return view('agent.settings', compact('user', 'profile'));
    }

    /**
     * تحديث بيانات الوكيل
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'area_of_operation' => 'required|string|max:255',
            'vehicle_type' => 'required_if:agent_type,delivery|string|max:50',
            'vehicle_number' => 'required_if:agent_type,delivery|string|max:20',
        ]);

        $user = Auth::user();
        $profile = $user->agentProfile;
        
        if (!$profile) {
            return redirect()->route('agent.complete-profile');
        }
        
        // تحديث بيانات المستخدم
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->save();
        
        // تحديث بيانات الوكيل
        $profile->area_of_operation = $request->area_of_operation;
        
        if ($profile->agent_type == 'delivery') {
            $profile->vehicle_type = $request->vehicle_type;
            $profile->vehicle_number = $request->vehicle_number;
        }
        
        $profile->save();
        
        return back()->with('success', 'تم تحديث البيانات بنجاح');
    }
}
