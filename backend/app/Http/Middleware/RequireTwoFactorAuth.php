<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\TwoFactorSession;
use Illuminate\Support\Facades\Cookie;

class RequireTwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si el usuario no tiene 2FA activado, continuar
        if (!$user || !$user->two_factor_enabled) {
            return $next($request);
        }

        // Si el usuario ya ha pasado la verificación 2FA en esta sesión
        if (Session::has('two_factor_verified') && Session::get('two_factor_verified') === true) {
            return $next($request);
        }

        // Si el dispositivo está en la lista de confianza, continuar
        if ($this->isTrustedDevice($request, $user)) {
            // Marcar la sesión como verificada
            Session::put('two_factor_verified', true);
            return $next($request);
        }

        // Si hay una sesión de 2FA válida para este usuario y sesión
        if ($this->hasValidTwoFactorSession($user)) {
            // Marcar la sesión como verificada
            Session::put('two_factor_verified', true);
            return $next($request);
        }

        // Guardar la URL actual para redirigir después de la verificación
        Session::put('url.intended', $request->fullUrl());

        // Almacenar información para el proceso de verificación 2FA
        Session::put('auth.two_factor.required', true);
        Session::put('auth.two_factor.user_id', $user->id);

        // Redirigir a la página de verificación 2FA
        return redirect()->route('two-factor.form');
    }

    /**
     * Verifica si el dispositivo actual está en la lista de confianza del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function isTrustedDevice(Request $request, $user)
    {
        $deviceId = $request->cookie('trusted_device');
        
        if (!$deviceId) {
            return false;
        }

        // Verificar si el dispositivo está en la lista de confianza del usuario
        return $user->isTrustedDevice($deviceId);
    }

    /**
     * Verifica si hay una sesión de 2FA válida para este usuario y sesión.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    protected function hasValidTwoFactorSession($user)
    {
        $session = TwoFactorSession::where('user_id', $user->id)
            ->where('session_id', Session::getId())
            ->where('expires_at', '>', now())
            ->first();

        return $session !== null;
    }
}
