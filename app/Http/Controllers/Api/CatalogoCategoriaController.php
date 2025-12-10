<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogoCategoria;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CatalogoCategoriaController extends Controller
{
    public function index()
    {
        $categorias = CatalogoCategoria::orderBy('nombre')->get();
        return response()->json($categorias);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:catalogo_categorias,nombre',
        ]);

        $categoria = CatalogoCategoria::create($data);
        return response()->json($categoria, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $categoria = CatalogoCategoria::find($id);
        if (!$categoria) {
            return response()->json(['error' => 'Categoría no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:catalogo_categorias,nombre,' . $id,
        ]);

        $categoria->update($data);
        return response()->json($categoria);
    }

    public function destroy(int $id)
    {
        $categoria = CatalogoCategoria::find($id);
        if (!$categoria) {
            return response()->json(['error' => 'Categoría no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $categoria->delete();
        return response()->json(['mensaje' => 'Categoría eliminada correctamente']);
    }
}
