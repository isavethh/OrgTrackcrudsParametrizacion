<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CondicionesTransporte;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CondicionTransporteController extends Controller
{
    public function index()
    {
        $condiciones = CondicionesTransporte::orderBy('id')->get();
        return response()->json($condiciones);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:condiciones_transporte,codigo'],
            'titulo' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $condicion = CondicionesTransporte::create($data);

        return response()->json([
            'mensaje' => 'Condición creada correctamente',
            'condicion' => $condicion,
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $condicion = CondicionesTransporte::find($id);
        if (!$condicion) {
            return response()->json(['error' => 'Condición no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:condiciones_transporte,codigo,' . $condicion->id],
            'titulo' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $condicion->update($data);

        return response()->json([
            'mensaje' => 'Condición actualizada correctamente',
            'condicion' => $condicion,
        ]);
    }

    public function destroy(int $id)
    {
        $condicion = CondicionesTransporte::find($id);
        if (!$condicion) {
            return response()->json(['error' => 'Condición no encontrada'], Response::HTTP_NOT_FOUND);
        }

        if ($condicion->checklistDetalles()->exists()) {
            return response()->json([
                'error' => 'No se puede eliminar, está siendo utilizada en checklist.',
            ], Response::HTTP_CONFLICT);
        }

        $condicion->delete();

        return response()->json(['mensaje' => 'Condición eliminada correctamente']);
    }
}

