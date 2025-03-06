<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UserService
{
    /**
     * إنشاء مستخدم جديد
     *
     * @param array $data بيانات المستخدم
     * @return User
     */
    public function createUser(array $data)
    {
        DB::beginTransaction();
        
        try {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->phone = $data['phone'] ?? null;
            $user->password = Hash::make($data['password']);
            $user->country = $data['country'] ?? null;
            $user->city = $data['city'] ?? null;
            $user->role = $data['role'] ?? 'user';
            $user->status = $data['status'] ?? 'active';
            $user->save();
            
            // إضافة الصورة الشخصية إذا وجدت
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $this->updateAvatar($user, $data['avatar']);
            }
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * تحديث بيانات المستخدم
     *
     * @param User $user المستخدم
     * @param array $data البيانات المراد تحديثها
     * @return User
     */
    public function updateUser(User $user, array $data)
    {
        DB::beginTransaction();
        
        try {
            if (isset($data['name'])) {
                $user->name = $data['name'];
            }
            
            if (isset($data['email']) && $data['email'] !== $user->email) {
                $user->email = $data['email'];
                $user->email_verified_at = null;
            }
            
            if (isset($data['phone']) && $data['phone'] !== $user->phone) {
                $user->phone = $data['phone'];
                $user->phone_verified_at = null;
            }
            
            if (isset($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            
            if (isset($data['country'])) {
                $user->country = $data['country'];
            }
            
            if (isset($data['city'])) {
                $user->city = $data['city'];
            }
            
            if (isset($data['status'])) {
                $user->status = $data['status'];
            }
            
            if (isset($data['role'])) {
                $user->role = $data['role'];
            }
            
            // تحديث الصورة الشخصية إذا وجدت
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $this->updateAvatar($user, $data['avatar']);
            }
            
            $user->save();
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * تحديث الصورة الشخصية للمستخدم
     *
     * @param User $user المستخدم
     * @param UploadedFile $avatar الصورة الشخصية
     * @return User
     */
    public function updateAvatar(User $user, UploadedFile $avatar)
    {
        // حذف الصورة القديمة إذا وجدت
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // حفظ الصورة الجديدة
        $fileName = 'avatars/' . Str::uuid() . '.' . $avatar->getClientOriginalExtension();
        $avatar->storeAs('public', $fileName);
        
        $user->avatar = $fileName;
        $user->save();
        
        return $user;
    }
    
    /**
     * تفعيل/تعطيل المصادقة الثنائية للمستخدم
     *
     * @param User $user المستخدم
     * @param bool $enable تفعيل أم تعطيل
     * @return User
     */
    public function toggleTwoFactorAuth(User $user, bool $enable = true)
    {
        if ($enable) {
            $user->enableTwoFactorAuthentication();
        } else {
            $user->disableTwoFactorAuthentication();
        }
        
        return $user;
    }
    
    /**
     * إضافة إشعار للمستخدم
     *
     * @param User $user المستخدم
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $type نوع الإشعار (info, success, warning, danger)
     * @param string|null $icon أيقونة الإشعار (Font Awesome)
     * @param string|null $actionUrl رابط الإجراء
     * @param array|null $data بيانات إضافية
     * @return UserNotification
     */
    public function addNotification(User $user, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
    {
        $notification = new UserNotification();
        $notification->user_id = $user->id;
        $notification->title = $title;
        $notification->message = $message;
        $notification->type = $type;
        $notification->icon = $icon;
        $notification->action_url = $actionUrl;
        $notification->data = $data;
        $notification->save();
        
        return $notification;
    }
    
    /**
     * إرسال طلب تحقق KYC
     *
     * @param User $user المستخدم
     * @param array $data بيانات التحقق
     * @return KycVerification
     */
    public function submitKycVerification(User $user, array $data)
    {
        DB::beginTransaction();
        
        try {
            // التحقق من عدم وجود طلب تحقق معلق
            $pendingVerification = KycVerification::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_review'])
                ->first();
                
            if ($pendingVerification) {
                throw new \Exception('لديك طلب تحقق معلق بالفعل');
            }
            
            $verification = new KycVerification();
            $verification->user_id = $user->id;
            $verification->full_name = $data['full_name'];
            $verification->id_type = $data['id_type'];
            $verification->id_number = $data['id_number'];
            $verification->date_of_birth = $data['date_of_birth'];
            $verification->country = $data['country'];
            $verification->city = $data['city'];
            $verification->address = $data['address'];
            $verification->status = 'pending';
            
            // معالجة المستندات
            if (isset($data['id_front']) && $data['id_front'] instanceof UploadedFile) {
                $idFrontPath = 'kyc/' . Str::uuid() . '.' . $data['id_front']->getClientOriginalExtension();
                $data['id_front']->storeAs('public', $idFrontPath);
                $verification->id_front = $idFrontPath;
            }
            
            if (isset($data['id_back']) && $data['id_back'] instanceof UploadedFile) {
                $idBackPath = 'kyc/' . Str::uuid() . '.' . $data['id_back']->getClientOriginalExtension();
                $data['id_back']->storeAs('public', $idBackPath);
                $verification->id_back = $idBackPath;
            }
            
            if (isset($data['selfie']) && $data['selfie'] instanceof UploadedFile) {
                $selfiePath = 'kyc/' . Str::uuid() . '.' . $data['selfie']->getClientOriginalExtension();
                $data['selfie']->storeAs('public', $selfiePath);
                $verification->selfie = $selfiePath;
            }
            
            $verification->save();
            
            DB::commit();
            
            return $verification;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * تحديث حالة طلب تحقق KYC
     *
     * @param KycVerification $verification طلب التحقق
     * @param string $status الحالة الجديدة (pending, in_review, approved, rejected)
     * @param string|null $notes ملاحظات
     * @param User $updatedBy المستخدم الذي قام بالتحديث
     * @return KycVerification
     */
    public function updateKycStatus(KycVerification $verification, string $status, ?string $notes = null, User $updatedBy)
    {
        // التحقق من أن المستخدم لديه صلاحية تحديث حالة طلبات التحقق
        if (!in_array($updatedBy->role, ['admin', 'supervisor'])) {
            throw new \Exception('ليس لديك صلاحية تحديث حالة طلبات التحقق');
        }
        
        // التحقق من أن الحالة صالحة
        if (!in_array($status, ['pending', 'in_review', 'approved', 'rejected'])) {
            throw new \Exception('الحالة غير صالحة');
        }
        
        $verification->status = $status;
        $verification->notes = $notes;
        $verification->reviewed_by = $updatedBy->id;
        $verification->reviewed_at = now();
        $verification->save();
        
        // إذا تم الموافقة على الطلب، قم بتحديث حالة المستخدم
        if ($status === 'approved') {
            $user = User::find($verification->user_id);
            $user->is_verified = true;
            $user->save();
        }
        
        return $verification;
    }
}
