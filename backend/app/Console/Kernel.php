<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CleanupTwoFactorSessions::class,
        \App\Console\Commands\CleanupOldNotifications::class,
        \App\Console\Commands\VerifyRoutes::class,
        \App\Console\Commands\UpdateCountrySettings::class,
        \App\Console\Commands\UpdateCurrencySettings::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Limpiar sesiones de autenticación de dos factores expiradas cada día
        $schedule->command('auth:cleanup-2fa-sessions')->daily();
        
        // Limpiar notificaciones antiguas cada semana
        $schedule->command('notifications:cleanup --days=30')->weekly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
