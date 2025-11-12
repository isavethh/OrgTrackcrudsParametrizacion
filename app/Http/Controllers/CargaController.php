<?php

namespace App\Http\Controllers;

use App\Models\Carga;
use Illuminate\Http\Request;

class CargaController extends Controller
{
    public function index()
    {
        $cargas = Carga::all();
        return view('cargas.index', compact('cargas'));
    }

    public function create()
    {
        return view('cargas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50',
            'variedad' => 'required|string|max:50',
            'cantidad' => 'required|integer|min:1',
            'empaquetado' => 'required|string|max:50',
            'peso' => 'required|numeric|min:0',
        ]);

        Carga::create($validated);

        return redirect()->route('cargas.index')
            ->with('success', 'Carga creada exitosamente.');
    }

    public function edit(Carga $carga)
    {
        return view('cargas.edit', compact('carga'));
    }

    public function update(Request $request, Carga $carga)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50',
            'variedad' => 'required|string|max:50',
            'cantidad' => 'required|integer|min:1',
            'empaquetado' => 'required|string|max:50',
            'peso' => 'required|numeric|min:0',
        ]);

        $carga->update($validated);

        return redirect()->route('cargas.index')
            ->with('success', 'Carga actualizada exitosamente.');
    }

    public function destroy(Carga $carga)
    {
        $carga->delete();

        return redirect()->route('cargas.index')
            ->with('success', 'Carga eliminada exitosamente.');
    }
}
