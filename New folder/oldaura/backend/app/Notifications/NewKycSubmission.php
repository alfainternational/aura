<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewKycSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user who submitted the KYC verification.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            ->subject('طلب تحقق جديد من الهوية (KYC)')
            ->greeting('مرحبًا!')
            ->line('تم استلام طلب تحقق جديد من الهوية (KYC) من المستخدم ' . $this->user->name . '.')
            ->line('يرجى مراجعة الطلب في لوحة التحكم.')
            ->action('عرض الطلب', url('/admin/kyc-verifications/' . $this->user->id))
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'message' => 'تم استلام طلب تحقق جديد من الهوية (KYC) من المستخدم ' . $this->user->name . '.',
            'action_url' => '/admin/kyc-verifications/' . $this->user->id,
        ];
    }
}
