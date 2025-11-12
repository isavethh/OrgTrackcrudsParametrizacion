<?php

namespace App\Http\Controllers;

use App\Models\Transportista;
use App\Models\Usuario;
use App\Models\EstadoTransportista;
use Illuminate\Http\Request;

class TransportistaController extends Controller
{
    public function index()
    {
        $transportistas = Transportista::with('usuario', 'estado')->get();
        return view('transportistas.index', compact('transportistas'));
    }

    public function create()
    {
        $usuarios = Usuario::whereDoesntHave('transportista')
            ->whereDoesntHave('admin')
            ->whereDoesntHave('cliente')
            ->get();
        $estados = EstadoTransportista::all();
        return view('transportistas.create', compact('usuarios', 'estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuario,id|unique:transportista,usuario_id',
            'ci' => 'required|string|max:20|unique:transportista,ci',
            'placa' => 'required|string|max:20|unique:transportista,placa',
            'telefono' => 'nullable|string|max:20',
            'estado_id' => 'required|exists:estado_transportista,id',
        ]);

        Transportista::create($validated);

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista creado exitosamente.');
    }

    public function edit(Transportista $transportista)
    {
        $usuarios = Usuario::where(function($query) use ($transportista) {
                $query->whereDoesntHave('transportista')
                    ->orWhere('id', $transportista->usuario_id);
            })
            ->whereDoesntHave('admin')
            ->whereDoesntHave('cliente')
            ->get();
        $estados = EstadoTransportista::all();
        return view('transportistas.edit', compact('transportista', 'usuarios', 'estados'));
    }

    public function update(Request $request, Transportista $transportista)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuario,id|unique:transportista,usuario_id,' . $transportista->id,
            'ci' => 'required|string|max:20|unique:transportista,ci,' . $transportista->id,
            'placa' => 'required|string|max:20|unique:transportista,placa,' . $transportista->id,
            'telefono' => 'nullable|string|max:20',
            'estado_id' => 'required|exists:estado_transportista,id',
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
