<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApplyUserUiSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Obtener configuraciones de UI o crear si no existen
            $uiSettings = $user->uiSettings ?? $user->uiSettings()->create();
            
            // Compartir las configuraciones con todas las vistas
            view()->share('userUiSettings', $uiSettings);
            
            // Establecer una cookie para JavaScript
            $response = $next($request);
            
            if (method_exists($response, 'cookie')) {
                // Establecer cookies para tema y esquema de color
                $theme = $uiSettings->theme ?? 'light';
                $colorScheme = $uiSettings->color_scheme ?? 'default';
                
                $response->cookie('aura_theme', $theme, 60 * 24 * 365, null, null, false, false);
                $response->cookie('aura_color_scheme', $colorScheme, 60 * 24 * 365, null, null, false, false);
            }
            
            return $response;
        }
        
        return $next($request);
    }
}
