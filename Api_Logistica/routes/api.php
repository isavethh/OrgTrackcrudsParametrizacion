<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DireccionController;
use App\Http\Controllers\Api\EnvioController;

// Rutas públicas (sin autenticación por ahora, se puede agregar después)

// Direcciones
Route::apiResource('direcciones', DireccionController::class);

// Envíos
Route::apiResource('envios', EnvioController::class);

// Rutas adicionales para envíos
Route::prefix('envios')->group(function () {
    Route::get('/sistema/{sistema}', [EnvioController::class, 'index'])->where('sistema', 'agronexus|orgtrack');
    Route::get('/estado/{estado}', [EnvioController::class, 'index'])->where('estado', 'pendiente|en_transito|entregado|cancelado');
});

// Ruta de prueba
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Logística funcionando correctamente',
        'version' => '1.0.0'
    ]);
});
