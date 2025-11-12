<?php

namespace App\Http\Controllers;

use App\Models\Transportista;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TransportistaController extends Controller
{
    public function index()
    {
        $transportistas = Transportista::with('usuario')->get();
        return view('transportistas.index', compact('transportistas'));
    }

    public function create()
    {
        $usuarios = Usuario::whereDoesntHave('transportista')
            ->where('rol', 'transportista')
            ->get();
        return view('transportistas.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_usuario' => 'nullable|exists:usuarios,id|unique:transportistas,id_usuario',
            'ci' => 'required|string|max:20|unique:transportistas,ci',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:Inactivo,No Disponible,En ruta,Disponible',
        ]);

        Transportista::create($validated);

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista creado exitosamente.');
    }

    public function edit(Transportista $transportista)
    {
        $usuarios = Usuario::whereDoesntHave('transportista')
            ->orWhere('id', $transportista->id_usuario)
            ->where('rol', 'transportista')
            ->get();
        return view('transportistas.edit', compact('transportista', 'usuarios'));
    }

    public function update(Request $request, Transportista $transportista)
    {
        $validated = $request->validate([
            'id_usuario' => 'nullable|exists:usuarios,id|unique:transportistas,id_usuario,' . $transportista->id,
            'ci' => 'required|string|max:20|unique:transportistas,ci,' . $transportista->id,
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:Inactivo,No Disponible,En ruta,Disponible',
        ]);

        $transportista->update($validated);

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista actualizado exitosamente.');
    }

    public function destroy(Transportista $transportista)
    {
        $transportista->delete();

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista eliminado exitosamente.');
    }
}
