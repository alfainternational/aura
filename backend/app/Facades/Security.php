<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\Security\SecurityService;

/**
 * @method static int logFailedAttempt(string $type, string $identifier, ?\Illuminate\Http\Request $request = null)
 * @method static bool isIpBlocked(string $ip)
 * @method static void blockIp(string $ip, string $reason = null, int $duration = null)
 * @method static void unblockIp(string $ip)
 * @method static \App\Models\SecurityIncident logSecurityIncident(string $type, string $description, array $data = [], string $ip = null, int $userId = null, string $severity = 'medium')
 * @method static bool analyzeRequest(\Illuminate\Http\Request $request)
 * @method static array detectAnomalies(\App\Models\User $user, \Illuminate\Http\Request $request = null)
 * @method static array getSecurityStatistics(int $days = 30)
 * 
 * @see \App\Services\Security\SecurityService
 */
class Security extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SecurityService::class;
    }
}
