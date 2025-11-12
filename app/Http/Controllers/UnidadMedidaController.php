<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadMedida;

class UnidadMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidades = UnidadMedida::all();
        return view('unidades-medida.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades-medida.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:20|unique:unidad_medida,nombre',
        ]);

        UnidadMedida::create($request->all());

        return redirect()->route('unidades-medida.index')
            ->with('success', 'Unidad de medida creada exitosamente.');
    }

    public function edit(UnidadMedida $unidadesMedidum)
    {
        return view('unidades-medida.edit', compact('unidadesMedidum'));
    }

    public function update(Request $request, UnidadMedida $unidadesMedidum)
    {
        $request->validate([
            'nombre' => 'required|string|max:20|unique:unidad_medida,nombre,' . $unidadesMedidum->id,
        ]);

        $unidadesMedidum->update($request->all());

        return redirect()->route('unidades-medida.index')
            ->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    public function destroy(UnidadMedida $unidadesMedidum)
    {
        $unidadesMedidum->delete();

        return redirect()->route('unidades-medida.index')
            ->with('success', 'Unidad de medida eliminada exitosamente.');
    }
}
