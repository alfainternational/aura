<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The new KYC status.
     *
     * @var string
     */
    protected $status;

    /**
     * The rejection reason (if applicable).
     *
     * @var string|null
     */
    protected $rejectionReason;

    /**
     * Create a new notification instance.
     *
     * @param  string  $status
     * @param  string|null  $rejectionReason
     * @return void
     */
    public function __construct($status, $rejectionReason = null)
    {
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
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
        $mailMessage = (new MailMessage)
            ->greeting('مرحبًا ' . $notifiable->name . '!');

        if ($this->status === 'approved') {
            $mailMessage->subject('تم الموافقة على التحقق من هويتك')
                ->line('يسرنا إبلاغك بأنه تمت الموافقة على طلب التحقق من هويتك.')
                ->line('يمكنك الآن الوصول إلى جميع ميزات المنصة المتاحة للمستخدمين المتحقق من هويتهم.');
        } else {
            $mailMessage->subject('تحديث بشأن طلب التحقق من هويتك')
                ->line('نأسف لإبلاغك بأنه تم رفض طلب التحقق من هويتك.');

            if ($this->rejectionReason) {
                $mailMessage->line('سبب الرفض: ' . $this->rejectionReason);
            }

            $mailMessage->line('يرجى مراجعة المعلومات المقدمة وإعادة تقديم طلبك.');
        }

        return $mailMessage
            ->action('عرض تفاصيل التحقق', url('/user/kyc'))
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
        $data = [
            'status' => $this->status,
            'action_url' => '/user/kyc',
        ];

        if ($this->status === 'approved') {
            $data['title'] = 'تم الموافقة على التحقق من هويتك';
            $data['message'] = 'تمت الموافقة على طلب التحقق من هويتك. يمكنك الآن الوصول إلى جميع ميزات المنصة.';
            $data['icon'] = 'check-circle';
            $data['type'] = 'success';
        } else {
            $data['title'] = 'تم رفض طلب التحقق من هويتك';
            $data['message'] = 'تم رفض طلب التحقق من هويتك. يرجى مراجعة التفاصيل وإعادة التقديم.';
            $data['icon'] = 'x-circle';
            $data['type'] = 'danger';
            
            if ($this->rejectionReason) {
                $data['rejection_reason'] = $this->rejectionReason;
            }
        }

        return $data;
    }
}
