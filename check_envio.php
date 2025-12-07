<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$idEnvio = 12;

echo "Verificando Envío #$idEnvio:" . PHP_EOL;
echo "=" . str_repeat("=", 60) . PHP_EOL;

$envio = App\Models\Envio::with(['asignaciones.estadoAsignacion'])->find($idEnvio);

if (!$envio) {
    echo "Envío no encontrado" . PHP_EOL;
    exit(1);
}

// Obtener estado actual del envío
$estadoActual = App\Http\Controllers\Api\Helpers\EstadoHelper::obtenerEstadoActualEnvio($idEnvio);
echo "Estado actual en historial: " . ($estadoActual ?? 'NULL') . PHP_EOL;
echo PHP_EOL;

// Mostrar asignaciones
echo "Asignaciones:" . PHP_EOL;
foreach ($envio->asignaciones as $asignacion) {
    $estado = $asignacion->estadoAsignacion?->nombre ?? 'NULL';
    echo "  - Asignación #{$asignacion->id}: $estado" . PHP_EOL;
}

echo PHP_EOL;
echo "Total asignaciones: " . $envio->asignaciones->count() . PHP_EOL;

// Verificar historial completo
$historial = App\Models\HistorialEstados::where('id_envio', $idEnvio)
    ->with('estadoEnvio')
    ->orderBy('fecha', 'desc')
    ->get();

echo PHP_EOL;
echo "Historial de estados completo:" . PHP_EOL;
foreach ($historial as $h) {
    echo "  - " . $h->estadoEnvio->nombre . " (" . $h->fecha . ")" . PHP_EOL;
}
