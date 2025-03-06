<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Verificar países
$countries = DB::table('countries')->get();
echo "Total de países: " . $countries->count() . PHP_EOL;
foreach ($countries as $country) {
    echo "País: " . $country->name . " (" . $country->code . ")" . PHP_EOL;
}

echo PHP_EOL;

// Verificar ciudades
$cities = DB::table('cities')->get();
echo "Total de ciudades: " . $cities->count() . PHP_EOL;
foreach ($cities as $city) {
    $country = DB::table('countries')->where('id', $city->country_id)->first();
    echo "Ciudad: " . $city->name . " (País: " . $country->name . ")" . PHP_EOL;
}

echo PHP_EOL;

// Verificar usuarios
$users = DB::table('users')->get();
echo "Total de usuarios: " . $users->count() . PHP_EOL;
foreach ($users as $user) {
    echo "Usuario: " . $user->name . " (" . $user->email . ") - Tipo: " . $user->user_type . PHP_EOL;
}
