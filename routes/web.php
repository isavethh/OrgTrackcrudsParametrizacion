<?php

use Illuminate\Support\Facades\Route;

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

// Rutas del dashboard 
Route::get('/dashboard', function () {
    return view('cliente.dashboard');
})->name('dashboard');

// Rutas de envíos
Route::get('/envios', function () {
    return view('cliente.envios.index');
})->name('envios.index');

Route::get('/envios/create', function () {
    return view('cliente.envios.create');
})->name('envios.create');

Route::get('/envios/{id}', function ($id) {
    return view('cliente.envios.show', ['id' => $id]);
})->name('envios.show');

// Rutas de direcciones
Route::get('/direcciones', function () {
    return view('cliente.direcciones.index');
})->name('direcciones.index');

Route::get('/direcciones/create', function () {
    return view('cliente.direcciones.create');
})->name('direcciones.create');

Route::get('/direcciones/{id}/edit', function ($id) {
    return view('cliente.direcciones.create', ['editId' => $id]);
})->name('direcciones.edit');

// Rutas de documentos
Route::get('/documentos', function () {
    return view('cliente.documentos.index');
})->name('documentos.index');

// ============================================
// RUTAS ADMIN (prefijo /admin)
// ============================================
Route::prefix('admin')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Rutas de envíos admin
    Route::get('/envios', function () {
        return view('admin.envios.index');
    })->name('admin.envios.index');

    Route::get('/envios/create', function () {
        return view('admin.envios.create');
    })->name('admin.envios.create');

    Route::get('/envios/{id}', function ($id) {
        return view('admin.envios.show', ['id' => $id]);
    })->name('admin.envios.show');

    // Rutas de direcciones admin
    Route::get('/direcciones', function () {
        return view('admin.direcciones.index');
    })->name('admin.direcciones.index');

    Route::get('/direcciones/create', function () {
        return view('admin.direcciones.create');
    })->name('admin.direcciones.create');

    Route::get('/direcciones/{id}/edit', function ($id) {
        return view('admin.direcciones.create', ['editId' => $id]);
    })->name('admin.direcciones.edit');

    // Rutas de documentos admin
    Route::get('/documentos', function () {
        return view('admin.documentos.index');
    })->name('admin.documentos.index');

    Route::get('/documentos/cliente/{id_cliente}', function ($id_cliente) {
        return view('admin.documentos.cliente', ['id_cliente' => $id_cliente]);
    })->name('admin.documentos.cliente');

    Route::get('/documentos/create', function () {
        return view('admin.documentos.create');
    })->name('admin.documentos.create');

    Route::get('/documentos/{id}/particiones', function ($id) {
        return view('admin.documentos.particiones', ['id' => $id]);
    })->name('admin.documentos.particiones');

    Route::get('/documentos/{id}/ver', function ($id) {
        return view('admin.documentos.ver', ['id' => $id]);
    })->name('admin.documentos.ver');

    // Rutas de transportistas admin
    Route::get('/transportistas', function () {
        return view('admin.transportistas.index');
    })->name('admin.transportistas.index');

    // Rutas de vehículos admin
    Route::get('/vehiculos', function () {
        return view('admin.vehiculos.index');
    })->name('admin.vehiculos.index');

    // Rutas de usuarios admin
    Route::get('/usuarios', function () {
        return view('admin.usuarios.index');
    })->name('admin.usuarios.index');

    // Catálogo de condiciones
    Route::get('/condiciones', function () {
        return view('admin.condiciones.index');
    })->name('admin.condiciones.index');
    
    // Catálogo de incidentes
    Route::get('/incidentes', function () {
        return view('admin.incidentes.index');
    })->name('admin.incidentes.index');

    // Unidades de medida
    Route::get('/unidades-medida', function () {
        return view('admin.unidades_medida.index');
    })->name('admin.unidades_medida.index');

    // Catálogo de carga
    Route::get('/catalogo-carga', function () {
        return view('admin.catalogo_carga.index');
    })->name('admin.catalogo_carga.index');
});
