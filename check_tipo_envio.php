<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$envio = App\Models\Envio::find(12);

if (!$envio) {
    echo "Envío no encontrado" . PHP_EOL;
    exit(1);
}

echo "Envío #12:" . PHP_EOL;
echo "  es_publico: " . ($envio->es_publico ? 'SÍ (1)' : 'NO (0/NULL)') . PHP_EOL;
echo "  id_usuario: " . ($envio->id_usuario ?? 'NULL') . PHP_EOL;
echo "  Tipo: " . ($envio->es_publico ? 'PRODUCTOR' : 'CLIENTE') . PHP_EOL;
