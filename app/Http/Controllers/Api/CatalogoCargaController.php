<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatalogoCarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatalogoCargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $catalogos = CatalogoCarga::orderBy('tipo', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $catalogos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el catálogo de cargas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|max:50',
            'variedad' => 'required|string|max:50',
            'empaque' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:150'
        ], [
            'tipo.required' => 'El tipo es obligatorio',
            'variedad.required' => 'La variedad es obligatoria',
            'empaque.required' => 'El empaque es obligatorio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si ya existe la combinación
            $existe = CatalogoCarga::where('tipo', $request->tipo)
                ->where('variedad', $request->variedad)
                ->where('empaque', $request->empaque)
                ->first();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un producto con esta combinación de tipo, variedad y empaque'
                ], 422);
            }

            $catalogo = CatalogoCarga::create([
                'tipo' => $request->tipo,
                'variedad' => $request->variedad,
                'empaque' => $request->empaque,
                'descripcion' => $request->descripcion
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al catálogo exitosamente',
                'data' => $catalogo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto en el catálogo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $catalogo = CatalogoCarga::find($id);

            if (!$catalogo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado en el catálogo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $catalogo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $catalogo = CatalogoCarga::find($id);

        if (!$catalogo) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado en el catálogo'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo' => 'required|string|max:50',
            'variedad' => 'required|string|max:50',
            'empaque' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:150'
        ], [
            'tipo.required' => 'El tipo es obligatorio',
            'variedad.required' => 'La variedad es obligatoria',
            'empaque.required' => 'El empaque es obligatorio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar si ya existe otra combinación igual
            $existe = CatalogoCarga::where('tipo', $request->tipo)
                ->where('variedad', $request->variedad)
                ->where('empaque', $request->empaque)
                ->where('id', '!=', $id)
                ->first();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un producto con esta combinación de tipo, variedad y empaque'
                ], 422);
            }

            $catalogo->update([
                'tipo' => $request->tipo,
                'variedad' => $request->variedad,
                'empaque' => $request->empaque,
                'descripcion' => $request->descripcion
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $catalogo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $catalogo = CatalogoCarga::find($id);

            if (!$catalogo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado en el catálogo'
                ], 404);
            }

            // Verificar si tiene cargas asociadas
            if ($catalogo->cargas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque tiene cargas asociadas'
                ], 409);
            }

            $catalogo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del catálogo exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
