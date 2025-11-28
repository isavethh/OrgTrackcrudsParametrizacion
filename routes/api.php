<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CondicionTransporteController;
use App\Http\Controllers\Api\EnvioController;
use App\Http\Controllers\Api\UbicacionController;
use App\Http\Controllers\Api\TipotransporteController;
use App\Http\Controllers\Api\TipoIncidenteTransporteController;
use App\Http\Controllers\Api\TiposVehiculoController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\TransportistaController;
use App\Http\Controllers\Api\FirmaController;
use App\Http\Controllers\Api\QrController;
use App\Http\Controllers\Api\UnidadesMedidaController;
use App\Http\Controllers\Api\CatalogoCargaController;

Route::middleware([])->group(function () {

    // Routes Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Routes Vehiculos
    Route::middleware('jwt')->group(function () {
        Route::get('/vehiculos', [VehiculoController::class, 'index']);
        Route::get('/vehiculos/{id}', [VehiculoController::class, 'show']);
        Route::post('/vehiculos', [VehiculoController::class, 'store']);
        Route::put('/vehiculos/{id}', [VehiculoController::class, 'update']);
        Route::delete('/vehiculos/{id}', [VehiculoController::class, 'destroy']);
    });

    // Routes Envios
    // Routes Envios - ORDEN IMPORTANTE: rutas específicas primero, luego genéricas
    Route::middleware('jwt')->post('/envios/completo', [EnvioController::class, 'crearEnvioCompleto']);
    Route::middleware('jwt')->post('/envios/completo-admin', [EnvioController::class, 'crearEnvioCompletoAdmin']);
    
    // Rutas específicas PRIMERO (antes de las genéricas)
    Route::middleware('jwt')->get('/envios/mis-envios', [EnvioController::class, 'obtenerMisEnvios']);
    Route::middleware('jwt')->get('/envios/usuario/{id_usuario}', [EnvioController::class, 'obtenerEnviosPorUsuario']);
    Route::middleware('jwt')->get('/envios/transportista/asignados', [EnvioController::class, 'obtenerEnviosAsignadosTransportista']);
    Route::middleware('jwt')->get('/envios/particiones/en-curso', [EnvioController::class, 'obtenerParticionesEnCursoCliente']);
    
    // Rutas con parámetros específicos
    Route::middleware('jwt')->put('/envios/asignacion/{id_asignacion}/asignar', [EnvioController::class, 'asignarTransportistaYVehiculoAParticion']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/iniciar', [EnvioController::class, 'iniciarViaje']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/finalizar', [EnvioController::class, 'finalizarEnvio']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/checklist-condiciones', [EnvioController::class, 'registrarChecklistCondiciones']);
    Route::middleware('jwt')->post('/envios/asignacion/{id_asignacion}/checklist-incidentes', [EnvioController::class, 'registrarChecklistIncidentes']);
    Route::middleware('jwt')->get('/envios/asignacion/{id_asignacion}/documento', [EnvioController::class, 'generarDocumentoParticion']);
    Route::middleware('jwt')->get('/envios/documentos/asignacion/{id_asignacion}', [EnvioController::class, 'generarDocumentoParticion']);
    
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

    // Routes Tipos de Transporte
    Route::middleware('jwt')->group(function () {
        Route::get('/tipotransporte', [TipotransporteController::class, 'index']);
        Route::post('/tipotransporte', [TipotransporteController::class, 'store']);
        Route::put('/tipotransporte/{id}', [TipotransporteController::class, 'update']);
        Route::delete('/tipotransporte/{id}', [TipotransporteController::class, 'destroy']);
    });

    // Routes Tipos de Vehículo
    Route::middleware('jwt')->group(function () {
        Route::get('/tipos-vehiculo', [TiposVehiculoController::class, 'index']);
        Route::post('/tipos-vehiculo', [TiposVehiculoController::class, 'store']);
        Route::put('/tipos-vehiculo/{id}', [TiposVehiculoController::class, 'update']);
        Route::delete('/tipos-vehiculo/{id}', [TiposVehiculoController::class, 'destroy']);
    });

    // Catálogos de checklist (condiciones e incidentes)
    Route::middleware('jwt')->group(function () {
        Route::get('/condiciones-transporte', [CondicionTransporteController::class, 'index']);
        Route::post('/condiciones-transporte', [CondicionTransporteController::class, 'store']);
        Route::put('/condiciones-transporte/{id}', [CondicionTransporteController::class, 'update'])->whereNumber('id');
        Route::delete('/condiciones-transporte/{id}', [CondicionTransporteController::class, 'destroy'])->whereNumber('id');

        Route::get('/tipos-incidente-transporte', [TipoIncidenteTransporteController::class, 'index']);
        Route::post('/tipos-incidente-transporte', [TipoIncidenteTransporteController::class, 'store']);
        Route::put('/tipos-incidente-transporte/{id}', [TipoIncidenteTransporteController::class, 'update'])->whereNumber('id');
        Route::delete('/tipos-incidente-transporte/{id}', [TipoIncidenteTransporteController::class, 'destroy'])->whereNumber('id');
    });

    // Routes Usuarios
    // ORDEN IMPORTANTE: rutas específicas primero, luego genéricas
    Route::middleware('jwt')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'obtenerTodos']);
        Route::post('/usuarios', [UsuarioController::class, 'crear']);
        
        // Rutas específicas PRIMERO (antes de las genéricas)
        Route::get('/usuarios/clientes', [UsuarioController::class, 'obtenerClientes']);
        Route::get('/usuarios/rol/{rol}', [UsuarioController::class, 'obtenerPorRol']);
        Route::put('/usuarios/{id}/cambiar-rol', [UsuarioController::class, 'cambiarRol']);
        
        // Rutas genéricas AL FINAL
        Route::get('/usuarios/{id}', [UsuarioController::class, 'obtenerPorId']);
        Route::put('/usuarios/{id}', [UsuarioController::class, 'editar']);
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'eliminar']);
    });

    // Routes Transportistas
    Route::middleware('jwt')->group(function () {
        Route::get('/transportistas', [TransportistaController::class, 'obtenerTodos']);
        Route::get('/transportistas/{id}', [TransportistaController::class, 'obtenerPorId'])->whereNumber('id');
        Route::post('/transportistas', [TransportistaController::class, 'crear']);
        Route::put('/transportistas/{id}', [TransportistaController::class, 'editar'])->whereNumber('id');
        Route::delete('/transportistas/{id}', [TransportistaController::class, 'eliminar'])->whereNumber('id');
        Route::post('/transportistas/completo', [TransportistaController::class, 'crearTransportistaCompleto']);
        Route::get('/transportistas/estado/{estado}', [TransportistaController::class, 'obtenerPorEstado']);
        Route::get('/transportistas/disponibles', [TransportistaController::class, 'obtenerDisponibles']);
    });

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
        Route::get('/generar/{id_asignacion}', [QrController::class, 'generarQrToken']);
        Route::get('/{id_asignacion}', [QrController::class, 'obtenerQrToken']);
        Route::get('/transportista/{id_asignacion}', [QrController::class, 'obtenerQR']);
        Route::post('/validar', [QrController::class, 'validarQrToken']);
        Route::get('/cliente/tokens', [QrController::class, 'obtenerQrTokensCliente']);
        Route::delete('/{id_asignacion}', [QrController::class, 'eliminarQrToken']);
    });

    // Routes Unidades de Medida
    Route::middleware('jwt')->group(function () {
        Route::get('/unidades-medida', [UnidadesMedidaController::class, 'index']);
        Route::get('/unidades-medida/{id}', [UnidadesMedidaController::class, 'show']);
        Route::post('/unidades-medida', [UnidadesMedidaController::class, 'store']);
        Route::put('/unidades-medida/{id}', [UnidadesMedidaController::class, 'update']);
        Route::delete('/unidades-medida/{id}', [UnidadesMedidaController::class, 'destroy']);
    });

    // Routes Catálogo de Carga
    Route::middleware('jwt')->group(function () {
        Route::get('/catalogo-carga', [CatalogoCargaController::class, 'index']);
        Route::get('/catalogo-carga/{id}', [CatalogoCargaController::class, 'show']);
        Route::post('/catalogo-carga', [CatalogoCargaController::class, 'store']);
        Route::put('/catalogo-carga/{id}', [CatalogoCargaController::class, 'update']);
        Route::delete('/catalogo-carga/{id}', [CatalogoCargaController::class, 'destroy']);
    });
});


