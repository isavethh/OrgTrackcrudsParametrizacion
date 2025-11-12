<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    public function index()
    {
        $direcciones = Direccion::with('envio')->get();
        return view('direcciones.index', compact('direcciones'));
    }

    public function create()
    {
        return view('direcciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_ruta' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'punto_recogida_lat' => 'nullable|numeric|between:-90,90',
            'punto_recogida_lng' => 'nullable|numeric|between:-180,180',
            'nombre_punto_recogida' => 'nullable|string|max:200',
            'punto_entrega_lat' => 'nullable|numeric|between:-90,90',
            'punto_entrega_lng' => 'nullable|numeric|between:-180,180',
            'nombre_punto_entrega' => 'nullable|string|max:200',
        ]);

        Direccion::create($validated);

        return redirect()->route('direcciones.index')
            ->with('success', 'Dirección creada exitosamente.');
    }

    public function edit(Direccion $direccione)
    {
        return view('direcciones.edit', compact('direccione'));
    }

    public function update(Request $request, Direccion $direccione)
    {
        $validated = $request->validate([
            'nombre_ruta' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'punto_recogida_lat' => 'nullable|numeric|between:-90,90',
            'punto_recogida_lng' => 'nullable|numeric|between:-180,180',
            'nombre_punto_recogida' => 'nullable|string|max:200',
            'punto_entrega_lat' => 'nullable|numeric|between:-90,90',
            'punto_entrega_lng' => 'nullable|numeric|between:-180,180',
            'nombre_punto_entrega' => 'nullable|string|max:200',
        ]);

        $direccione->update($validated);

        return redirect()->route('direcciones.index')
            ->with('success', 'Dirección actualizada exitosamente.');
    }

    public function destroy(Direccion $direccione)
    {
        $direccione->delete();

        return redirect()->route('direcciones.index')
            ->with('success', 'Dirección eliminada exitosamente.');
    }
}
