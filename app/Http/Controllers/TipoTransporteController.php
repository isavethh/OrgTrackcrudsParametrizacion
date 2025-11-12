<?php

namespace App\Http\Controllers;

use App\Models\TipoTransporte;
use Illuminate\Http\Request;

class TipoTransporteController extends Controller
{
    public function index()
    {
        $tipoTransportes = TipoTransporte::all();
        return view('tipo-transportes.index', compact('tipoTransportes'));
    }

    public function create()
    {
        return view('tipo-transportes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ]);

        TipoTransporte::create($validated);

        return redirect()->route('tipo-transportes.index')
            ->with('success', 'Tipo de Transporte creado exitosamente.');
    }

    public function edit(TipoTransporte $tipoTransporte)
    {
        return view('tipo-transportes.edit', compact('tipoTransporte'));
    }

    public function update(Request $request, TipoTransporte $tipoTransporte)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipoTransporte->update($validated);

        return redirect()->route('tipo-transportes.index')
            ->with('success', 'Tipo de Transporte actualizado exitosamente.');
    }

    public function destroy(TipoTransporte $tipoTransporte)
    {
        $tipoTransporte->delete();

        return redirect()->route('tipo-transportes.index')
            ->with('success', 'Tipo de Transporte eliminado exitosamente.');
    }
}
