<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    /**
     * عرض لوحة تحكم العميل
     */
    public function dashboard()
    {
        return view('customer.dashboard');
    }

    /**
     * عرض صفحة الملف الشخصي
     */
    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    /**
     * تحديث بيانات الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $user->name = $request->name;
        $user->phone_number = $request->phone_number;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض صفحة العناوين
     */
    public function addresses()
    {
        $addresses = Auth::user()->addresses;
        return view('customer.addresses', compact('addresses'));
    }

    /**
     * إضافة عنوان جديد
     */
    public function addAddress(Request $request)
    {
        $request->validate([
            'address_title' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        
        $address = new UserAddress($request->all());
        
        // إذا كان هذا هو العنوان الأول أو تم تحديده كافتراضي
        if ($user->addresses()->count() === 0 || $request->is_default) {
            // جعل كل العناوين غير افتراضية
            $user->addresses()->update(['is_default' => false]);
            $address->is_default = true;
        }
        
        $user->addresses()->save($address);

        return back()->with('success', 'تم إضافة العنوان بنجاح');
    }

    /**
     * تعيين عنوان كافتراضي
     */
    public function setDefaultAddress($id)
    {
        $user = Auth::user();
        
        // التأكد من أن العنوان ينتمي للمستخدم الحالي
        $address = $user->addresses()->findOrFail($id);
        
        // جعل كل العناوين غير افتراضية
        $user->addresses()->update(['is_default' => false]);
        
        // تعيين العنوان المحدد كافتراضي
        $address->is_default = true;
        $address->save();
        
        return back()->with('success', 'تم تعيين العنوان كافتراضي');
    }

    /**
     * حذف عنوان
     */
    public function deleteAddress($id)
    {
        $user = Auth::user();
        
        // التأكد من أن العنوان ينتمي للمستخدم الحالي
        $address = $user->addresses()->findOrFail($id);
        
        // حذف العنوان
        $address->delete();
        
        // إذا كان العنوان المحذوف هو الافتراضي وهناك عناوين أخرى
        // نجعل أول عنوان هو الافتراضي
        if ($address->is_default && $user->addresses()->count() > 0) {
            $user->addresses()->first()->update(['is_default' => true]);
        }
        
        return back()->with('success', 'تم حذف العنوان بنجاح');
    }

    /**
     * عرض صفحة استكمال بيانات العميل
     */
    public function completeProfile()
    {
        $user = Auth::user();
        $profile = $user->customerProfile;
        
        if ($profile) {
            return redirect()->route('customer.dashboard');
        }
        
        return view('customer.complete-profile');
    }

    /**
     * حفظ بيانات العميل المستكملة
     */
    public function storeProfile(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
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
        
        // إنشاء ملف تعريف العميل إذا لم يكن موجودًا
        if (!$user->customerProfile) {
            $profileData = [
                'preferences' => json_encode([]),
                'verified' => true
            ];
            
            $profile = new CustomerProfile($profileData);
            $user->customerProfile()->save($profile);
            
            // إضافة العنوان الأساسي إذا تم تقديمه
            if ($request->filled('address')) {
                $address = new UserAddress([
                    'address_title' => 'العنوان الرئيسي',
                    'recipient_name' => $user->name,
                    'recipient_phone' => $user->phone_number,
                    'address_line1' => $request->address,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'country' => 'المملكة العربية السعودية',
                    'is_default' => true
                ]);
                
                $user->addresses()->save($address);
            }
        }
        
        return redirect()->route('customer.dashboard')
            ->with('success', 'تم استكمال البيانات بنجاح');
    }
}
