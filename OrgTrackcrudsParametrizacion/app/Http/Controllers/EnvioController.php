<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Usuario;
use App\Models\Direccion;
use App\Models\EstadoEnvio;
use App\Models\HistorialEstado;
use App\Models\EnvioProducto;
use App\Models\TipoEmpaque;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnvioController extends Controller
{
    public function index()
    {
        $envios = Envio::with(['usuario', 'direccion', 'productos', 'tipoVehiculo', 'transportistaAsignado', 'historialEstados.estadoEnvio'])
            ->orderBy('fecha_creacion', 'desc')
            ->get();
        return view('envios.index', compact('envios'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        $direcciones = Direccion::all();
        $tiposEmpaque = TipoEmpaque::orderBy('nombre')->get();
        $unidadesMedida = UnidadMedida::orderBy('nombre')->get();
        
        return view('envios.create', compact('usuarios', 'direcciones', 'tiposEmpaque', 'unidadesMedida'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_usuario' => 'required|exists:usuarios,id',
                'id_direccion' => 'required|exists:direccion,id',
                'fecha_entrega_aproximada' => 'nullable|date',
                'hora_entrega_aproximada' => 'nullable|date_format:H:i',
                'productos' => 'required|array|min:1',
                'productos.*.categoria' => 'required|string|in:Verduras,Frutas',
                'productos.*.producto' => 'required|string',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.peso_por_unidad' => 'required|numeric|min:0',
                'productos.*.costo_unitario' => 'required|numeric|min:0',
                'productos.*.id_tipo_empaque' => 'required|exists:tipo_empaque,id',
                'productos.*.id_unidad_medida' => 'required|exists:unidad_medida,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', 'Error de validación. Verifique los datos ingresados.');
        }

        DB::beginTransaction();
        try {
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
                'fecha_entrega_aproximada' => $validated['fecha_entrega_aproximada'] ?? null,
                'hora_entrega_aproximada' => $validated['hora_entrega_aproximada'] ?? null,
                'peso_total_envio' => $pesoTotal,
                'costo_total_envio' => $costoTotal,
            ]);

            // Crear productos del envío
            foreach ($validated['productos'] as $prod) {
                EnvioProducto::create([
                    'id_envio' => $envio->id,
                    'categoria' => $prod['categoria'],
                    'producto' => $prod['producto'],
                    'cantidad' => $prod['cantidad'],
                    'peso_por_unidad' => $prod['peso_por_unidad'],
                    'peso_total' => $prod['cantidad'] * $prod['peso_por_unidad'],
                    'costo_unitario' => $prod['costo_unitario'],
                    'costo_total' => $prod['cantidad'] * $prod['costo_unitario'],
                    'id_tipo_empaque' => $prod['id_tipo_empaque'],
                    'id_unidad_medida' => $prod['id_unidad_medida'],
                ]);
            }

            // Crear el primer estado del envío como "Pendiente"
            $estadoPendiente = EstadoEnvio::where('nombre', 'Pendiente')->first();
            if ($estadoPendiente) {
                HistorialEstado::create([
                    'id_envio' => $envio->id,
                    'id_estado_envio' => $estadoPendiente->id,
                    'observaciones' => 'Envío creado',
                ]);
            }

            DB::commit();
            return redirect()->route('envios.index')
                ->with('success', 'Envío creado exitosamente con ' . count($validated['productos']) . ' producto(s).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear envío: ' . $e->getMessage());
        }
    }

    public function show(Envio $envio)
    {
        $envio->load([
            'usuario', 
            'direccion', 
            'productos.tipoEmpaque', 
            'productos.unidadMedida',
            'tipoVehiculo',
            'transportistaAsignado',
            'historialEstados.estadoEnvio', 
            'asignaciones'
        ]);
        return view('envios.show', compact('envio'));
    }

    public function edit(Envio $envio)
    {
        $usuarios = Usuario::all();
        $direcciones = Direccion::all();
        $tiposEmpaque = TipoEmpaque::orderBy('nombre')->get();
        $unidadesMedida = UnidadMedida::orderBy('nombre')->get();
        $envio->load('productos');
        
        return view('envios.edit', compact('envio', 'usuarios', 'direcciones', 'tiposEmpaque', 'unidadesMedida'));
    }

    public function update(Request $request, Envio $envio)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'id_direccion' => 'required|exists:direccion,id',
            'fecha_entrega_aproximada' => 'nullable|date',
            'hora_entrega_aproximada' => 'nullable|date_format:H:i',
            'productos' => 'required|array|min:1',
            'productos.*.categoria' => 'required|string|in:Verduras,Frutas',
            'productos.*.producto' => 'required|string',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.peso_por_unidad' => 'required|numeric|min:0',
            'productos.*.costo_unitario' => 'required|numeric|min:0',
            'productos.*.id_tipo_empaque' => 'required|exists:tipo_empaque,id',
            'productos.*.id_unidad_medida' => 'required|exists:unidad_medida,id',
        ]);

        DB::beginTransaction();
        try {
            // Calcular totales
            $pesoTotal = 0;
            $costoTotal = 0;

            foreach ($validated['productos'] as $prod) {
                $pesoTotal += $prod['cantidad'] * $prod['peso_por_unidad'];
                $costoTotal += $prod['cantidad'] * $prod['costo_unitario'];
            }

            // Actualizar envío
            $envio->update([
                'id_usuario' => $validated['id_usuario'],
                'id_direccion' => $validated['id_direccion'],
                'fecha_entrega_aproximada' => $validated['fecha_entrega_aproximada'] ?? null,
                'hora_entrega_aproximada' => $validated['hora_entrega_aproximada'] ?? null,
                'peso_total_envio' => $pesoTotal,
                'costo_total_envio' => $costoTotal,
            ]);

            // Eliminar productos anteriores y crear nuevos
            $envio->productos()->delete();

            foreach ($validated['productos'] as $prod) {
                EnvioProducto::create([
                    'id_envio' => $envio->id,
                    'categoria' => $prod['categoria'],
                    'producto' => $prod['producto'],
                    'cantidad' => $prod['cantidad'],
                    'peso_por_unidad' => $prod['peso_por_unidad'],
                    'peso_total' => $prod['cantidad'] * $prod['peso_por_unidad'],
                    'costo_unitario' => $prod['costo_unitario'],
                    'costo_total' => $prod['cantidad'] * $prod['costo_unitario'],
                    'id_tipo_empaque' => $prod['id_tipo_empaque'],
                    'id_unidad_medida' => $prod['id_unidad_medida'],
                ]);
            }

            DB::commit();
            return redirect()->route('envios.index')
                ->with('success', 'Envío actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar envío: ' . $e->getMessage());
        }
    }

    public function destroy(Envio $envio)
    {
        $envio->delete();

        return redirect()->route('envios.index')
            ->with('success', 'Envío eliminado exitosamente.');
    }

    public function aprobar(Request $request, Envio $envio)
    {
        try {
            $validated = $request->validate([
                'transportista_id' => 'required|exists:usuarios,id',
            ]);

            \Log::info("Intentando aprobar envío", [
                'envio_id' => $envio->id,
                'transportista_id' => $validated['transportista_id']
            ]);

            // Actualizar en la base de datos local
            $envio->update([
                'estado_aprobacion' => 'aprobado',
                'id_transportista_asignado' => $validated['transportista_id'],
            ]);

            \Log::info("Envío actualizado localmente", ['envio_id' => $envio->id]);

            // Actualizar en la API
            $response = \Illuminate\Support\Facades\Http::post("http://localhost:8001/api/envios/{$envio->id}/aprobar", [
                'id_transportista_asignado' => $validated['transportista_id'],
            ]);

            if (!$response->successful()) {
                \Log::warning("No se pudo actualizar el envío en la API", [
                    'envio_id' => $envio->id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } else {
                \Log::info("Envío actualizado en la API exitosamente", ['envio_id' => $envio->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Envío aprobado y transportista asignado exitosamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Error de validación al aprobar envío", ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error("Error al aprobar envío", [
                'envio_id' => $envio->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar envío: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rechazar(Request $request, Envio $envio)
    {
        $validated = $request->validate([
            'motivo_rechazo' => 'required|string|min:10',
        ]);

        try {
            // Actualizar en la base de datos local
            $envio->update([
                'estado_aprobacion' => 'rechazado',
                'motivo_rechazo' => $validated['motivo_rechazo'],
            ]);

            // Actualizar en la API
            $response = \Illuminate\Support\Facades\Http::post("http://localhost:8001/api/envios/{$envio->id}/rechazar", [
                'motivo_rechazo' => $validated['motivo_rechazo'],
            ]);

            if (!$response->successful()) {
                \Log::warning("No se pudo actualizar el envío en la API", ['envio_id' => $envio->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Envío rechazado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar envío: ' . $e->getMessage()
            ], 500);
        }
    }
}
