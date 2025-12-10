<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogoProducto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CatalogoProductoController extends Controller
{
    public function index()
    {
        $productos = CatalogoProducto::with('categoria:id,nombre')->orderBy('nombre')->get();
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_categoria' => 'required|integer|exists:catalogo_categorias,id',
            'nombre' => 'required|string|max:150',
        ]);

        $producto = CatalogoProducto::create($data);
        return response()->json($producto->load('categoria'), Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $producto = CatalogoProducto::find($id);
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'id_categoria' => 'required|integer|exists:catalogo_categorias,id',
            'nombre' => 'required|string|max:150',
        ]);

        $producto->update($data);
        return response()->json($producto->load('categoria'));
    }

    public function destroy(int $id)
    {
        $producto = CatalogoProducto::find($id);
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $producto->delete();
        return response()->json(['mensaje' => 'Producto eliminado correctamente']);
    }
}
