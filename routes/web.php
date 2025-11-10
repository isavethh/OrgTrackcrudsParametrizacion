<?php

use Illuminate\Support\Facades\Route;

// Página principal - redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación (solo vistas)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    return redirect()->route('dashboard');
})->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
    return redirect()->route('dashboard');
})->name('register.post');

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', function () {
    return back()->with('status', 'Hemos enviado un enlace de recuperación a tu email.');
})->name('password.email');

Route::get('/password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->name('password.reset');

Route::post('/password/reset', function () {
    return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida.');
})->name('password.update');

// Rutas del dashboard (sin auth para previsualizar)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Rutas de envíos
Route::get('/envios', function () {
    return view('envios.index');
})->name('envios.index');

Route::get('/envios/create', function () {
    return view('envios.create');
})->name('envios.create');

Route::get('/envios/{id}', function ($id) {
    return view('envios.show', ['id' => $id]);
})->name('envios.show');

// Rutas de direcciones
Route::get('/direcciones', function () {
    return view('direcciones.index');
})->name('direcciones.index');

Route::get('/direcciones/create', function () {
    return view('direcciones.create');
})->name('direcciones.create');

Route::get('/direcciones/{id}/edit', function ($id) {
    return view('direcciones.create', ['editId' => $id]);
})->name('direcciones.edit');

// Rutas de documentos
Route::get('/documentos', function () {
    return view('documentos.index');
})->name('documentos.index');

Route::get('/documentos/create', function () {
    return view('documentos.create');
})->name('documentos.create');

// Documentos - vistas secundarias (hardcode)
Route::get('/documentos/{id}/particiones', function ($id) {
    return view('documentos.particiones', ['id' => $id]);
})->name('documentos.particiones');

Route::get('/documentos/{id}/ver', function ($id) {
    return view('documentos.ver', ['id' => $id]);
})->name('documentos.ver');

// Rutas de Transportistas (CRUD hardcodeado - vistas placeholder)
Route::get('/transportistas', function () {
    return view('transportistas.index');
})->name('transportistas.index');

// Rutas de Vehículos (CRUD hardcodeado - vistas placeholder)
Route::get('/vehiculos', function () {
    return view('vehiculos.index');
})->name('vehiculos.index');
