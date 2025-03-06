<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MessengerProfile;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MessengerAuthController extends Controller
{
    /**
     * عرض صفحة استكمال بيانات المندوب
     */
    public function completeProfile()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if ($profile) {
            return redirect()->route('messenger.dashboard');
        }
        
        return view('messenger.complete-profile');
    }

    /**
     * حفظ بيانات المندوب
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string|max:30|unique:messenger_profiles',
            'id_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driving_license' => 'required|string|max:30',
            'license_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_model' => 'required|string|max:100',
            'vehicle_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'vehicle_color' => 'required|string|max:30',
            'plate_number' => 'required|string|max:20',
            'vehicle_image' => 'required|image|max:5120',
            'zone_id' => 'nullable|integer|exists:zones,id',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'delivery_preference' => 'required|in:food,goods,both',
            'work_hours' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        
        // التحقق من عدم وجود ملف تعريف سابق
        if ($user->messengerProfile) {
            return redirect()->route('messenger.dashboard');
        }
        
        // حفظ المستندات
        $idDocumentPath = $request->file('id_document')->store('messenger_documents', 'public');
        $licenseDocumentPath = $request->file('license_document')->store('messenger_documents', 'public');
        $vehicleImagePath = $request->file('vehicle_image')->store('vehicle_images', 'public');
        
        // إنشاء المركبة
        $vehicle = Vehicle::create([
            'type' => $request->vehicle_type,
            'model' => $request->vehicle_model,
            'year' => $request->vehicle_year,
            'color' => $request->vehicle_color,
            'plate_number' => $request->plate_number,
            'image' => $vehicleImagePath,
        ]);
        
        // إنشاء ملف تعريف المندوب
        $messengerProfile = new MessengerProfile([
            'national_id' => $request->national_id,
            'id_document' => $idDocumentPath,
            'driving_license' => $request->driving_license,
            'license_document' => $licenseDocumentPath,
            'vehicle_id' => $vehicle->id,
            'zone_id' => $request->zone_id,
            'address' => $request->address,
            'city' => $request->city,
            'delivery_preference' => $request->delivery_preference,
            'work_hours' => $request->work_hours,
            'status' => 'pending', // الحالة الإفتراضية هي 'قيد المراجعة'
            'is_online' => false,
            'rating' => 0,
            'completed_deliveries' => 0,
            'reference_code' => strtoupper(Str::random(8)),
        ]);
        
        // حفظ ملف التعريف وربطه بالمستخدم
        $user->messengerProfile()->save($messengerProfile);
        
        return redirect()->route('messenger.dashboard')->with('success', 'تم تسجيل بياناتك بنجاح وهي قيد المراجعة من قبل الإدارة');
    }

    /**
     * عرض صفحة الملف الشخصي
     */
    public function profile()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        $vehicle = $profile ? $profile->vehicle : null;
        
        return view('messenger.profile', compact('user', 'profile', 'vehicle'));
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'profile_image' => 'nullable|image|max:2048',
            'work_hours' => 'required|string|max:100',
            'delivery_preference' => 'required|in:food,goods,both',
        ]);
        
        $profile = $user->messengerProfile;
        if (!$profile) {
            return redirect()->route('messenger.dashboard')->with('error', 'يجب استكمال بيانات الملف الشخصي أولاً');
        }
        
        // تحديث معلومات المستخدم
        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->email = $request->email;
        
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }
        
        $user->save();
        
        // تحديث معلومات المندوب
        $profile->address = $request->address;
        $profile->city = $request->city;
        $profile->work_hours = $request->work_hours;
        $profile->delivery_preference = $request->delivery_preference;
        $profile->save();
        
        return redirect()->route('messenger.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض صفحة معلومات المركبة
     */
    public function vehicle()
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('messenger.dashboard')->with('error', 'يجب استكمال بيانات الملف الشخصي أولاً');
        }
        
        $vehicle = $profile->vehicle;
        
        return view('messenger.vehicle', compact('vehicle'));
    }

    /**
     * تحديث معلومات المركبة
     */
    public function updateVehicle(Request $request)
    {
        $user = Auth::user();
        $profile = $user->messengerProfile;
        
        if (!$profile) {
            return redirect()->route('messenger.dashboard')->with('error', 'يجب استكمال بيانات الملف الشخصي أولاً');
        }
        
        $vehicle = $profile->vehicle;
        
        if (!$vehicle) {
            return redirect()->route('messenger.dashboard')->with('error', 'لم يتم العثور على معلومات المركبة');
        }
        
        $request->validate([
            'type' => 'required|string|max:50',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'color' => 'required|string|max:30',
            'plate_number' => 'required|string|max:20',
            'image' => 'nullable|image|max:5120',
        ]);
        
        $vehicle->type = $request->type;
        $vehicle->model = $request->model;
        $vehicle->year = $request->year;
        $vehicle->color = $request->color;
        $vehicle->plate_number = $request->plate_number;
        
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            
            $imagePath = $request->file('image')->store('vehicle_images', 'public');
            $vehicle->image = $imagePath;
        }
        
        $vehicle->save();
        
        return redirect()->route('messenger.vehicle')->with('success', 'تم تحديث معلومات المركبة بنجاح');
    }
}
