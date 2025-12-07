<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Estados de Asignación:" . PHP_EOL;
$estados = DB::table('estados_asignacion_multiple')->pluck('nombre');
foreach ($estados as $estado) {
    echo "  - '$estado'" . PHP_EOL;
}
