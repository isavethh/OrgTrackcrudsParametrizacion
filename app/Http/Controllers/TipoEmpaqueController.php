<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoEmpaque;

class TipoEmpaqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos = TipoEmpaque::all();
        return view('tipos-empaque.index', compact('tipos'));
    }

    public function create()
    {
        return view('tipos-empaque.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_empaque,nombre',
        ]);

        TipoEmpaque::create($request->all());

        return redirect()->route('tipos-empaque.index')
            ->with('success', 'Tipo de empaque creado exitosamente.');
    }

    public function edit(TipoEmpaque $tiposEmpaque)
    {
        return view('tipos-empaque.edit', compact('tiposEmpaque'));
    }

    public function update(Request $request, TipoEmpaque $tiposEmpaque)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:tipo_empaque,nombre,' . $tiposEmpaque->id,
        ]);

        $tiposEmpaque->update($request->all());

        return redirect()->route('tipos-empaque.index')
            ->with('success', 'Tipo de empaque actualizado exitosamente.');
    }

    public function destroy(TipoEmpaque $tiposEmpaque)
    {
        $tiposEmpaque->delete();

        return redirect()->route('tipos-empaque.index')
            ->with('success', 'Tipo de empaque eliminado exitosamente.');
    }
}
