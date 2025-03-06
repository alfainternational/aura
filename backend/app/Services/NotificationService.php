<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use App\Models\KycVerification;
use App\Models\NotificationChannel;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
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
            
            // الحصول على القنوات النشطة
            $activeChannels = NotificationChannel::where('is_active', true)->get();
            
            foreach ($activeChannels as $channel) {
                switch ($channel->type) {
                    case 'email':
                        if ($user->email) {
                            $this->sendViaEmail($user, $title, $message, $actionUrl, $channel);
                        }
                        break;
                    case 'sms':
                        if ($user->phone) {
                            $this->sendViaSms($user, $message, $channel);
                        }
                        break;
                    case 'telegram':
                        if ($user->telegram_id) {
                            $this->sendViaTelegram($user, $title, $message, $actionUrl, $channel);
                        }
                        break;
                    case 'whatsapp':
                        if ($user->phone) {
                            $this->sendViaWhatsApp($user, $message, $channel);
                        }
                        break;
                }
            }
            
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
     * @param NotificationChannel|null $channel قناة الإشعار
     * @return void
     */
    protected function sendViaEmail(User $user, string $title, string $message, ?string $actionUrl = null, ?NotificationChannel $channel = null)
    {
        if (!$user->email) {
            return;
        }
        
        if ($channel && isset($channel->settings['template_id'])) {
            // استخدام قالب إذا كان متوفرًا
            $template = NotificationTemplate::find($channel->settings['template_id']);
            
            if ($template) {
                $parsed = $template->parse([
                    'user_name' => $user->name,
                    'title' => $title,
                    'message' => $message,
                    'action_url' => $actionUrl,
                    'app_name' => config('app.name'),
                ]);
                
                $title = $parsed['subject'];
                $message = $parsed['body'];
            }
        }
        
        NotificationFacade::route('mail', $user->email)
            ->notify(new \App\Notifications\GeneralNotification($title, $message, $actionUrl));
    }
    
    /**
     * إرسال إشعار عبر SMS
     *
     * @param User $user المستخدم المستهدف
     * @param string $message نص الإشعار
     * @param NotificationChannel|null $channel قناة الإشعار
     * @return void
     */
    protected function sendViaSms(User $user, string $message, ?NotificationChannel $channel = null)
    {
        if (!$user->phone) {
            return;
        }
        
        // يمكن استخدام خدمة SMS هنا مثل Twilio أو Nexmo
        try {
            Log::info('إرسال SMS إلى الرقم: ' . $user->phone, [
                'message' => $message,
                'service' => $channel ? $channel->settings['service'] ?? 'default' : 'default',
            ]);
            
            // هنا يتم استدعاء خدمة SMS
            // مثال: $this->smsService->send($user->phone, $message);
        } catch (\Exception $e) {
            Log::error('فشل في إرسال SMS: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'phone' => $user->phone,
            ]);
        }
    }
    
    /**
     * إرسال إشعار عبر Telegram
     *
     * @param User $user المستخدم المستهدف
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string|null $actionUrl رابط الإجراء
     * @param NotificationChannel|null $channel قناة الإشعار
     * @return void
     */
    protected function sendViaTelegram(User $user, string $title, string $message, ?string $actionUrl = null, ?NotificationChannel $channel = null)
    {
        if (!$user->telegram_id) {
            return;
        }
        
        try {
            $botToken = $channel ? $channel->settings['bot_token'] ?? env('TELEGRAM_BOT_TOKEN') : env('TELEGRAM_BOT_TOKEN');
            
            if (!$botToken) {
                throw new \Exception('Telegram Bot Token not configured');
            }
            
            $text = "*{$title}*\n\n{$message}";
            
            if ($actionUrl) {
                $text .= "\n\n[اضغط هنا للمزيد من المعلومات]({$actionUrl})";
            }
            
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $user->telegram_id,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
            
            if (!$response->successful()) {
                throw new \Exception('Telegram API error: ' . $response->body());
            }
            
            Log::info('تم إرسال إشعار Telegram بنجاح', [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
            ]);
        } catch (\Exception $e) {
            Log::error('فشل في إرسال إشعار Telegram: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'telegram_id' => $user->telegram_id,
            ]);
        }
    }
    
    /**
     * إرسال إشعار عبر WhatsApp
     *
     * @param User $user المستخدم المستهدف
     * @param string $message نص الإشعار
     * @param NotificationChannel|null $channel قناة الإشعار
     * @return void
     */
    protected function sendViaWhatsApp(User $user, string $message, ?NotificationChannel $channel = null)
    {
        if (!$user->phone) {
            return;
        }
        
        // يمكن استخدام واجهة برمجة تطبيقات WhatsApp Business API هنا
        try {
            Log::info('إرسال WhatsApp إلى الرقم: ' . $user->phone, [
                'message' => $message,
                'service' => $channel ? $channel->settings['service'] ?? 'default' : 'default',
            ]);
            
            // هنا يتم استدعاء خدمة WhatsApp API
            // مثال: $this->whatsappService->send($user->phone, $message);
        } catch (\Exception $e) {
            Log::error('فشل في إرسال WhatsApp: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'phone' => $user->phone,
            ]);
        }
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
    
    /**
     * إرسال إشعار للمستخدم عن معاملة في المحفظة
     *
     * @param User $user المستخدم المستهدف
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $notificationType نوع الإشعار (wallet_deposit, wallet_withdrawal, etc.)
     * @param array $data بيانات إضافية
     * @return UserNotification
     */
    public function sendNotification(User $user, string $title, string $message, string $notificationType, array $data = [])
    {
        $type = 'info';
        $icon = null;
        $actionUrl = null;
        
        switch ($notificationType) {
            case 'wallet_deposit':
                $type = 'success';
                $icon = 'money-bill-wave';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            case 'wallet_deposit_approved':
                $type = 'success';
                $icon = 'check-circle';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            case 'wallet_withdrawal':
                $type = 'warning';
                $icon = 'money-bill-wave';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            case 'wallet_withdrawal_approved':
                $type = 'success';
                $icon = 'check-circle';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            case 'wallet_transaction_rejected':
                $type = 'danger';
                $icon = 'times-circle';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            case 'wallet_payment':
                $type = 'info';
                $icon = 'shopping-cart';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            case 'wallet_refund':
                $type = 'success';
                $icon = 'undo';
                $actionUrl = '/wallet/transactions/' . ($data['transaction_id'] ?? '');
                break;
                
            default:
                $type = 'info';
                $icon = 'bell';
                $actionUrl = '/wallet';
                break;
        }
        
        return $this->send($user, $title, $message, $type, $icon, $actionUrl, $data);
    }
    
    /**
     * إرسال إشعار للمسؤولين عن معاملة في المحفظة
     *
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $notificationType نوع الإشعار (admin_wallet_deposit, admin_wallet_withdrawal, etc.)
     * @param array $data بيانات إضافية
     * @return int عدد الإشعارات المرسلة
     */
    public function sendAdminNotification(string $title, string $message, string $notificationType, array $data = [])
    {
        $type = 'info';
        $icon = null;
        $actionUrl = null;
        
        switch ($notificationType) {
            case 'admin_wallet_deposit':
                $type = 'info';
                $icon = 'money-bill-wave';
                $actionUrl = '/admin/wallet/deposits';
                break;
                
            case 'admin_wallet_withdrawal':
                $type = 'warning';
                $icon = 'money-bill-wave';
                $actionUrl = '/admin/wallet/withdrawals';
                break;
                
            case 'admin_wallet_transaction':
                $type = 'info';
                $icon = 'exchange-alt';
                $actionUrl = '/admin/wallet/transactions';
                break;
                
            default:
                $type = 'info';
                $icon = 'bell';
                $actionUrl = '/admin/wallet/transactions';
                break;
        }
        
        // Si hay un ID de transacción específico, añadir a la URL
        if (isset($data['transaction_id'])) {
            $actionUrl .= '/' . $data['transaction_id'];
        }
        
        return $this->sendToAdmins($title, $message, $type, $icon, $actionUrl, $data);
    }
}
