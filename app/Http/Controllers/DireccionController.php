<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    public function index()
    {
        $direcciones = Direccion::all();
        return view('direcciones.index', compact('direcciones'));
    }

    public function create()
    {
        return view('direcciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreorigen' => 'required|string|max:200',
            'origen_lat' => 'required|numeric|between:-90,90',
            'origen_lng' => 'required|numeric|between:-180,180',
            'nombredestino' => 'required|string|max:200',
            'destino_lat' => 'required|numeric|between:-90,90',
            'destino_lng' => 'required|numeric|between:-180,180',
            'rutageojson' => 'nullable|string',
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
            'nombreorigen' => 'required|string|max:200',
            'origen_lat' => 'required|numeric|between:-90,90',
            'origen_lng' => 'required|numeric|between:-180,180',
            'nombredestino' => 'required|string|max:200',
            'destino_lat' => 'required|numeric|between:-90,90',
            'destino_lng' => 'required|numeric|between:-180,180',
            'rutageojson' => 'nullable|string',
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
