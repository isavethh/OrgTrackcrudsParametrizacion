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
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipotransporte,nombre',
        ]);

        TipoTransporte::create($request->all());

        return redirect()->route('tipo-transportes.index')
            ->with('success', 'Tipo de transporte creado exitosamente.');
    }

    public function edit(TipoTransporte $tipoTransporte)
    {
        return view('tipo-transportes.edit', compact('tipoTransporte'));
    }

    public function update(Request $request, TipoTransporte $tipoTransporte)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipotransporte,nombre,' . $tipoTransporte->id,
        ]);

        $tipoTransporte->update($request->all());

        return redirect()->route('tipo-transportes.index')
            ->with('success', 'Tipo de transporte actualizado exitosamente.');
    }

    public function destroy(TipoTransporte $tipoTransporte)
    {
        $tipoTransporte->delete();

        return redirect()->route('tipo-transportes.index')
            ->with('success', 'Tipo de Transporte eliminado exitosamente.');
    }
}
