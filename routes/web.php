<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\TipoTransporteController;
use App\Http\Controllers\CargaController;
use App\Http\Controllers\TransportistaController;

// Dashboard principal
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// CRUD de Usuarios
Route::resource('usuarios', UsuarioController::class);

// CRUD de Vehículos
Route::resource('vehiculos', VehiculoController::class);

// CRUD de Tipos de Transporte
Route::resource('tipo-transportes', TipoTransporteController::class);

// CRUD de Cargas
Route::resource('cargas', CargaController::class);

// CRUD de Transportistas
Route::resource('transportistas', TransportistaController::class);
