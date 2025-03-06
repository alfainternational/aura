<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\NotificationSentEvent;
use App\Listeners\SendNotificationListener;
use App\Events\MessageSentEvent;
use App\Events\MessageStatusUpdatedEvent;
use App\Events\VoiceCallEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NotificationSentEvent::class => [
            SendNotificationListener::class,
        ],
        // لا نحتاج إلى مستمعين لأحداث المراسلة والمكالمات الصوتية
        // لأنها تستخدم للبث المباشر فقط
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
