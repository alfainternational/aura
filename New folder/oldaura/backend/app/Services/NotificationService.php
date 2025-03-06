<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\KycVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    /**
     * إرسال إشعار إلى مستخدم
     *
     * @param User $user المستخدم المستهدف
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $type نوع الإشعار (info, success, warning, danger)
     * @param string|null $icon أيقونة الإشعار (Font Awesome)
     * @param string|null $actionUrl رابط الإجراء
     * @param array|null $data بيانات إضافية
     * @return UserNotification
     */
    public function send(User $user, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
    {
        try {
            // إنشاء إشعار داخلي في قاعدة البيانات
            $notification = $user->addNotification($title, $message, $type, $icon, $actionUrl, $data);
            
            // إرسال إشعار عبر البريد الإلكتروني إذا كان لديه بريد
            if ($user->email) {
                $this->sendEmail($user, $title, $message, $actionUrl);
            }
            
            // يمكن إضافة قنوات أخرى هنا (مثل SMS، Push Notifications، إلخ)
            
            return $notification;
        } catch (\Exception $e) {
            Log::error('فشل في إرسال الإشعار: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
            ]);
            
            throw $e;
        }
    }
    
    /**
     * إرسال إشعار إلى مجموعة من المستخدمين
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $users مجموعة المستخدمين
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $type نوع الإشعار (info, success, warning, danger)
     * @param string|null $icon أيقونة الإشعار (Font Awesome)
     * @param string|null $actionUrl رابط الإجراء
     * @param array|null $data بيانات إضافية
     * @return int عدد الإشعارات المرسلة
     */
    public function sendToMany($users, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
    {
        $count = 0;
        
        foreach ($users as $user) {
            try {
                $this->send($user, $title, $message, $type, $icon, $actionUrl, $data);
                $count++;
            } catch (\Exception $e) {
                Log::error('فشل في إرسال الإشعار للمستخدم #' . $user->id . ': ' . $e->getMessage());
                continue;
            }
        }
        
        return $count;
    }
    
    /**
     * إرسال إشعار إلى جميع المسؤولين
     *
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $type نوع الإشعار (info, success, warning, danger)
     * @param string|null $icon أيقونة الإشعار (Font Awesome)
     * @param string|null $actionUrl رابط الإجراء
     * @param array|null $data بيانات إضافية
     * @return int عدد الإشعارات المرسلة
     */
    public function sendToAdmins(string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
    {
        $admins = User::where('user_type', 'admin')->get();
        return $this->sendToMany($admins, $title, $message, $type, $icon, $actionUrl, $data);
    }
    
    /**
     * إرسال إشعار إلى جميع المستخدمين من نوع معين
     *
     * @param string $userType نوع المستخدم (admin, supervisor, merchant, agent, messenger, customer)
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $type نوع الإشعار (info, success, warning, danger)
     * @param string|null $icon أيقونة الإشعار (Font Awesome)
     * @param string|null $actionUrl رابط الإجراء
     * @param array|null $data بيانات إضافية
     * @return int عدد الإشعارات المرسلة
     */
    public function sendToUserType(string $userType, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
    {
        $users = User::where('user_type', $userType)->get();
        return $this->sendToMany($users, $title, $message, $type, $icon, $actionUrl, $data);
    }
    
    /**
     * إرسال إشعار عبر البريد الإلكتروني
     *
     * @param User $user المستخدم المستهدف
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string|null $actionUrl رابط الإجراء
     * @return void
     */
    protected function sendEmail(User $user, string $title, string $message, ?string $actionUrl = null)
    {
        NotificationFacade::route('mail', $user->email)
            ->notify(new \App\Notifications\GeneralNotification($title, $message, $actionUrl));
    }
    
    /**
     * إرسال إشعار بتحديث حالة التحقق من الهوية
     *
     * @param User $user المستخدم المستهدف
     * @param \App\Models\KycVerification $verification طلب التحقق من الهوية
     * @return UserNotification
     */
    public function sendKycStatusUpdated(User $user, $verification)
    {
        $title = 'تحديث حالة التحقق من الهوية';
        $message = '';
        $type = '';
        $icon = '';
        $actionUrl = '/user/kyc';
        
        if ($verification->status === 'approved') {
            $message = 'تمت الموافقة على طلب التحقق من الهوية الخاص بك. يمكنك الآن الاستفادة من جميع خدمات المنصة.';
            $type = 'success';
            $icon = 'check-circle';
        } elseif ($verification->status === 'rejected') {
            $message = 'تم رفض طلب التحقق من الهوية الخاص بك. سبب الرفض: ' . $verification->rejection_reason;
            $type = 'danger';
            $icon = 'times-circle';
        } else {
            $message = 'تم تحديث حالة طلب التحقق من الهوية الخاص بك إلى "قيد المراجعة".';
            $type = 'warning';
            $icon = 'clock';
        }
        
        // إرسال إشعار داخلي
        $notification = $this->send($user, $title, $message, $type, $icon, $actionUrl);
        
        // إرسال إشعار عبر نظام Laravel Notifications
        try {
            $user->notify(new \App\Notifications\KycStatusUpdated(
                $verification->status,
                $verification->status === 'rejected' ? $verification->rejection_reason : null
            ));
        } catch (\Exception $e) {
            Log::error('فشل في إرسال إشعار تحديث حالة KYC: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'verification_id' => $verification->id,
            ]);
        }
        
        return $notification;
    }
}
