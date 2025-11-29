<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\Direccion;
use App\Models\Envio;
use App\Models\RecogidaEntrega;
use App\Models\AsignacionMultiple;
use App\Models\AsignacionCarga;
use App\Models\Carga;
use App\Models\CatalogoCarga;
use App\Models\Tipotransporte;
use App\Models\Tipotransporte as TipoTransporteModel;

use App\Http\Controllers\Api\Helpers\EstadoHelper;
use App\Http\Controllers\Api\Helpers\UsuarioHelper;

class ProductorEnvioController extends Controller
{
    /**
     * Crear envío enviado por un productor externo.
     * - Si `usuario.correo` ya existe: reutiliza usuario.
     * - Si no existe: crea Persona (opcional), Usuario (rol cliente) y Cliente.
     * - Si no se envía `usuario.contrasena`, se genera una contraseña y se devuelve en la respuesta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'usuario.correo' => 'required|email',
            'usuario.contrasena' => 'nullable|string|min:6',
            'usuario.nombre' => 'nullable|string|max:100',
            'usuario.apellido' => 'nullable|string|max:100',
            'usuario.ci' => 'nullable|string|max:20',
            'usuario.telefono' => 'nullable|string|max:20',
            'id_direccion' => 'nullable|integer|exists:direccion,id',
            'ubicacion' => 'nullable|array',
            'particiones' => 'required|array|min:1',
            'particiones.*.cargas' => 'required|array|min:1',
            'particiones.*.cargas.*.tipo' => 'required|string|max:50',
            'particiones.*.cargas.*.variedad' => 'required|string|max:50',
            'particiones.*.cargas.*.cantidad' => 'required|integer|min:1',
            'particiones.*.cargas.*.empaquetado' => 'required|string|max:50',
            'particiones.*.cargas.*.peso' => 'required|numeric|min:0',
            'particiones.*.recogidaEntrega' => 'required|array',
            'particiones.*.recogidaEntrega.fecha_recogida' => 'required|date',
            'particiones.*.recogidaEntrega.hora_recogida' => 'required|date_format:H:i:s',
            'particiones.*.recogidaEntrega.hora_entrega' => 'required|date_format:H:i:s',
            'particiones.*.recogidaEntrega.instrucciones_recogida' => 'nullable|string|max:255',
            'particiones.*.recogidaEntrega.instrucciones_entrega' => 'nullable|string|max:255',
            'particiones.*.id_tipo_transporte' => 'required|integer|exists:tipotransporte,id',
        ]);

        $usuarioPayload = $request->input('usuario', []);
        $idDireccion = $request->input('id_direccion');
        $ubicacion = $request->input('ubicacion');
        $particiones = $request->input('particiones');

        try {
            return DB::transaction(function () use ($usuarioPayload, $idDireccion, $ubicacion, $particiones) {
                $correo = $usuarioPayload['correo'];
                $usuario = Usuario::where('correo', $correo)->first();
                $contrasenaGenerada = null;

                if (!$usuario) {
                    // Crear persona si hay datos
                    $personaData = [];
                    if (!empty($usuarioPayload['nombre'])) $personaData['nombre'] = $usuarioPayload['nombre'];
                    if (!empty($usuarioPayload['apellido'])) $personaData['apellido'] = $usuarioPayload['apellido'];
                    if (!empty($usuarioPayload['ci'])) $personaData['ci'] = $usuarioPayload['ci'];
                    if (!empty($usuarioPayload['telefono'])) $personaData['telefono'] = $usuarioPayload['telefono'];

                    $persona = null;
                    if (!empty($personaData)) {
                        if (!empty($personaData['ci']) && Persona::where('ci', $personaData['ci'])->exists()) {
                            return response()->json(['error' => 'El CI proporcionado ya está registrado'], Response::HTTP_CONFLICT);
                        }
                        $persona = Persona::create($personaData);
                    }

                    // Determinar contraseña (usada o generada)
                    $plainPassword = $usuarioPayload['contrasena'] ?? substr(bin2hex(random_bytes(4)), 0, 8);
                    if (!isset($usuarioPayload['contrasena'])) {
                        $contrasenaGenerada = $plainPassword;
                    }

                    $idRol = UsuarioHelper::obtenerRolPorCodigo('cliente');

                    $usuario = Usuario::create([
                        'correo' => $correo,
                        'contrasena' => Hash::make($plainPassword),
                        'id_rol' => $idRol,
                        'id_persona' => $persona?->id ?? null,
                        'fecha_registro' => now(),
                    ]);

                    Cliente::create(['id_usuario' => $usuario->id]);
                }

                // Direccion: usar id_direccion si llega, sino crear con ubicacion
                if ($idDireccion) {
                    $direccion = Direccion::find($idDireccion);
                    if (!$direccion) {
                        return response()->json(['error' => 'Dirección no encontrada'], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    if (empty($ubicacion) || !is_array($ubicacion)) {
                        return response()->json(['error' => 'Falta ubicación o id_direccion'], Response::HTTP_BAD_REQUEST);
                    }
                    $direccion = Direccion::create([
                        'id_usuario' => $usuario->id,
                        'nombreorigen' => $ubicacion['nombreorigen'] ?? null,
                        'origen_lng' => $ubicacion['origen_lng'] ?? null,
                        'origen_lat' => $ubicacion['origen_lat'] ?? null,
                        'nombredestino' => $ubicacion['nombredestino'] ?? null,
                        'destino_lng' => $ubicacion['destino_lng'] ?? null,
                        'destino_lat' => $ubicacion['destino_lat'] ?? null,
                        'rutageojson' => $ubicacion['rutageojson'] ?? null,
                    ]);
                    $idDireccion = $direccion->id;
                }

                // Crear envío
                $envio = Envio::create([
                    'id_usuario' => $usuario->id,
                    'id_direccion' => $idDireccion,
                ]);

                EstadoHelper::actualizarEstadoEnvio($envio->id, 'Pendiente');

                // Procesar particiones
                foreach ($particiones as $particion) {
                    $cargas = $particion['cargas'] ?? null;
                    $recogidaEntrega = $particion['recogidaEntrega'] ?? null;
                    $idTipoTransporte = $particion['id_tipo_transporte'] ?? null;

                    if (!$cargas || !is_array($cargas) || count($cargas) === 0 || !$recogidaEntrega || !$idTipoTransporte) {
                        return response()->json(['error' => 'Cada partición debe incluir cargas, recogidaEntrega y tipo de transporte'], Response::HTTP_BAD_REQUEST);
                    }

                    if (!Tipotransporte::where('id', $idTipoTransporte)->exists()) {
                        return response()->json(['error' => 'El tipo de transporte no existe: '.$idTipoTransporte], Response::HTTP_BAD_REQUEST);
                    }

                    $r = RecogidaEntrega::create([
                        'fecha_recogida' => $recogidaEntrega['fecha_recogida'],
                        'hora_recogida' => $recogidaEntrega['hora_recogida'],
                        'hora_entrega' => $recogidaEntrega['hora_entrega'],
                        'instrucciones_recogida' => $recogidaEntrega['instrucciones_recogida'] ?? null,
                        'instrucciones_entrega' => $recogidaEntrega['instrucciones_entrega'] ?? null,
                    ]);

                    $idEstadoPendiente = EstadoHelper::obtenerEstadoAsignacionPorNombre('Pendiente');

                    $asignacion = AsignacionMultiple::create([
                        'id_envio' => $envio->id,
                        'id_tipo_transporte' => $idTipoTransporte,
                        'id_estado_asignacion' => $idEstadoPendiente,
                        'id_recogida_entrega' => $r->id,
                    ]);

                    foreach ($cargas as $carga) {
                        $catalogo = CatalogoCarga::firstOrCreate(
                            [
                                'tipo' => $carga['tipo'],
                                'variedad' => $carga['variedad'],
                                'empaque' => $carga['empaquetado'],
                            ],
                            ['descripcion' => null]
                        );

                        $c = Carga::create([
                            'id_catalogo_carga' => $catalogo->id,
                            'cantidad' => $carga['cantidad'],
                            'peso' => $carga['peso'],
                        ]);

                        AsignacionCarga::create([
                            'id_asignacion' => $asignacion->id,
                            'id_carga' => $c->id,
                        ]);
                    }
                }

                $response = [
                    'mensaje' => 'Envío creado exitosamente',
                    'id_envio' => $envio->id,
                    'id_usuario' => $usuario->id,
                ];

                // NOTA: por seguridad no devolvemos contraseñas generadas en la respuesta.
                // Si el productor proporciona la contraseña en el payload, el usuario
                // podrá iniciar sesión con esas credenciales. Si no, el backend habrá
                // generado una contraseña interna pero no la expondrá aquí.

                return response()->json($response, Response::HTTP_CREATED);
            });
        } catch (\Exception $e) {
            \Log::error('Error crearEnvioProductor: '.$e->getMessage());
            return response()->json(['error' => 'Error interno al crear envío'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Devuelve transportistas y vehículos disponibles para que el productor los seleccione.
     */
    public function recursosDisponibles(Request $request)
    {
        try {
            // Devuelve solo tipos de transporte — el productor solo necesita elegir esto
            $tipos = TipoTransporteModel::all()->map(function ($t) {
                return [
                    'id' => $t->id,
                    'nombre' => $t->nombre,
                    'descripcion' => $t->descripcion,
                ];
            });

            return response()->json(['tipos_transporte' => $tipos]);
        } catch (\Exception $e) {
            \Log::error('Error recursosDisponibles: '.$e->getMessage());
            return response()->json(['error' => 'Error interno al obtener recursos'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
