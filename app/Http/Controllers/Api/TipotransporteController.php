<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tipotransporte;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TipotransporteController extends Controller
{
    public function index()
    {
        $tipos = Tipotransporte::orderBy('id')->get();
        return response()->json($tipos, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:tipotransporte,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $tipo = Tipotransporte::create($validated);

        return response()->json([
            'mensaje' => 'Tipo de transporte creado correctamente',
            'data' => $tipo
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $tipo = Tipotransporte::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de transporte no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:50', 'unique:tipotransporte,nombre,' . $id],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $tipo->update($validated);

        return response()->json([
            'mensaje' => 'Tipo de transporte actualizado correctamente',
            'data' => $tipo
        ]);
    }

    public function destroy(int $id)
    {
        $tipo = Tipotransporte::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de transporte no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Verificar si hay vehículos usando este tipo de transporte
        if ($tipo->vehiculos()->count() > 0) {
            return response()->json([
                'error' => 'No se puede eliminar el tipo de transporte porque hay vehículos asociados'
            ], Response::HTTP_BAD_REQUEST);
        }

        $tipo->delete();

        return response()->json(['mensaje' => 'Tipo de transporte eliminado correctamente']);
    }
}


