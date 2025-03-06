<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Country;

class CountrySettingsMiddleware
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
        // Obtener el país predeterminado (Sudán)
        $defaultCountry = Country::getDefaultCountry();
        
        // Compartir datos con todas las vistas
        View::share('defaultCountry', $defaultCountry);
        View::share('defaultCurrency', $defaultCountry ? $defaultCountry->currency : 'SDG');
        View::share('defaultCurrencySymbol', $defaultCountry ? $defaultCountry->currency_symbol : 'ج.س');
        
        // Establecer las configuraciones de aplicación
        config(['app.country' => $defaultCountry ? $defaultCountry->code : 'SD']);
        config(['app.currency' => $defaultCountry ? $defaultCountry->currency : 'SDG']);
        
        // Obtener los países permitidos para registro
        $allowedCountries = Country::getRegistrationAllowedCountries();
        View::share('allowedCountries', $allowedCountries);
        
        return $next($request);
    }
}
