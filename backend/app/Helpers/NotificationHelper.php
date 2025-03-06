<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserNotification;
use App\Events\NotificationSentEvent;

class NotificationHelper
{
    /**
     * إنشاء إشعار جديد للمستخدم
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
    public static function createNotification(User $user, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
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
        
        // إطلاق حدث إرسال الإشعار
        event(new NotificationSentEvent($user, $notification));
        
        return $notification;
    }
    
    /**
     * إنشاء إشعار لمجموعة من المستخدمين
     *
     * @param array $userIds معرفات المستخدمين
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string $type نوع الإشعار (info, success, warning, danger)
     * @param string|null $icon أيقونة الإشعار (Font Awesome)
     * @param string|null $actionUrl رابط الإجراء
     * @param array|null $data بيانات إضافية
     * @return array
     */
    public static function createNotificationForUsers(array $userIds, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
    {
        $notifications = [];
        
        $users = User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            $notifications[] = self::createNotification($user, $title, $message, $type, $icon, $actionUrl, $data);
        }
        
        return $notifications;
    }
    
    /**
     * وضع علامة "مقروء" على إشعار
     *
     * @param UserNotification $notification الإشعار
     * @return UserNotification
     */
    public static function markAsRead(UserNotification $notification)
    {
        $notification->read_at = now();
        $notification->save();
        
        return $notification;
    }
    
    /**
     * وضع علامة "مقروء" على جميع إشعارات المستخدم
     *
     * @param User $user المستخدم
     * @return bool
     */
    public static function markAllAsRead(User $user)
    {
        return UserNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
    
    /**
     * حذف إشعار
     *
     * @param UserNotification $notification الإشعار
     * @return bool
     */
    public static function deleteNotification(UserNotification $notification)
    {
        return $notification->delete();
    }
    
    /**
     * حذف جميع إشعارات المستخدم
     *
     * @param User $user المستخدم
     * @return bool
     */
    public static function deleteAllNotifications(User $user)
    {
        return UserNotification::where('user_id', $user->id)->delete();
    }
}
