<?php

namespace App\Http\Controllers;

use App\Models\EstadoVehiculo;
use Illuminate\Http\Request;

class EstadoVehiculoController extends Controller
{
    public function index()
    {
        $estadosVehiculo = EstadoVehiculo::all();
        return view('estados-vehiculo.index', compact('estadosVehiculo'));
    }

    public function create()
    {
        return view('estados-vehiculo.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:estados_vehiculo,nombre',
        ]);

        EstadoVehiculo::create($validated);

        return redirect()->route('estados-vehiculo.index')
            ->with('success', 'Estado de vehículo creado exitosamente.');
    }

    public function edit(EstadoVehiculo $estadosVehiculo)
    {
        return view('estados-vehiculo.edit', compact('estadosVehiculo'));
    }

    public function update(Request $request, EstadoVehiculo $estadosVehiculo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:estados_vehiculo,nombre,' . $estadosVehiculo->id,
        ]);

        $estadosVehiculo->update($validated);

        return redirect()->route('estados-vehiculo.index')
            ->with('success', 'Estado de vehículo actualizado exitosamente.');
    }

    public function destroy(EstadoVehiculo $estadosVehiculo)
    {
        $estadosVehiculo->delete();

        return redirect()->route('estados-vehiculo.index')
            ->with('success', 'Estado de vehículo eliminado exitosamente.');
    }
}
