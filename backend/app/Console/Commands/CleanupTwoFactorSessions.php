<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TwoFactorSession;
use Illuminate\Support\Facades\Log;

class CleanupTwoFactorSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:cleanup-2fa-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia las sesiones de autenticación de dos factores expiradas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de sesiones de autenticación de dos factores expiradas...');
        
        try {
            // Obtener el número de sesiones antes de la limpieza
            $totalSessions = TwoFactorSession::count();
            $this->info("Total de sesiones antes de la limpieza: {$totalSessions}");
            
            // Eliminar sesiones expiradas
            $deleted = TwoFactorSession::where('expires_at', '<', now())->delete();
            
            // Registrar resultado
            $this->info("Se han eliminado {$deleted} sesiones expiradas.");
            Log::info("Limpieza de sesiones 2FA: {$deleted} sesiones expiradas eliminadas.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error al limpiar sesiones: {$e->getMessage()}");
            Log::error("Error al limpiar sesiones 2FA: {$e->getMessage()}");
            
            return Command::FAILURE;
        }
    }
}
