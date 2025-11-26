<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TiposVehiculo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TiposVehiculoController extends Controller
{
    public function index()
    {
        $tipos = TiposVehiculo::orderBy('id')->get();
        return response()->json($tipos, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:tipos_vehiculo,nombre'],
            'descripcion' => ['nullable', 'string', 'max:150'],
        ]);

        $tipo = TiposVehiculo::create($validated);

        return response()->json([
            'mensaje' => 'Tipo de vehículo creado correctamente',
            'data' => $tipo
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $tipo = TiposVehiculo::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de vehículo no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:50', 'unique:tipos_vehiculo,nombre,' . $id],
            'descripcion' => ['nullable', 'string', 'max:150'],
        ]);

        $tipo->update($validated);

        return response()->json([
            'mensaje' => 'Tipo de vehículo actualizado correctamente',
            'data' => $tipo
        ]);
    }

    public function destroy(int $id)
    {
        $tipo = TiposVehiculo::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de vehículo no encontrado'], Response::HTTP_NOT_FOUND);
        }

        // Verificar si hay vehículos usando este tipo de vehículo
        if ($tipo->vehiculos()->count() > 0) {
            return response()->json([
                'error' => 'No se puede eliminar el tipo de vehículo porque hay vehículos asociados'
            ], Response::HTTP_BAD_REQUEST);
        }

        $tipo->delete();

        return response()->json(['mensaje' => 'Tipo de vehículo eliminado correctamente']);
    }
}

