<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Admin;
use App\Models\TipoEmpaque;
use App\Models\UnidadMedida;
use App\Models\Direccion;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function index()
    {
        $envios = Envio::with(['tipoEmpaque', 'unidadMedida', 'direcciones'])
            ->orderBy('fecha_envio', 'desc')
            ->get();
        return view('envios.index', compact('envios'));
    }

    public function create()
    {
        $tiposEmpaque = TipoEmpaque::all();
        $unidadesMedida = UnidadMedida::all();
        $direcciones = Direccion::all();
        
        return view('envios.create', compact('tiposEmpaque', 'unidadesMedida', 'direcciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_empaque_id' => 'nullable|exists:tipo_empaque,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medida,id',
            'peso_por_unidad' => 'nullable|numeric|min:0',
            'cantidad_productos' => 'nullable|integer|min:1',
            'peso' => 'nullable|numeric|min:0',
            'fecha_envio' => 'required|date',
            'fecha_entrega_estimada' => 'nullable|date|after_or_equal:fecha_envio',
            'direccion_id' => 'nullable|exists:direccion,id',
        ]);

        // Calcular peso total si hay peso por unidad y cantidad
        if (!empty($validated['peso_por_unidad']) && !empty($validated['cantidad_productos'])) {
            $validated['peso'] = $validated['peso_por_unidad'] * $validated['cantidad_productos'];
        }

        $envio = Envio::create($validated);

        // Asignar la dirección al envío si se seleccionó una
        if (!empty($validated['direccion_id'])) {
            $direccion = Direccion::find($validated['direccion_id']);
            $direccion->envio_id = $envio->id;
            $direccion->save();
        }

        return redirect()->route('envios.index')
            ->with('success', 'Envío creado exitosamente.');
    }

    public function show(Envio $envio)
    {
        $envio->load(['tipoEmpaque', 'unidadMedida', 'direcciones']);
        return view('envios.show', compact('envio'));
    }

    public function edit(Envio $envio)
    {
        $tiposEmpaque = TipoEmpaque::all();
        $unidadesMedida = UnidadMedida::all();
        $direcciones = Direccion::all();
        
        return view('envios.edit', compact('envio', 'tiposEmpaque', 'unidadesMedida', 'direcciones'));
    }

    public function update(Request $request, Envio $envio)
    {
        $validated = $request->validate([
            'tipo_empaque_id' => 'nullable|exists:tipo_empaque,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medida,id',
            'peso_por_unidad' => 'nullable|numeric|min:0',
            'cantidad_productos' => 'nullable|integer|min:1',
            'peso' => 'nullable|numeric|min:0',
            'fecha_envio' => 'required|date',
            'fecha_entrega_estimada' => 'nullable|date|after_or_equal:fecha_envio',
            'direccion_id' => 'nullable|exists:direccion,id',
        ]);

        // Calcular peso total si hay peso por unidad y cantidad
        if (!empty($validated['peso_por_unidad']) && !empty($validated['cantidad_productos'])) {
            $validated['peso'] = $validated['peso_por_unidad'] * $validated['cantidad_productos'];
        }

        $envio->update($validated);

        // Asignar la dirección al envío si se seleccionó una
        if (!empty($validated['direccion_id'])) {
            $direccion = Direccion::find($validated['direccion_id']);
            $direccion->envio_id = $envio->id;
            $direccion->save();
        }

        return redirect()->route('envios.index')
            ->with('success', 'Envío actualizado exitosamente.');
    }

    public function destroy(Envio $envio)
    {
        $envio->delete();

        return redirect()->route('envios.index')
            ->with('success', 'Envío eliminado exitosamente.');
    }
}
