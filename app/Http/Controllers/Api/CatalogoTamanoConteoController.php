<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\CatalogoTamanoConteo;
use Illuminate\Http\Response;

class CatalogoTamanoConteoController extends Controller
{
    public function index()
    {
        $lista = CatalogoTamanoConteo::with('producto')->orderBy('conteo_por_empaque')->get();
        return response()->json($lista);
    }

    public function show(int $id)
    {
        $item = CatalogoTamanoConteo::with('producto')->find($id);
        if (!$item) {
            return response()->json(['error' => 'Elemento no encontrado'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'conteo_por_empaque' => 'required|integer|min:1',
            'peso_promedio_unidad' => 'required|numeric|min:0',
            'id_producto' => 'nullable|exists:catalogo_productos,id',
        ]);

        $item = CatalogoTamanoConteo::create($data);
        return response()->json($item, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $item = CatalogoTamanoConteo::find($id);
        if (!$item) {
            return response()->json(['error' => 'Elemento no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'conteo_por_empaque' => 'required|integer|min:1',
            'peso_promedio_unidad' => 'required|numeric|min:0',
            'id_producto' => 'nullable|exists:catalogo_productos,id',
        ]);

        $item->update($data);
        return response()->json($item);
    }

    public function destroy(int $id)
    {
        $item = CatalogoTamanoConteo::find($id);
        if (!$item) {
            return response()->json(['error' => 'Elemento no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $item->delete();
        return response()->json(['mensaje' => 'Elemento eliminado correctamente']);
    }
}
