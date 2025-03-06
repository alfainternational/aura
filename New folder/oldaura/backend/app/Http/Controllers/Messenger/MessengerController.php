<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MessengerProfile;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessengerController extends Controller
{
    /**
     * عرض لوحة تحكم المندوب
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        // إحصائيات المندوب
        $stats = [
            'completed_deliveries' => $profile->completed_deliveries,
            'rating' => $profile->rating,
            'status' => $profile->status,
            'is_online' => $profile->is_online,
            'zone' => $profile->zone,
        ];
        
        // الحصول على معلومات المشرف المسؤول
        $supervisor = null;
        if ($profile->supervisor_id) {
            $supervisor = User::find($profile->supervisor_id);
        }
        
        // معلومات آخر نشاط
        $lastActivity = [
            'last_login' => $user->last_login_at,
            'last_active' => $profile->last_active_at,
        ];
        
        return view('messenger.dashboard', compact('stats', 'supervisor', 'lastActivity'));
    }

    /**
     * تغيير حالة المندوب (متصل/غير متصل)
     */
    public function toggleOnlineStatus(Request $request)
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        $isOnline = $request->has('is_online') ? (bool)$request->is_online : !$profile->is_online;
        
        $profile->is_online = $isOnline;
        
        if ($isOnline) {
            $profile->status = 'available';
            $profile->last_active_at = Carbon::now();
        } else {
            $profile->status = 'offline';
        }
        
        $profile->save();
        
        $status = $isOnline ? 'متصل' : 'غير متصل';
        
        return back()->with('success', "تم تغيير حالتك إلى: $status");
    }

    /**
     * تحديث موقع المندوب الحالي
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return response()->json(['error' => 'غير مصرح لك بالوصول'], 403);
        }
        
        $profile->current_latitude = $request->latitude;
        $profile->current_longitude = $request->longitude;
        $profile->last_active_at = Carbon::now();
        $profile->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * تغيير حالة المندوب (متاح، مشغول، إلخ)
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:available,busy,on_delivery,on_break',
        ]);
        
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        // إذا كان المندوب غير متصل، يجب تغيير حالته إلى متصل أولاً
        if (!$profile->is_online && $request->status !== 'offline') {
            $profile->is_online = true;
        }
        
        $profile->status = $request->status;
        $profile->last_active_at = Carbon::now();
        $profile->save();
        
        $statusMessages = [
            'available' => 'متاح',
            'busy' => 'مشغول',
            'on_delivery' => 'في مهمة توصيل',
            'on_break' => 'في استراحة',
        ];
        
        return back()->with('success', "تم تغيير حالتك إلى: " . $statusMessages[$request->status]);
    }

    /**
     * عرض ملف المندوب الشخصي
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        return view('messenger.profile', compact('user', 'profile'));
    }

    /**
     * تحديث معلومات المندوب الشخصية
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'profile_image' => 'nullable|image|max:2048',
        ]);
        
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }
        
        $user->save();
        
        return back()->with('success', 'تم تحديث المعلومات الشخصية بنجاح');
    }

    /**
     * عرض معلومات المركبة
     */
    public function vehicle()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        return view('messenger.vehicle', compact('profile'));
    }

    /**
     * تحديث معلومات المركبة
     */
    public function updateVehicle(Request $request)
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        $request->validate([
            'vehicle_type' => 'required|string|max:50',
            'vehicle_plate' => 'required|string|max:20',
        ]);
        
        $profile->vehicle_type = $request->vehicle_type;
        $profile->vehicle_plate = $request->vehicle_plate;
        $profile->save();
        
        return back()->with('success', 'تم تحديث معلومات المركبة بنجاح');
    }

    /**
     * عرض إحصائيات المندوب
     */
    public function statistics()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('login')->with('error', 'لا يمكنك الوصول إلى هذه الصفحة');
        }
        
        // هنا يمكن جلب إحصائيات أكثر تفصيلاً من جداول طلبات التوصيل
        
        $statistics = [
            'completed_deliveries' => $profile->completed_deliveries,
            'rating' => $profile->rating,
            'earnings' => 0, // يتم حسابها من جدول الطلبات
            'distance_traveled' => 0, // يتم حسابها من جدول الطلبات
        ];
        
        return view('messenger.statistics', compact('statistics'));
    }
}
