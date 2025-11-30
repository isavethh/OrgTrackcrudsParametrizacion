<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TransportistaController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\TipoVehiculoController;
use App\Http\Controllers\EstadoVehiculoController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\RutaTiempoRealController;
use App\Http\Controllers\TipoEmpaqueController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\QRController;

// Dashboard principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// CRUD de Tipos de Usuario
Route::resource('admins', AdminController::class);
Route::resource('clientes', ClienteController::class);
Route::resource('transportistas', TransportistaController::class);

// CRUD de Vehículos
Route::resource('vehiculos', VehiculoController::class);

// CRUD de Catálogos
Route::resource('tipos-vehiculo', TipoVehiculoController::class);
Route::resource('estados-vehiculo', EstadoVehiculoController::class);
Route::resource('tipos-empaque', TipoEmpaqueController::class);
Route::resource('unidades-medida', UnidadMedidaController::class);

// CRUD de Direcciones
Route::resource('direcciones', DireccionController::class);

// CRUD de Envíos
Route::resource('envios', EnvioController::class);
Route::post('envios/{envio}/aprobar', [EnvioController::class, 'aprobar'])->name('envios.aprobar');
Route::post('envios/{envio}/rechazar', [EnvioController::class, 'rechazar'])->name('envios.rechazar');

// Rutas en Tiempo Real
Route::get('rutas-tiempo-real', [RutaTiempoRealController::class, 'index'])->name('rutas-tiempo-real.index');
Route::post('rutas-tiempo-real/{id}/start', [RutaTiempoRealController::class, 'start'])->name('rutas-tiempo-real.start');
Route::post('rutas-tiempo-real/{id}/update-location', [RutaTiempoRealController::class, 'updateLocation'])->name('rutas-tiempo-real.update-location');
Route::post('rutas-tiempo-real/{id}/complete', [RutaTiempoRealController::class, 'complete'])->name('rutas-tiempo-real.complete');
Route::get('rutas-tiempo-real/{id}/status', [RutaTiempoRealController::class, 'getStatus'])->name('rutas-tiempo-real.status');

// Códigos QR
Route::get('qr', [QRController::class, 'index'])->name('qr.index');
Route::post('qr/envios-cliente', [QRController::class, 'enviosPorCliente'])->name('qr.envios-cliente');
Route::post('qr/generar-codigo/{id}', [QRController::class, 'generarCodigoQR'])->name('qr.generar-codigo');
Route::get('qr/generar/{id}', [QRController::class, 'generarQR'])->name('qr.generar');
Route::get('qr/leer', [QRController::class, 'leerQR'])->name('qr.leer');
Route::get('qr/test-camera', function() { return view('qr.test-camera'); })->name('qr.test-camera');
Route::get('qr/documento/{codigo}', [QRController::class, 'documento'])->name('qr.documento');
Route::post('qr/buscar', [QRController::class, 'buscarPorCodigo'])->name('qr.buscar');
