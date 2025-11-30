<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Envio;
use App\Models\Usuario;
use App\Models\EnvioProducto;
use App\Models\EstadoEnvio;
use App\Models\HistorialEstado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnvioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Envio::with(['usuario', 'direccion', 'productos', 'tipoVehiculo', 'transportistaAsignado', 'historialEstados.estadoEnvio']);

        $envios = $query->orderBy('fecha_creacion', 'desc')->get();

        // Transformar datos para incluir el nombre del usuario
        $enviosTransformados = $envios->map(function($envio) {
            $data = $envio->toArray();
            $data['usuario_nombre'] = $envio->usuario ? $envio->usuario->nombre . ' ' . $envio->usuario->apellido : 'N/A';
            $data['sistema_origen'] = 'orgtrack'; // Todos los envíos son de OrgTrack en esta BD
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $enviosTransformados
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_usuario' => 'sometimes|exists:usuarios,id',
                'usuario_nombre' => 'required_without:id_usuario|string',
                'usuario_correo' => 'required_without:id_usuario|email',
                'id_direccion' => 'required|exists:direccion,id',
                'id_tipo_vehiculo' => 'nullable|exists:tipos_vehiculo,id',
                'fecha_entrega_aproximada' => 'nullable|date',
                'hora_entrega_aproximada' => 'nullable|date_format:H:i',
                'productos' => 'required|array|min:1',
                'productos.*.categoria' => 'nullable|string',
                'productos.*.producto' => 'required|string',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.peso_por_unidad' => 'required|numeric|min:0',
                'productos.*.costo_unitario' => 'required|numeric|min:0',
                'productos.*.id_tipo_empaque' => 'nullable|exists:tipo_empaque,id',
                'productos.*.id_unidad_medida' => 'nullable|exists:unidad_medida,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Si no viene id_usuario, buscar o crear usuario con el nombre/correo
            if (!isset($validated['id_usuario'])) {
                $usuario = Usuario::where('correo', $validated['usuario_correo'])->first();
                
                if (!$usuario) {
                    // Extraer nombre y apellido
                    $nombreCompleto = explode(' ', $validated['usuario_nombre'], 2);
                    $nombre = $nombreCompleto[0];
                    $apellido = $nombreCompleto[1] ?? '';
                    
                    $usuario = Usuario::create([
                        'correo' => $validated['usuario_correo'],
                        'contrasena' => bcrypt(Str::random(16)), // Password aleatorio
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'ci' => 'EXTERNO',
                        'id_rol' => 3, // Rol de cliente por defecto
                        'fecha_registro' => now(),
                    ]);
                }
                
                $validated['id_usuario'] = $usuario->id;
            }

            // Calcular totales
            $pesoTotal = 0;
            $costoTotal = 0;

            foreach ($validated['productos'] as $prod) {
                $pesoTotal += $prod['cantidad'] * $prod['peso_por_unidad'];
                $costoTotal += $prod['cantidad'] * $prod['costo_unitario'];
            }

            // Crear envío
            $envio = Envio::create([
                'id_usuario' => $validated['id_usuario'],
                'id_direccion' => $validated['id_direccion'],
                'id_tipo_vehiculo' => $validated['id_tipo_vehiculo'] ?? null,
                'fecha_entrega_aproximada' => $validated['fecha_entrega_aproximada'] ?? null,
                'hora_entrega_aproximada' => $validated['hora_entrega_aproximada'] ?? null,
                'peso_total_envio' => $pesoTotal,
                'costo_total_envio' => $costoTotal,
                'estado_aprobacion' => 'pendiente',
            ]);

            // Crear productos del envío
            foreach ($validated['productos'] as $prod) {
                EnvioProducto::create([
                    'id_envio' => $envio->id,
                    'categoria' => $prod['categoria'] ?? 'General',
                    'producto' => $prod['producto'],
                    'cantidad' => $prod['cantidad'],
                    'peso_por_unidad' => $prod['peso_por_unidad'],
                    'peso_total' => $prod['cantidad'] * $prod['peso_por_unidad'],
                    'costo_unitario' => $prod['costo_unitario'],
                    'costo_total' => $prod['cantidad'] * $prod['costo_unitario'],
                    'id_tipo_empaque' => $prod['id_tipo_empaque'] ?? null,
                    'id_unidad_medida' => $prod['id_unidad_medida'] ?? null,
                ]);
            }

            // Crear el primer estado del envío como "Pendiente"
            $estadoPendiente = EstadoEnvio::where('nombre', 'Pendiente')->first();
            if ($estadoPendiente) {
                HistorialEstado::create([
                    'id_envio' => $envio->id,
                    'id_estado_envio' => $estadoPendiente->id,
                    'observaciones' => 'Envío creado desde API',
                ]);
            }

            DB::commit();

            // Cargar relaciones para la respuesta
            $envio->load(['usuario', 'direccion', 'productos']);
            
            $response = $envio->toArray();
            $response['usuario_nombre'] = $envio->usuario->nombre . ' ' . $envio->usuario->apellido;
            $response['sistema_origen'] = 'orgtrack';

            return response()->json([
                'success' => true,
                'message' => 'Envío creado exitosamente',
                'data' => $response
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear envío: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Envio $envio)
    {
        $envio->load(['usuario', 'direccion', 'productos', 'tipoVehiculo', 'transportistaAsignado', 'historialEstados.estadoEnvio']);
        
        $response = $envio->toArray();
        $response['usuario_nombre'] = $envio->usuario->nombre . ' ' . $envio->usuario->apellido;
        $response['sistema_origen'] = 'orgtrack';
        
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Envio $envio)
    {
        $validated = $request->validate([
            'id_direccion' => 'sometimes|exists:direccion,id',
            'fecha_entrega_aproximada' => 'nullable|date',
            'hora_entrega_aproximada' => 'nullable|date_format:H:i',
            'productos' => 'sometimes|array|min:1',
            'productos.*.categoria' => 'nullable|string',
            'productos.*.producto' => 'required_with:productos|string',
            'productos.*.cantidad' => 'required_with:productos|integer|min:1',
            'productos.*.peso_por_unidad' => 'required_with:productos|numeric|min:0',
            'productos.*.costo_unitario' => 'required_with:productos|numeric|min:0',
            'productos.*.id_tipo_empaque' => 'nullable|exists:tipo_empaque,id',
            'productos.*.id_unidad_medida' => 'nullable|exists:unidad_medida,id',
        ]);

        DB::beginTransaction();
        try {
            // Si se actualizan productos, recalcular totales
            if (isset($validated['productos'])) {
                $pesoTotal = 0;
                $costoTotal = 0;

                foreach ($validated['productos'] as $prod) {
                    $pesoTotal += $prod['cantidad'] * $prod['peso_por_unidad'];
                    $costoTotal += $prod['cantidad'] * $prod['costo_unitario'];
                }

                $validated['peso_total_envio'] = $pesoTotal;
                $validated['costo_total_envio'] = $costoTotal;

                // Eliminar productos anteriores
                $envio->productos()->delete();

                // Crear nuevos productos
                foreach ($validated['productos'] as $prod) {
                    EnvioProducto::create([
                        'id_envio' => $envio->id,
                        'categoria' => $prod['categoria'] ?? 'General',
                        'producto' => $prod['producto'],
                        'cantidad' => $prod['cantidad'],
                        'peso_por_unidad' => $prod['peso_por_unidad'],
                        'peso_total' => $prod['cantidad'] * $prod['peso_por_unidad'],
                        'costo_unitario' => $prod['costo_unitario'],
                        'costo_total' => $prod['cantidad'] * $prod['costo_unitario'],
                        'id_tipo_empaque' => $prod['id_tipo_empaque'] ?? null,
                        'id_unidad_medida' => $prod['id_unidad_medida'] ?? null,
                    ]);
                }

                unset($validated['productos']);
            }

            // Actualizar envío
            $envio->update($validated);

            DB::commit();

            $envio->load(['usuario', 'direccion', 'productos']);
            
            $response = $envio->toArray();
            $response['usuario_nombre'] = $envio->usuario->nombre . ' ' . $envio->usuario->apellido;

            return response()->json([
                'success' => true,
                'message' => 'Envío actualizado exitosamente',
                'data' => $response
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar envío: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Envio $envio)
    {
        $envio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Envío eliminado exitosamente'
        ]);
    }

    /**
     * Aprobar un envío y asignar transportista
     */
    public function aprobar(Request $request, Envio $envio)
    {
        try {
            \Log::info("API - Recibiendo solicitud de aprobación", [
                'envio_id' => $envio->id,
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'id_transportista_asignado' => 'required|integer',
            ]);

            \Log::info("API - Validación exitosa", ['validated' => $validated]);

            $envio->update([
                'estado_aprobacion' => 'aprobado',
                'id_transportista_asignado' => $validated['id_transportista_asignado'],
            ]);

            \Log::info("API - Envío actualizado exitosamente", [
                'envio_id' => $envio->id,
                'estado_aprobacion' => $envio->estado_aprobacion
            ]);

            $envio->load(['transportistaAsignado']);

            return response()->json([
                'success' => true,
                'message' => 'Envío aprobado y transportista asignado exitosamente',
                'data' => $envio
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("API - Error de validación al aprobar", ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("API - Error al aprobar envío", [
                'envio_id' => $envio->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar envío: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar un envío
     */
    public function rechazar(Request $request, Envio $envio)
    {
        $validated = $request->validate([
            'motivo_rechazo' => 'required|string|min:10',
        ]);

        try {
            $envio->update([
                'estado_aprobacion' => 'rechazado',
                'motivo_rechazo' => $validated['motivo_rechazo'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Envío rechazado exitosamente',
                'data' => $envio
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar envío: ' . $e->getMessage()
            ], 500);
        }
    }
}
