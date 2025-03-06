<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\NotificationService;

/**
 * @method static \App\Models\UserNotification send(\App\Models\User $user, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
 * @method static bool sendViaEmail(\App\Models\User $user, string $subject, string $message, ?string $actionUrl = null, ?\App\Models\NotificationChannel $channel = null)
 * @method static bool sendViaSms(\App\Models\User $user, string $message, ?\App\Models\NotificationChannel $channel = null)
 * @method static bool sendViaPush(\App\Models\User $user, string $title, string $message, ?string $actionUrl = null, ?\App\Models\NotificationChannel $channel = null)
 * @method static bool sendKycVerificationNotification(\App\Models\KycVerification $kycVerification, string $status)
 * 
 * @see \App\Services\NotificationService
 */
class Notification extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return NotificationService::class;
    }
}
