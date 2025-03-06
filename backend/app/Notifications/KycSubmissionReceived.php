<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycSubmissionReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تم استلام طلب التحقق من الهوية (KYC)')
            ->greeting('مرحبًا ' . $notifiable->name . '!')
            ->line('نود إعلامك بأننا استلمنا طلب التحقق من الهوية الخاص بك بنجاح.')
            ->line('سيقوم فريقنا بمراجعة المستندات المقدمة في أقرب وقت ممكن.')
            ->line('سيتم إشعارك بمجرد اكتمال عملية المراجعة.')
            ->action('تتبع حالة الطلب', url('/user/kyc'))
            ->line('شكرًا لاستخدامك تطبيق أورا!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'تم استلام طلب التحقق من الهوية',
            'message' => 'تم استلام طلب التحقق من الهوية الخاص بك بنجاح وسيتم مراجعته قريبًا.',
            'icon' => 'shield-check',
            'type' => 'info',
            'action_url' => '/user/kyc',
        ];
    }
}
