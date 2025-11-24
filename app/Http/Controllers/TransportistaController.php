<?php

namespace App\Http\Controllers;

use App\Models\Transportista;
use App\Models\EstadoTransportista;
use Illuminate\Http\Request;

class TransportistaController extends Controller
{
    public function index()
    {
        $transportistas = Transportista::with('estadoTransportista')->get();
        return view('transportistas.index', compact('transportistas'));
    }

    public function create()
    {
        $estadosTransportista = EstadoTransportista::all();
        return view('transportistas.create', compact('estadosTransportista'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:transportistas,ci',
            'telefono' => 'required|string|max:20',
            'licencia' => 'required|string|max:50',
            'id_estado_transportista' => 'required|exists:estados_transportista,id',
        ]);

        Transportista::create($validated);

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista creado exitosamente.');
    }

    public function edit(Transportista $transportista)
    {
        $estadosTransportista = EstadoTransportista::all();
        return view('transportistas.edit', compact('transportista', 'estadosTransportista'));
    }

    public function update(Request $request, Transportista $transportista)
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:20|unique:transportistas,ci,' . $transportista->id,
            'telefono' => 'required|string|max:20',
            'licencia' => 'required|string|max:50',
            'id_estado_transportista' => 'required|exists:estados_transportista,id',
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
