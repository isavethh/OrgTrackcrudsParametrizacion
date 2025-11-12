<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Admin;
use App\Models\TipoTransporte;
use App\Models\TamanoTransporte;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with(['admin.usuario', 'tipoTransporte', 'tamanoTransporte'])->get();
        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        $admins = Admin::with('usuario')->get();
        $tiposTransporte = TipoTransporte::all();
        $tamanosTransporte = TamanoTransporte::all();
        
        return view('vehiculos.create', compact('admins', 'tiposTransporte', 'tamanosTransporte'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'tipo_transporte_id' => 'required|exists:tipo_transporte,id',
            'tamano_transporte_id' => 'required|exists:tamano_transporte,id',
            'placa' => 'required|string|max:20|unique:vehiculo,placa',
            'estado' => 'required|in:Disponible,En ruta,No Disponible,Mantenimiento',
        ]);

        Vehiculo::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo creado exitosamente.');
    }

    public function edit(Vehiculo $vehiculo)
    {
        $admins = Admin::with('usuario')->get();
        $tiposTransporte = TipoTransporte::all();
        $tamanosTransporte = TamanoTransporte::all();
        
        return view('vehiculos.edit', compact('vehiculo', 'admins', 'tiposTransporte', 'tamanosTransporte'));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'tipo_transporte_id' => 'required|exists:tipo_transporte,id',
            'tamano_transporte_id' => 'required|exists:tamano_transporte,id',
            'placa' => 'required|string|max:20|unique:vehiculo,placa,' . $vehiculo->id,
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
