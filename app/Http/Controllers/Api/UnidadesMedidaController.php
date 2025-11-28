<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnidadesMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $unidades = UnidadMedida::orderBy('nombre', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $unidades
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las unidades de medida',
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
            'codigo' => 'required|string|max:20|unique:unidades_medida,codigo',
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:150'
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'Ya existe una unidad de medida con este código',
            'nombre.required' => 'El nombre es obligatorio',
            'tipo.required' => 'El tipo es obligatorio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $unidad = UnidadMedida::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unidad de medida creada exitosamente',
                'data' => $unidad
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la unidad de medida',
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
            $unidad = UnidadMedida::find($id);

            if (!$unidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unidad de medida no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $unidad
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la unidad de medida',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $unidad = UnidadMedida::find($id);

        if (!$unidad) {
            return response()->json([
                'success' => false,
                'message' => 'Unidad de medida no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:20|unique:unidades_medida,codigo,' . $id,
            'nombre' => 'required|string|max:50',
            'tipo' => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:150'
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'Ya existe una unidad de medida con este código',
            'nombre.required' => 'El nombre es obligatorio',
            'tipo.required' => 'El tipo es obligatorio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $unidad->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'descripcion' => $request->descripcion
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unidad de medida actualizada exitosamente',
                'data' => $unidad
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la unidad de medida',
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
            $unidad = UnidadMedida::find($id);

            if (!$unidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unidad de medida no encontrada'
                ], 404);
            }

            // Verificar si tiene cargas asociadas
            if ($unidad->cargas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la unidad de medida porque tiene cargas asociadas'
                ], 409);
            }

            $unidad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unidad de medida eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la unidad de medida',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
