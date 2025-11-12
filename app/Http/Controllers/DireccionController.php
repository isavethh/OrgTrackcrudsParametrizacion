<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Envio;
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
        $envios = Envio::all();
        return view('direcciones.create', compact('envios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'envio_id' => 'required|exists:envio,id',
            'nombre_ruta' => 'required|string|max:100',
            'ruta_geojson' => 'required|string',
        ]);

        // Validar que ruta_geojson sea JSON válido
        $rutaJson = json_decode($request->ruta_geojson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['ruta_geojson' => 'El formato de la ruta no es válido.'])->withInput();
        }

        Direccion::create($validated);

        return redirect()->route('direcciones.index')
            ->with('success', 'Dirección creada exitosamente.');
    }

    public function edit(Direccion $direccione)
    {
        $envios = Envio::all();
        return view('direcciones.edit', compact('direccione', 'envios'));
    }

    public function update(Request $request, Direccion $direccione)
    {
        $validated = $request->validate([
            'envio_id' => 'required|exists:envio,id',
            'nombre_ruta' => 'required|string|max:100',
            'ruta_geojson' => 'required|string',
        ]);

        // Validar que ruta_geojson sea JSON válido
        $rutaJson = json_decode($request->ruta_geojson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['ruta_geojson' => 'El formato de la ruta no es válido.'])->withInput();
        }

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
