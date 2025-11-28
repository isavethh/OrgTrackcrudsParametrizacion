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
            $catalogos = CatalogoCarga::orderBy('nombre_producto', 'asc')->get();
            
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
            'nombre_producto' => 'required|string|max:100|unique:catalogo_carga,nombre_producto',
            'categoria' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:500',
            'temp_min' => 'nullable|numeric',
            'temp_max' => 'nullable|numeric',
            'humedad_min' => 'nullable|numeric|min:0|max:100',
            'humedad_max' => 'nullable|numeric|min:0|max:100',
            'requiere_refrigeracion' => 'boolean'
        ], [
            'nombre_producto.required' => 'El nombre del producto es obligatorio',
            'nombre_producto.unique' => 'Ya existe un producto con este nombre',
            'categoria.required' => 'La categoría es obligatoria',
            'temp_min.numeric' => 'La temperatura mínima debe ser un número',
            'temp_max.numeric' => 'La temperatura máxima debe ser un número',
            'humedad_min.min' => 'La humedad mínima debe ser mayor o igual a 0',
            'humedad_min.max' => 'La humedad mínima debe ser menor o igual a 100',
            'humedad_max.min' => 'La humedad máxima debe ser mayor o igual a 0',
            'humedad_max.max' => 'La humedad máxima debe ser menor o igual a 100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $catalogo = CatalogoCarga::create([
                'nombre_producto' => $request->nombre_producto,
                'categoria' => $request->categoria,
                'descripcion' => $request->descripcion,
                'temp_min' => $request->temp_min,
                'temp_max' => $request->temp_max,
                'humedad_min' => $request->humedad_min,
                'humedad_max' => $request->humedad_max,
                'requiere_refrigeracion' => $request->requiere_refrigeracion ?? false
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
            'nombre_producto' => 'required|string|max:100|unique:catalogo_carga,nombre_producto,' . $id . ',id_catalogo',
            'categoria' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:500',
            'temp_min' => 'nullable|numeric',
            'temp_max' => 'nullable|numeric',
            'humedad_min' => 'nullable|numeric|min:0|max:100',
            'humedad_max' => 'nullable|numeric|min:0|max:100',
            'requiere_refrigeracion' => 'boolean'
        ], [
            'nombre_producto.required' => 'El nombre del producto es obligatorio',
            'nombre_producto.unique' => 'Ya existe un producto con este nombre',
            'categoria.required' => 'La categoría es obligatoria',
            'temp_min.numeric' => 'La temperatura mínima debe ser un número',
            'temp_max.numeric' => 'La temperatura máxima debe ser un número',
            'humedad_min.min' => 'La humedad mínima debe ser mayor o igual a 0',
            'humedad_min.max' => 'La humedad mínima debe ser menor o igual a 100',
            'humedad_max.min' => 'La humedad máxima debe ser mayor o igual a 0',
            'humedad_max.max' => 'La humedad máxima debe ser menor o igual a 100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $catalogo->update([
                'nombre_producto' => $request->nombre_producto,
                'categoria' => $request->categoria,
                'descripcion' => $request->descripcion,
                'temp_min' => $request->temp_min,
                'temp_max' => $request->temp_max,
                'humedad_min' => $request->humedad_min,
                'humedad_max' => $request->humedad_max,
                'requiere_refrigeracion' => $request->requiere_refrigeracion ?? false
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
