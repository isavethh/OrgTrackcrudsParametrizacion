<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogoTipoEmpaque;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CatalogoTipoEmpaqueController extends Controller
{
    public function index()
    {
        $tipos = CatalogoTipoEmpaque::orderBy('nombre')->get();
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:catalogo_tipos_empaque,nombre',
            'largo' => 'nullable|numeric|min:0',
            'ancho' => 'nullable|numeric|min:0',
            'alto' => 'nullable|numeric|min:0',
            'tara' => 'nullable|numeric|min:0',
            'capacidad' => 'nullable|integer|min:0',
            'unidades_por_pallet' => 'nullable|integer|min:0',
        ]);

        $tipo = CatalogoTipoEmpaque::create($data);
        return response()->json($tipo, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $tipo = CatalogoTipoEmpaque::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de empaque no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:catalogo_tipos_empaque,nombre,' . $id,
            'largo' => 'nullable|numeric|min:0',
            'ancho' => 'nullable|numeric|min:0',
            'alto' => 'nullable|numeric|min:0',
            'tara' => 'nullable|numeric|min:0',
            'capacidad' => 'nullable|integer|min:0',
            'unidades_por_pallet' => 'nullable|integer|min:0',
        ]);

        $tipo->update($data);
        return response()->json($tipo);
    }

    public function destroy(int $id)
    {
        $tipo = CatalogoTipoEmpaque::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de empaque no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $tipo->delete();
        return response()->json(['mensaje' => 'Tipo de empaque eliminado correctamente']);
    }
}
