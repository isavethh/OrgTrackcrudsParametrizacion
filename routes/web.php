<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Sistema de Gestión de Envíos y Logística - OrgTrack
| 
| 
|
*/

// ============================================================================
// API CONTROLLERS
// ============================================================================
use App\Http\Controllers\Api\EnvioPublicoController;
use App\Http\Controllers\Api\EnvioController;
use App\Http\Controllers\Api\TipotransporteController;
use App\Http\Controllers\Api\CatalogoCategoriaController;
use App\Http\Controllers\Api\CatalogoProductoController;
use App\Http\Controllers\Api\CatalogoTipoEmpaqueController;
use App\Http\Controllers\Api\CatalogoTamanoConteoController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\TiposVehiculoController;
use App\Http\Controllers\Api\TransportistaController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\CondicionTransporteController;
use App\Http\Controllers\Api\TipoIncidenteTransporteController;
use App\Http\Controllers\Api\FirmaController;
use App\Http\Controllers\Api\QrController;

// ============================================================================
// AUTENTICACIÓN
// ============================================================================
use App\Http\Controllers\Web\AuthWebController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');

Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

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

// ============================================================================
// MÓDULO: DASHBOARD CLIENTE 
// ============================================================================
Route::group([], function () {
    Route::get('/dashboard', function () {
        return view('cliente.dashboard');
    })->name('dashboard');

    // ────────────────────────────────────────────────────────────────────────
    // Envíos Cliente
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/envios', function () {
        return view('cliente.envios.index');
    })->name('envios.index');

    Route::get('/envios/create', function () {
        return view('cliente.envios.create');
    })->name('envios.create');

    Route::get('/envios/{id}', function ($id) {
        return view('cliente.envios.show', ['id' => $id]);
    })->name('envios.show');

    // ────────────────────────────────────────────────────────────────────────
    // Direcciones Cliente
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/direcciones', function () {
        return view('cliente.direcciones.index');
    })->name('direcciones.index');

    Route::get('/direcciones/create', function () {
        return view('cliente.direcciones.create');
    })->name('direcciones.create');

    Route::get('/direcciones/{id}/edit', function ($id) {
        return view('cliente.direcciones.create', ['editId' => $id]);
    })->name('direcciones.edit');

    // ────────────────────────────────────────────────────────────────────────
    // Documentos Cliente
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/documentos', function () {
        return view('cliente.documentos.index');
    })->name('documentos.index');

    // ────────────────────────────────────────────────────────────────────────
    // Centro de Soporte Cliente
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/helpdesk', function () {
        return view('cliente.helpdesk.index');
    })->name('cliente.helpdesk');
});

// ============================================================================
// MÓDULO: PANEL ADMINISTRADOR 
// ============================================================================
Route::prefix('admin')->group(function () {

    // ────────────────────────────────────────────────────────────────────────
    // Dashboard Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('admin.dashboard.stats');

    // ────────────────────────────────────────────────────────────────────────
    // Envíos Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/envios', function () {
        return view('admin.envios.index');
    })->name('admin.envios.index');

    Route::get('/envios/create', function () {
        return view('admin.envios.create');
    })->name('admin.envios.create');

    Route::get('/envios/{id}', function ($id) {
        return view('admin.envios.show', ['id' => $id]);
    })->name('admin.envios.show');

    // ────────────────────────────────────────────────────────────────────────
    // Direcciones Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/direcciones', function () {
        return view('admin.direcciones.index');
    })->name('admin.direcciones.index');

    Route::get('/direcciones/create', function () {
        return view('admin.direcciones.create');
    })->name('admin.direcciones.create');

    Route::get('/direcciones/{id}/edit', function ($id) {
        return view('admin.direcciones.create', ['editId' => $id]);
    })->name('admin.direcciones.edit');

    // ────────────────────────────────────────────────────────────────────────
    // Documentos Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/documentos', function () {
        return view('admin.documentos.index');
    })->name('admin.documentos.index');

    Route::get('/documentos/cliente/{id_cliente}', function ($id_cliente) {
        return view('admin.documentos.cliente', ['id_cliente' => $id_cliente]);
    })->name('admin.documentos.cliente');

    Route::get('/documentos/productor/{id_envio}', function ($id_envio) {
        return view('admin.documentos.productor', ['id_envio' => $id_envio]);
    })->name('admin.documentos.productor');

    Route::get('/documentos/create', function () {
        return view('admin.documentos.create');
    })->name('admin.documentos.create');

    // ────────────────────────────────────────────────────────────────────────
    // Transportistas Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/transportistas', function () {
        return view('admin.transportistas.index');
    })->name('admin.transportistas.index');

    // ────────────────────────────────────────────────────────────────────────
    // Vehículos Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/vehiculos', function () {
        return view('admin.vehiculos.index');
    })->name('admin.vehiculos.index');

    // ────────────────────────────────────────────────────────────────────────
    // Usuarios Admin
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/usuarios', function () {
        return view('admin.usuarios.index');
    })->name('admin.usuarios.index');

    // ────────────────────────────────────────────────────────────────────────
    // Catálogos de Condiciones e Incidentes
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/condiciones', function () {
        return view('admin.condiciones.index');
    })->name('admin.condiciones.index');

    Route::get('/incidentes', function () {
        return view('admin.incidentes.index');
    })->name('admin.incidentes.index');

    // ────────────────────────────────────────────────────────────────────────
    // Catálogos de Productos
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/categorias', function () {
        return view('admin.categorias.index');
    })->name('admin.categorias.index');

    Route::get('/productos', function () {
        return view('admin.productos.index');
    })->name('admin.productos.index');

    Route::get('/tipos-empaque', function () {
        return view('admin.tipos_empaque.index');
    })->name('admin.tipos_empaque.index');

    // ────────────────────────────────────────────────────────────────────────
    // Catálogos de Vehículos y Transporte
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/tipos-vehiculo', function () {
        return view('admin.tipos_vehiculo.index');
    })->name('admin.tipos_vehiculo.index');

    Route::get('/tipos-transporte', function () {
        return view('admin.tipos_transporte.index');
    })->name('admin.tipos_transporte.index');

    Route::get('/catalogo-tamano-conteo', function () {
        return view('admin.tamano_conteo.index');
    })->name('admin.tamano_conteo.index');

    // ────────────────────────────────────────────────────────────────────────
    // Gestión de Roles (Spatie)
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/roles', [\App\Http\Controllers\Web\RolesWebController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles/{id}/asignar', [\App\Http\Controllers\Web\RolesWebController::class, 'asignarRol'])->name('admin.roles.asignar');
    Route::get('/roles/{id}/verificar', [\App\Http\Controllers\Web\RolesWebController::class, 'verificarRoles'])->name('admin.roles.verificar');

    // ────────────────────────────────────────────────────────────────────────
    // Reportes (9 reportes funcionales)
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/reportes', [\App\Http\Controllers\Admin\ReporteController::class, 'index'])->name('admin.reportes.index');
    Route::get('/reportes/envios-estado', [\App\Http\Controllers\Admin\ReporteController::class, 'enviosPorEstado'])->name('admin.reportes.envios_estado');
    Route::get('/reportes/envios-transportista', [\App\Http\Controllers\Admin\ReporteController::class, 'enviosPorTransportista'])->name('admin.reportes.envios_transportista');
    Route::get('/reportes/productos-enviados', [\App\Http\Controllers\Admin\ReporteController::class, 'productosMasEnviados'])->name('admin.reportes.productos_enviados');
    Route::get('/reportes/usuarios-rol', [\App\Http\Controllers\Admin\ReporteController::class, 'usuariosPorRol'])->name('admin.reportes.usuarios_rol');
    Route::get('/reportes/vehiculos-tipo', [\App\Http\Controllers\Admin\ReporteController::class, 'vehiculosPorTipo'])->name('admin.reportes.vehiculos_tipo');
    Route::get('/reportes/envios-mes', [\App\Http\Controllers\Admin\ReporteController::class, 'enviosPorMes'])->name('admin.reportes.envios_mes');
    Route::get('/reportes/envio-detallado', [\App\Http\Controllers\Admin\ReporteController::class, 'envioDetallado'])->name('admin.reportes.envio_detallado');
    Route::get('/reportes/tipos-empaque', [\App\Http\Controllers\Admin\ReporteController::class, 'tiposEmpaque'])->name('admin.reportes.tipos_empaque');
    Route::get('/reportes/tamano-conteo', [\App\Http\Controllers\Admin\ReporteController::class, 'tamanoConteo'])->name('admin.reportes.tamano_conteo');
    Route::get('/reportes/tamano-conteo', [\App\Http\Controllers\Admin\ReporteController::class, 'tamanoConteo'])->name('admin.reportes.tamano_conteo');

    // ────────────────────────────────────────────────────────────────────────
    // Helpdesk Widget (Integración Manual / Nuclear Option)
    // ────────────────────────────────────────────────────────────────────────

    // 1. Endpoint API para generar URL SSO (SSO backend-to-backend)
    Route::get('/api/helpdesk/sso-url', [\App\Http\Controllers\Admin\HelpdeskIntegrationController::class, 'generateUrl']);

    // 2. Vista Principal (SPA Wrapper)
    Route::get('/helpdesk', [\App\Http\Controllers\Admin\HelpdeskIntegrationController::class, 'index'])->name('helpdesk');
});

// ============================================================================
// RUTA PÚBLICA: VALIDACIÓN QR
// ============================================================================
Route::get('/validar-qr/{token?}', function ($token = null) {
    return view('validar-qr', ['token' => $token]);
})->name('validar-qr');

// ============================================================================
// MÓDULO: WEB API ROUTES 
// ============================================================================
Route::prefix('web-api')->group(function () {

    // ────────────────────────────────────────────────────────────────────────
    // Envíos Públicos (Productores) - sin autenticación
    // ────────────────────────────────────────────────────────────────────────
    Route::post('/public/direccion', [EnvioPublicoController::class, 'crearDireccionProductor']);
    Route::post('/public/envios', [EnvioPublicoController::class, 'crearEnvioProductor']);
    Route::post('/public/envios/from-material-request', [EnvioPublicoController::class, 'crearEnvioDesdeMateriaPrima']);
    Route::get('/public/envios/all', [EnvioPublicoController::class, 'listarTodosEnviosPublicos']);
    Route::get('/public/envios/{id}/seguimiento', [EnvioPublicoController::class, 'obtenerEnvioPublicoPorId']);
    Route::get('/public/envios', [EnvioPublicoController::class, 'listarEnviosProductores']);
    Route::get('/public/envios/{id_envio}/documento', [EnvioPublicoController::class, 'obtenerDocumentoProductor']);

    // ────────────────────────────────────────────────────────────────────────
    // Envíos (Admin/Cliente)
    // ────────────────────────────────────────────────────────────────────────
    Route::post('/envios/completo', [EnvioController::class, 'crearEnvioCompleto']);
    Route::post('/envios/completo-admin', [EnvioController::class, 'crearEnvioCompletoAdmin']);
    Route::get('/envios/mis-envios', [EnvioController::class, 'obtenerMisEnvios']);
    Route::get('/envios/usuario/{id_usuario}', [EnvioController::class, 'obtenerEnviosPorUsuario']);
    Route::get('/envios/transportista/asignados', [EnvioController::class, 'obtenerEnviosAsignadosTransportista']);
    Route::get('/envios/particiones/en-curso', [EnvioController::class, 'obtenerParticionesEnCursoCliente']);
    Route::put('/envios/asignacion/{id_asignacion}/asignar', [EnvioController::class, 'asignarTransportistaYVehiculoAParticion']);
    Route::post('/envios/asignacion/{id_asignacion}/iniciar', [EnvioController::class, 'iniciarViaje']);
    Route::post('/envios/asignacion/{id_asignacion}/finalizar', [EnvioController::class, 'finalizarEnvio']);
    Route::post('/envios/asignacion/{id_asignacion}/checklist-condiciones', [EnvioController::class, 'registrarChecklistCondiciones']);
    Route::post('/envios/asignacion/{id_asignacion}/checklist-incidentes', [EnvioController::class, 'registrarChecklistIncidentes']);
    Route::get('/envios/asignacion/{id_asignacion}/documento', [EnvioController::class, 'generarDocumentoParticion']);
    Route::get('/envios/documentos/asignacion/{id_asignacion}', [EnvioController::class, 'generarDocumentoParticion']);
    Route::get('/envios', [EnvioController::class, 'obtenerTodos']);
    Route::get('/envios/{id}', [EnvioController::class, 'obtenerPorId']);
    Route::put('/envios/{id_envio}/asignar', [EnvioController::class, 'asignarTransportistaYVehiculo']);
    Route::delete('/envios/{id_envio}/cancelar', [EnvioController::class, 'cancelarEnvio']);
    Route::get('/envios/{id_envio}/documento', [EnvioController::class, 'generarDocumentoEnvio']);
    Route::put('/envios/{id_envio}/estado-global', [EnvioController::class, 'actualizarEstadoGlobalEnvio']);

    // ────────────────────────────────────────────────────────────────────────
    // Vehículos
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/vehiculos', [VehiculoController::class, 'index']);
    Route::get('/vehiculos/{id}', [VehiculoController::class, 'show']);
    Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::put('/vehiculos/{id}', [VehiculoController::class, 'update']);
    Route::delete('/vehiculos/{id}', [VehiculoController::class, 'destroy']);

    // ────────────────────────────────────────────────────────────────────────
    // Tipos de Vehículo
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/tipos-vehiculo', [TiposVehiculoController::class, 'index']);
    Route::post('/tipos-vehiculo', [TiposVehiculoController::class, 'store']);
    Route::put('/tipos-vehiculo/{id}', [TiposVehiculoController::class, 'update']);
    Route::delete('/tipos-vehiculo/{id}', [TiposVehiculoController::class, 'destroy']);

    // ────────────────────────────────────────────────────────────────────────
    // Tipos de Transporte
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/tipo-transporte', [TipotransporteController::class, 'index']);
    Route::get('/tipotransporte', [TipotransporteController::class, 'index']);
    Route::post('/tipotransporte', [TipotransporteController::class, 'store']);
    Route::put('/tipotransporte/{id}', [TipotransporteController::class, 'update']);
    Route::delete('/tipotransporte/{id}', [TipotransporteController::class, 'destroy']);

    // ────────────────────────────────────────────────────────────────────────
    // Transportistas
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/transportistas', [TransportistaController::class, 'obtenerTodos']);
    Route::get('/transportistas/{id}', [TransportistaController::class, 'obtenerPorId'])->whereNumber('id');
    Route::post('/transportistas', [TransportistaController::class, 'crear']);
    Route::put('/transportistas/{id}', [TransportistaController::class, 'editar'])->whereNumber('id');
    Route::delete('/transportistas/{id}', [TransportistaController::class, 'eliminar'])->whereNumber('id');
    Route::post('/transportistas/completo', [TransportistaController::class, 'crearTransportistaCompleto']);
    Route::get('/transportistas/estado/{estado}', [TransportistaController::class, 'obtenerPorEstado']);
    Route::get('/transportistas/disponibles', [TransportistaController::class, 'obtenerDisponibles']);

    // ────────────────────────────────────────────────────────────────────────
    // Usuarios
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/usuarios', [UsuarioController::class, 'obtenerTodos']);
    Route::post('/usuarios', [UsuarioController::class, 'crear']);
    Route::get('/usuarios/clientes', [UsuarioController::class, 'obtenerClientes']);
    Route::get('/usuarios/rol/{rol}', [UsuarioController::class, 'obtenerPorRol']);
    Route::put('/usuarios/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'obtenerPorId']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'editar']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminar']);

    // ────────────────────────────────────────────────────────────────────────
    // Condiciones de Transporte (Checklist)
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/condiciones-transporte', [CondicionTransporteController::class, 'index']);
    Route::post('/condiciones-transporte', [CondicionTransporteController::class, 'store']);
    Route::put('/condiciones-transporte/{id}', [CondicionTransporteController::class, 'update'])->whereNumber('id');
    Route::delete('/condiciones-transporte/{id}', [CondicionTransporteController::class, 'destroy'])->whereNumber('id');

    // ────────────────────────────────────────────────────────────────────────
    // Tipos de Incidente (Checklist)
    // ────────────────────────────────────────────────────────────────────────
    Route::get('/tipos-incidente-transporte', [TipoIncidenteTransporteController::class, 'index']);
    Route::post('/tipos-incidente-transporte', [TipoIncidenteTransporteController::class, 'store']);
    Route::put('/tipos-incidente-transporte/{id}', [TipoIncidenteTransporteController::class, 'update'])->whereNumber('id');
    Route::delete('/tipos-incidente-transporte/{id}', [TipoIncidenteTransporteController::class, 'destroy'])->whereNumber('id');

    // ────────────────────────────────────────────────────────────────────────
    // Catálogos
    // ────────────────────────────────────────────────────────────────────────
    // Categorías
    Route::get('/catalogo-categorias', [CatalogoCategoriaController::class, 'index']);
    Route::get('/catalogo-categorias/{id}', [CatalogoCategoriaController::class, 'show']);
    Route::post('/catalogo-categorias', [CatalogoCategoriaController::class, 'store']);
    Route::put('/catalogo-categorias/{id}', [CatalogoCategoriaController::class, 'update']);
    Route::delete('/catalogo-categorias/{id}', [CatalogoCategoriaController::class, 'destroy']);

    // Productos
    Route::get('/catalogo-productos', [CatalogoProductoController::class, 'index']);
    Route::get('/catalogo-productos/{id}', [CatalogoProductoController::class, 'show']);
    Route::post('/catalogo-productos', [CatalogoProductoController::class, 'store']);
    Route::put('/catalogo-productos/{id}', [CatalogoProductoController::class, 'update']);
    Route::delete('/catalogo-productos/{id}', [CatalogoProductoController::class, 'destroy']);

    // Tipos de Empaque
    Route::get('/catalogo-tipos-empaque', [CatalogoTipoEmpaqueController::class, 'index']);
    Route::get('/catalogo-tipos-empaque/{id}', [CatalogoTipoEmpaqueController::class, 'show']);
    Route::post('/catalogo-tipos-empaque', [CatalogoTipoEmpaqueController::class, 'store']);
    Route::put('/catalogo-tipos-empaque/{id}', [CatalogoTipoEmpaqueController::class, 'update']);
    Route::delete('/catalogo-tipos-empaque/{id}', [CatalogoTipoEmpaqueController::class, 'destroy']);

    // Tamaño Conteo
    Route::get('/catalogo-tamano-conteo', [CatalogoTamanoConteoController::class, 'index']);
    Route::get('/catalogo-tamano-conteo/{id}', [CatalogoTamanoConteoController::class, 'show']);
    Route::post('/catalogo-tamano-conteo', [CatalogoTamanoConteoController::class, 'store']);
    Route::put('/catalogo-tamano-conteo/{id}', [CatalogoTamanoConteoController::class, 'update']);
    Route::delete('/catalogo-tamano-conteo/{id}', [CatalogoTamanoConteoController::class, 'destroy']);

    // ────────────────────────────────────────────────────────────────────────
    // Firmas
    // ────────────────────────────────────────────────────────────────────────
    Route::post('/firmas/envio/{id_asignacion}', [FirmaController::class, 'guardarFirmaEnvio']);
    Route::post('/firmas/transportista/{id_asignacion}', [FirmaController::class, 'guardarFirmaTransportista']);
    Route::get('/firmas/envio/{id_asignacion}', [FirmaController::class, 'obtenerFirmaEnvio']);
    Route::get('/firmas/transportista/{id_asignacion}', [FirmaController::class, 'obtenerFirmaTransportista']);
    Route::get('/firmas/transportista/asignacion/{id_asignacion}', [FirmaController::class, 'obtenerFirmaPorAsignacion']);
    Route::put('/firmas/envio/{id_asignacion}', [FirmaController::class, 'actualizarFirmaEnvio']);
    Route::delete('/firmas/envio/{id_asignacion}', [FirmaController::class, 'eliminarFirmaEnvio']);

    // ────────────────────────────────────────────────────────────────────────
    // QR Tokens
    // ────────────────────────────────────────────────────────────────────────
    Route::post('/qr/validar-public', [QrController::class, 'validarQrToken']);
    Route::post('/qr/codigoacceso', [QrController::class, 'validarCodigoAcceso']);
    Route::get('/qr/generar/{id_asignacion}', [QrController::class, 'generarQrToken']);
    Route::get('/qr/{id_asignacion}', [QrController::class, 'obtenerQrToken']);
    Route::get('/qr/transportista/{id_asignacion}', [QrController::class, 'obtenerQR']);
    Route::post('/qr/validar', [QrController::class, 'validarQrToken']);
    Route::get('/qr/cliente/tokens', [QrController::class, 'obtenerQrTokensCliente']);
    Route::delete('/qr/{id_asignacion}', [QrController::class, 'eliminarQrToken']);
});



