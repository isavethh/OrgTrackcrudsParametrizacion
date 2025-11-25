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
        $envios = Envio::with(['usuario', 'direccion', 'historialEstados.estadoEnvio'])
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
        $envio->load(['usuario', 'direccion', 'historialEstados.estadoEnvio', 'asignaciones']);
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
}
