<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\VoiceCallService;

/**
 * @method static \App\Models\VoiceCall startCall(\App\Models\User $caller, array $participantIds, bool $isGroup = false)
 * @method static bool joinCall(\App\Models\VoiceCall $voiceCall, \App\Models\User $user)
 * @method static bool rejectCall(\App\Models\VoiceCall $voiceCall, \App\Models\User $user)
 * @method static bool endCall(\App\Models\VoiceCall $voiceCall, \App\Models\User $user)
 * @method static bool toggleMute(\App\Models\VoiceCall $voiceCall, \App\Models\User $user, bool $mute = true)
 * 
 * @see \App\Services\VoiceCallService
 */
class VoiceCall extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return VoiceCallService::class;
    }
}
