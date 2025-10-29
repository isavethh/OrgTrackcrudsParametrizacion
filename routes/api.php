<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EnvioController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\TipotransporteController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\TransportistaController;
use App\Http\Controllers\Api\FirmaController;
use App\Http\Controllers\Api\QrController;

Route::middleware([])->group(function () {

    // Routes Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Routes Vehiculos
    Route::get('/vehiculos', [VehiculoController::class, 'index']);
    Route::get('/vehiculos/{id}', [VehiculoController::class, 'show']);
    Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::put('/vehiculos/{id}', [VehiculoController::class, 'update']);
    Route::delete('/vehiculos/{id}', [VehiculoController::class, 'destroy']);

    // Routes Envios
    // Routes Envios - ORDEN IMPORTANTE: rutas específicas primero, luego genéricas
    Route::middleware('jwt')->post('/envios/completo', [EnvioController::class, 'crearEnvioCompleto']);
    Route::post('/envios/completo-admin', [EnvioController::class, 'crearEnvioCompletoAdmin']);
    
    // Rutas específicas PRIMERO (antes de las genéricas)
    Route::middleware('jwt')->get('/envios/mis-envios', [EnvioController::class, 'obtenerMisEnvios']);
    Route::middleware('jwt')->get('/envios/transportista/asignados', [EnvioController::class, 'obtenerEnviosAsignadosTransportista']);
    Route::middleware('jwt')->get('/envios/particiones/en-curso', [EnvioController::class, 'obtenerParticionesEnCursoCliente']);
    
    // Rutas con parámetros específicos
    Route::middleware('jwt')->put('/envios/asignacion/{id_asignacion}/asignar', [EnvioController::class, 'asignarTransportistaYVehiculoAParticion']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/iniciar', [EnvioController::class, 'iniciarViaje']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/finalizar', [EnvioController::class, 'finalizarEnvio']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/checklist-condiciones', [EnvioController::class, 'registrarChecklistCondiciones']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/checklist-incidentes', [EnvioController::class, 'registrarChecklistIncidentes']);
    Route::middleware('jwt')->get('/envios/asignacion/{id_asignacion}/documento', [EnvioController::class, 'generarDocumentoParticion']);
    
    // Rutas genéricas AL FINAL
    Route::middleware('jwt')->get('/envios', [EnvioController::class, 'obtenerTodos']);
    Route::middleware('jwt')->get('/envios/{id}', [EnvioController::class, 'obtenerPorId']);
    Route::middleware('jwt')->put('/envios/{id_envio}/asignar', [EnvioController::class, 'asignarTransportistaYVehiculo']);
    Route::middleware('jwt')->get('/envios/{id_envio}/documento', [EnvioController::class, 'generarDocumentoEnvio']);
    Route::middleware('jwt')->put('/envios/{id_envio}/estado-global', [EnvioController::class, 'actualizarEstadoGlobalEnvio']);

    Route::middleware('jwt')->prefix('ubicaciones')->group(function () {
        Route::get('/', [UbicacionController::class, 'index']);
        Route::get('/{id}', [UbicacionController::class, 'show']);
        Route::post('/', [UbicacionController::class, 'store']);
        Route::put('/{id}', [UbicacionController::class, 'update']);
        Route::delete('/{id}', [UbicacionController::class, 'destroy']);
    });

    Route::get('/tipotransporte', [TipotransporteController::class, 'index']);

    // Routes Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'obtenerTodos']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'obtenerPorId']);
    Route::post('/usuarios', [UsuarioController::class, 'crear']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'editar']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminar']);
    Route::get('/usuarios/clientes', [UsuarioController::class, 'obtenerClientes']);
    Route::get('/usuarios/rol/{rol}', [UsuarioController::class, 'obtenerPorRol']);
    Route::put('/usuarios/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol']);

    // Routes Transportistas
    Route::get('/transportistas', [TransportistaController::class, 'obtenerTodos']);
    Route::get('/transportistas/{id}', [TransportistaController::class, 'obtenerPorId']);
    Route::post('/transportistas', [TransportistaController::class, 'crear']);
    Route::put('/transportistas/{id}', [TransportistaController::class, 'editar']);
    Route::delete('/transportistas/{id}', [TransportistaController::class, 'eliminar']);
    Route::post('/transportistas/completo', [TransportistaController::class, 'crearTransportistaCompleto']);
    Route::get('/transportistas/estado/{estado}', [TransportistaController::class, 'obtenerPorEstado']);
    Route::get('/transportistas/disponibles', [TransportistaController::class, 'obtenerDisponibles']);

    // Routes Firmas
    Route::middleware('jwt')->prefix('firmas')->group(function () {
        Route::post('/envio/{id_asignacion}', [FirmaController::class, 'guardarFirmaEnvio']);
        Route::post('/transportista/{id_asignacion}', [FirmaController::class, 'guardarFirmaTransportista']);
        Route::get('/envio/{id_asignacion}', [FirmaController::class, 'obtenerFirmaEnvio']);
        Route::get('/transportista/{id_asignacion}', [FirmaController::class, 'obtenerFirmaTransportista']);
        Route::get('/transportista/asignacion/{id_asignacion}', [FirmaController::class, 'obtenerFirmaPorAsignacion']);
        Route::put('/envio/{id_asignacion}', [FirmaController::class, 'actualizarFirmaEnvio']);
        Route::delete('/envio/{id_asignacion}', [FirmaController::class, 'eliminarFirmaEnvio']);
    });

    // Routes QR Tokens
    Route::middleware('jwt')->prefix('qr')->group(function () {
        Route::post('/generar/{id_asignacion}', [QrController::class, 'generarQrToken']);
        Route::get('/{id_asignacion}', [QrController::class, 'obtenerQrToken']);
        Route::get('/transportista/{id_asignacion}', [QrController::class, 'obtenerQR']);
        Route::post('/validar', [QrController::class, 'validarQrToken']);
        Route::get('/cliente/tokens', [QrController::class, 'obtenerQrTokensCliente']);
        Route::delete('/{id_asignacion}', [QrController::class, 'eliminarQrToken']);
    });
});


