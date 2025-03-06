<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * عنوان الإشعار
     *
     * @var string
     */
    protected $title;

    /**
     * نص الإشعار
     *
     * @var string
     */
    protected $message;

    /**
     * رابط الإجراء
     *
     * @var string|null
     */
    protected $actionUrl;

    /**
     * نص زر الإجراء
     *
     * @var string|null
     */
    protected $actionText;

    /**
     * إنشاء إشعار جديد
     *
     * @param string $title عنوان الإشعار
     * @param string $message نص الإشعار
     * @param string|null $actionUrl رابط الإجراء
     * @param string|null $actionText نص زر الإجراء
     * @return void
     */
    public function __construct(string $title, string $message, ?string $actionUrl = null, ?string $actionText = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText ?? 'عرض التفاصيل';
    }

    /**
     * الحصول على قنوات توصيل الإشعار
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * الحصول على تمثيل البريد الإلكتروني للإشعار
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('مرحبًا!')
            ->line($this->message);
            
        if ($this->actionUrl) {
            $mail->action($this->actionText, $this->actionUrl);
        }
        
        return $mail->line('شكرًا لاستخدامك تطبيق أورا!');
    }

    /**
     * الحصول على تمثيل مصفوفة للإشعار
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }
}
