<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\UserService;

/**
 * @method static \App\Models\User createUser(array $data)
 * @method static \App\Models\User updateUser(\App\Models\User $user, array $data)
 * @method static \App\Models\User updateAvatar(\App\Models\User $user, \Illuminate\Http\UploadedFile $avatar)
 * @method static \App\Models\User toggleTwoFactorAuth(\App\Models\User $user, bool $enable = true)
 * @method static \App\Models\UserNotification addNotification(\App\Models\User $user, string $title, string $message, string $type = 'info', ?string $icon = null, ?string $actionUrl = null, ?array $data = null)
 * @method static \App\Models\KycVerification submitKycVerification(\App\Models\User $user, array $data)
 * @method static \App\Models\KycVerification updateKycStatus(\App\Models\KycVerification $verification, string $status, ?string $notes = null, \App\Models\User $updatedBy)
 * 
 * @see \App\Services\UserService
 */
class User extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return UserService::class;
    }
}
