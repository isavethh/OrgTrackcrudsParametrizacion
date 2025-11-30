<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DireccionController;
use App\Http\Controllers\Api\EnvioController;
use App\Http\Controllers\Api\TipoEmpaqueController;
use App\Http\Controllers\Api\UnidadMedidaController;
use App\Http\Controllers\Api\TipoVehiculoController;

// Rutas públicas (sin autenticación por ahora, se puede agregar después)

// Direcciones
Route::apiResource('direcciones', DireccionController::class);

// Envíos
Route::apiResource('envios', EnvioController::class);

// Tipos de Empaque (solo lectura)
Route::get('tipos-empaque', [TipoEmpaqueController::class, 'index']);
Route::get('tipos-empaque/{tipoEmpaque}', [TipoEmpaqueController::class, 'show']);

// Unidades de Medida (solo lectura)
Route::get('unidades-medida', [UnidadMedidaController::class, 'index']);
Route::get('unidades-medida/{unidadMedida}', [UnidadMedidaController::class, 'show']);

// Tipos de Vehículo (solo lectura)
Route::get('tipos-vehiculo', [TipoVehiculoController::class, 'index']);
Route::get('tipos-vehiculo/{tipoVehiculo}', [TipoVehiculoController::class, 'show']);

// Rutas adicionales para envíos
Route::prefix('envios')->group(function () {
    Route::get('/sistema/{sistema}', [EnvioController::class, 'index'])->where('sistema', 'agronexus|orgtrack');
    Route::get('/estado/{estado}', [EnvioController::class, 'index'])->where('estado', 'pendiente|en_transito|entregado|cancelado');
    Route::post('/{envio}/aprobar', [EnvioController::class, 'aprobar']);
    Route::post('/{envio}/rechazar', [EnvioController::class, 'rechazar']);
});

// Ruta de prueba
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Logística funcionando correctamente',
        'version' => '1.0.0'
    ]);
});
