<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Middleware Aliases
    |--------------------------------------------------------------------------
    |
    | This array contains the aliases for middleware that can be used in routes.
    |
    */
    'aliases' => [
        'checkrole' => \App\Http\Middleware\SimpleRoleMiddleware::class,
        'role' => \App\Http\Middleware\SimpleRoleMiddleware::class,
    ],
];
