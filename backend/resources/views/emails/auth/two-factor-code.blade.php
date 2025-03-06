@component('mail::message')
# Código de verificación

@if($userName)
Hola {{ $userName }},
@else
Hola,
@endif

Tu código de verificación para acceder a tu cuenta es:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px;">
{{ $code }}
</div>
@endcomponent

Este código es válido por 10 minutos. No compartas este código con nadie.

Si no has solicitado este código, por favor ignora este mensaje o contacta a soporte si tienes alguna preocupación.

Saludos,<br>
{{ config('app.name') }}
@endcomponent
