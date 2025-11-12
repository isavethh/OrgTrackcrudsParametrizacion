<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TamanoTransporte;

class TamanoTransporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tamanos = TamanoTransporte::all();
        return view('tamanos-transporte.index', compact('tamanos'));
    }

    public function create()
    {
        return view('tamanos-transporte.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tamano_transporte,nombre',
        ]);

        TamanoTransporte::create($request->all());

        return redirect()->route('tamanos-transporte.index')
            ->with('success', 'Tamaño de transporte creado exitosamente.');
    }

    public function edit(TamanoTransporte $tamanosTransporte)
    {
        return view('tamanos-transporte.edit', compact('tamanosTransporte'));
    }

    public function update(Request $request, TamanoTransporte $tamanosTransporte)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tamano_transporte,nombre,' . $tamanosTransporte->id,
        ]);

        $tamanosTransporte->update($request->all());

        return redirect()->route('tamanos-transporte.index')
            ->with('success', 'Tamaño de transporte actualizado exitosamente.');
    }

    public function destroy(TamanoTransporte $tamanosTransporte)
    {
        $tamanosTransporte->delete();

        return redirect()->route('tamanos-transporte.index')
            ->with('success', 'Tamaño de transporte eliminado exitosamente.');
    }
}
