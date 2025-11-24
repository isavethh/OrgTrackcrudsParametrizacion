<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\TipoVehiculo;
use App\Models\EstadoVehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with(['tipoVehiculo', 'estadoVehiculo'])->get();
        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        $tiposVehiculo = TipoVehiculo::all();
        $estadosVehiculo = EstadoVehiculo::all();
        
        return view('vehiculos.create', compact('tiposVehiculo', 'estadosVehiculo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:vehiculos,placa',
            'capacidad' => 'required|numeric|min:0',
            'id_tipo_vehiculo' => 'required|exists:tipos_vehiculo,id',
            'id_estado_vehiculo' => 'required|exists:estados_vehiculo,id',
        ]);

        Vehiculo::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo creado exitosamente.');
    }

    public function edit(Vehiculo $vehiculo)
    {
        $tiposVehiculo = TipoVehiculo::all();
        $estadosVehiculo = EstadoVehiculo::all();
        
        return view('vehiculos.edit', compact('vehiculo', 'tiposVehiculo', 'estadosVehiculo'));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $vehiculo->id,
            'capacidad' => 'required|numeric|min:0',
            'id_tipo_vehiculo' => 'required|exists:tipos_vehiculo,id',
            'id_estado_vehiculo' => 'required|exists:estados_vehiculo,id',
        ]);

        $vehiculo->update($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }
}
