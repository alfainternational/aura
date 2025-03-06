<?php

namespace App\Listeners;

use App\Events\NotificationSentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\NotificationService;

class SendNotificationListener implements ShouldQueue
{
    /**
     * خدمة الإشعارات
     *
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create the event listener.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     *
     * @param  NotificationSentEvent  $event
     * @return void
     */
    public function handle(NotificationSentEvent $event)
    {
        // إرسال الإشعار عبر القنوات النشطة (البريد الإلكتروني، الرسائل القصيرة، إلخ)
        // يتم استدعاء هذا بعد إنشاء الإشعار في قاعدة البيانات
        
        // الحصول على المستخدم والإشعار من الحدث
        $user = $event->user;
        $notification = $event->notification;
        
        // إرسال الإشعار عبر البريد الإلكتروني إذا كان لدى المستخدم بريد إلكتروني
        if ($user->email) {
            $this->notificationService->sendViaEmail(
                $user,
                $notification->title,
                $notification->message,
                $notification->action_url
            );
        }
        
        // إرسال الإشعار عبر الرسائل القصيرة إذا كان لدى المستخدم رقم هاتف
        if ($user->phone) {
            $this->notificationService->sendViaSms(
                $user,
                $notification->message
            );
        }
        
        // إرسال الإشعار عبر الإشعارات الفورية إذا كان المستخدم مسجلاً للإشعارات الفورية
        if ($user->push_token) {
            $this->notificationService->sendViaPush(
                $user,
                $notification->title,
                $notification->message,
                $notification->action_url
            );
        }
    }
}
