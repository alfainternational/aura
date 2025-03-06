<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\KycVerification;

class KycStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The KYC verification record.
     *
     * @var \App\Models\KycVerification
     */
    protected $verification;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\KycVerification  $verification
     * @return void
     */
    public function __construct(KycVerification $verification)
    {
        $this->verification = $verification;
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
        $message = (new MailMessage)
            ->subject('تحديث حالة التحقق من الهوية (KYC)')
            ->greeting('مرحبًا ' . $notifiable->name . '!');

        if ($this->verification->status === 'approved') {
            $message->line('نود إبلاغك بأنه تمت الموافقة على طلب التحقق من الهوية الخاص بك.')
                ->line('يمكنك الآن الوصول إلى جميع ميزات منصة أورا.')
                ->action('عرض حالة التحقق', url('/user/kyc'));
        } elseif ($this->verification->status === 'rejected') {
            $message->line('نأسف لإبلاغك بأنه تم رفض طلب التحقق من الهوية الخاص بك.')
                ->line('سبب الرفض: ' . $this->verification->rejection_reason)
                ->line('يرجى مراجعة المعلومات وإعادة تقديم الطلب.')
                ->action('إعادة تقديم الطلب', url('/user/kyc'));
        } else {
            $message->line('تم تحديث حالة طلب التحقق من الهوية الخاص بك إلى "قيد المراجعة".')
                ->line('سنقوم بإبلاغك بمجرد اكتمال المراجعة.')
                ->action('عرض حالة التحقق', url('/user/kyc'));
        }

        return $message->line('شكرًا لاستخدامك منصة أورا!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $messages = [
            'approved' => 'تمت الموافقة على طلب التحقق من الهوية الخاص بك.',
            'rejected' => 'تم رفض طلب التحقق من الهوية الخاص بك. سبب الرفض: ' . $this->verification->rejection_reason,
            'pending' => 'تم تحديث حالة طلب التحقق من الهوية الخاص بك إلى "قيد المراجعة".'
        ];

        return [
            'title' => 'تحديث حالة التحقق من الهوية',
            'message' => $messages[$this->verification->status],
            'status' => $this->verification->status,
            'action_url' => '/user/kyc',
            'icon' => $this->verification->status === 'approved' ? 'check-circle' : 
                     ($this->verification->status === 'rejected' ? 'x-circle' : 'clock'),
        ];
    }
}
