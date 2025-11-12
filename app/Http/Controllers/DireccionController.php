<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Usuario;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    public function index()
    {
        $direcciones = Direccion::with('usuario')->get();
        return view('direcciones.index', compact('direcciones'));
    }

    public function create()
    {
        $usuarios = Usuario::all();
        return view('direcciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'nombreorigen' => 'required|string|max:200',
            'origen_lng' => 'nullable|numeric',
            'origen_lat' => 'nullable|numeric',
            'nombredestino' => 'required|string|max:200',
            'destino_lng' => 'nullable|numeric',
            'destino_lat' => 'nullable|numeric',
            'rutageojson' => 'nullable|string',
        ]);

        Direccion::create($validated);

        return redirect()->route('direcciones.index')
            ->with('success', 'Dirección creada exitosamente.');
    }

    public function edit(Direccion $direccione)
    {
        $usuarios = Usuario::all();
        return view('direcciones.edit', compact('direccione', 'usuarios'));
    }

    public function update(Request $request, Direccion $direccione)
    {
        $validated = $request->validate([
            'id_usuario' => 'required|exists:usuarios,id',
            'nombreorigen' => 'required|string|max:200',
            'origen_lng' => 'nullable|numeric',
            'origen_lat' => 'nullable|numeric',
            'nombredestino' => 'required|string|max:200',
            'destino_lng' => 'nullable|numeric',
            'destino_lat' => 'nullable|numeric',
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
