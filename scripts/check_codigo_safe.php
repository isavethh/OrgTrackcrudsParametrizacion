<?php
// Ejecutar desde el root del proyecto: php scripts/check_codigo_safe.php IJZRI27V
if ($argc < 2) {
    echo "Usage: php scripts/check_codigo_safe.php <codigo>\n";
    exit(1);
}
$codigo = $argv[1];
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $asignacion = App\Models\AsignacionMultiple::with([
        'envio.usuario.persona:id,nombre,apellido',
        'envio.direccion:id,nombreorigen,nombredestino',
        'vehiculo.tipoVehiculo:id,nombre',
        'vehiculo:id,placa',
        'estadoAsignacion:id,nombre',
        'transportista.usuario.persona:id,ci,telefono',
    ])->where('codigo_acceso', $codigo)->first();

    if (!$asignacion) {
        echo "NO_FOUND\n";
        exit(0);
    }

    echo "FOUND\n";
    echo "id: " . $asignacion->id . "\n";
    echo "codigo: " . $asignacion->codigo_acceso . "\n";
    echo "cliente.nombre: " . ($asignacion->envio->usuario?->persona?->nombre ?? 'NULL') . "\n";
    echo "cliente.apellido: " . ($asignacion->envio->usuario?->persona?->apellido ?? 'NULL') . "\n";
    echo "transportista.ci: " . ($asignacion->transportista?->usuario?->persona?->ci ?? 'NULL') . "\n";
    echo "transportista.telefono: " . ($asignacion->transportista?->usuario?->persona?->telefono ?? 'NULL') . "\n";

    echo "---JSON---\n";
    echo json_encode([
        'id' => $asignacion->id,
        'codigo_acceso' => $asignacion->codigo_acceso,
        'cliente' => [
            'nombre' => $asignacion->envio->usuario?->persona?->nombre,
            'apellido' => $asignacion->envio->usuario?->persona?->apellido,
        ],
        'transportista' => [
            'ci' => $asignacion->transportista?->usuario?->persona?->ci,
            'telefono' => $asignacion->transportista?->usuario?->persona?->telefono,
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

} catch (Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(2);
}
