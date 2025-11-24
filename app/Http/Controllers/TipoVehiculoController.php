<?php

namespace App\Http\Controllers;

use App\Models\TipoVehiculo;
use Illuminate\Http\Request;

class TipoVehiculoController extends Controller
{
    public function index()
    {
        $tiposVehiculo = TipoVehiculo::all();
        return view('tipos-vehiculo.index', compact('tiposVehiculo'));
    }

    public function create()
    {
        return view('tipos-vehiculo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_vehiculo,nombre',
            'descripcion' => 'nullable|string|max:150',
        ]);

        TipoVehiculo::create($validated);

        return redirect()->route('tipos-vehiculo.index')
            ->with('success', 'Tipo de vehículo creado exitosamente.');
    }

    public function edit(TipoVehiculo $tiposVehiculo)
    {
        return view('tipos-vehiculo.edit', compact('tiposVehiculo'));
    }

    public function update(Request $request, TipoVehiculo $tiposVehiculo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_vehiculo,nombre,' . $tiposVehiculo->id,
            'descripcion' => 'nullable|string|max:150',
        ]);

        $tiposVehiculo->update($validated);

        return redirect()->route('tipos-vehiculo.index')
            ->with('success', 'Tipo de vehículo actualizado exitosamente.');
    }

    public function destroy(TipoVehiculo $tiposVehiculo)
    {
        $tiposVehiculo->delete();

        return redirect()->route('tipos-vehiculo.index')
            ->with('success', 'Tipo de vehículo eliminado exitosamente.');
    }
}
