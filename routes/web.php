<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\TipoTransporteController;
use App\Http\Controllers\TransportistaController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\TipoEmpaqueController;
use App\Http\Controllers\TamanoTransporteController;
use App\Http\Controllers\EstadoTransportistaController;

// Dashboard principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// CRUD de Usuarios
Route::resource('usuarios', UsuarioController::class);

// CRUD de Vehículos
Route::resource('vehiculos', VehiculoController::class);

// CRUD de Tipos de Transporte
Route::resource('tipo-transportes', TipoTransporteController::class);

// CRUD de Extensiones de Usuario
Route::resource('admins', AdminController::class);
Route::resource('clientes', ClienteController::class);
Route::resource('transportistas', TransportistaController::class);

// CRUD de Parámetros
Route::resource('unidades-medida', UnidadMedidaController::class);
Route::resource('tipos-empaque', TipoEmpaqueController::class);
Route::resource('tamanos-transporte', TamanoTransporteController::class);

// CRUD de Direcciones
Route::resource('direcciones', DireccionController::class);

// CRUD de Envíos
Route::resource('envios', EnvioController::class);
