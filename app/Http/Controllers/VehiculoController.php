<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Transportista;
use App\Models\TipoTransporte;
use App\Models\TamanoTransporte;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with(['transportista.usuario', 'tipoTransporte', 'tamanoTransporte'])->get();
        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        $transportistas = Transportista::with('usuario')->get();
        $tiposTransporte = TipoTransporte::all();
        $tamanosTransporte = TamanoTransporte::all();
        
        return view('vehiculos.create', compact('transportistas', 'tiposTransporte', 'tamanosTransporte'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transportista_id' => 'required|exists:transportista,id',
            'tipo_transporte_id' => 'required|exists:tipo_transporte,id',
            'tamano_transporte_id' => 'required|exists:tamano_transporte,id',
            'placa' => 'required|string|max:20|unique:vehiculo,placa',
            'marca' => 'nullable|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'estado' => 'required|in:Disponible,En ruta,No Disponible,Mantenimiento',
        ]);

        Vehiculo::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo creado exitosamente.');
    }

    public function edit(Vehiculo $vehiculo)
    {
        $transportistas = Transportista::with('usuario')->get();
        $tiposTransporte = TipoTransporte::all();
        $tamanosTransporte = TamanoTransporte::all();
        
        return view('vehiculos.edit', compact('vehiculo', 'transportistas', 'tiposTransporte', 'tamanosTransporte'));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'transportista_id' => 'required|exists:transportista,id',
            'tipo_transporte_id' => 'required|exists:tipo_transporte,id',
            'tamano_transporte_id' => 'required|exists:tamano_transporte,id',
            'placa' => 'required|string|max:20|unique:vehiculo,placa,' . $vehiculo->id,
            'marca' => 'nullable|string|max:50',
            'modelo' => 'nullable|string|max:50',
            'estado' => 'required|in:Disponible,En ruta,No Disponible,Mantenimiento',
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
