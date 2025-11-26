<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TiposIncidenteTransporte;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TipoIncidenteTransporteController extends Controller
{
    public function index()
    {
        $tipos = TiposIncidenteTransporte::orderBy('id')->get();
        return response()->json($tipos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:tipos_incidente_transporte,codigo'],
            'titulo' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $tipo = TiposIncidenteTransporte::create($data);

        return response()->json([
            'mensaje' => 'Tipo de incidente creado correctamente',
            'tipo' => $tipo,
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $tipo = TiposIncidenteTransporte::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de incidente no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:tipos_incidente_transporte,codigo,' . $tipo->id],
            'titulo' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $tipo->update($data);

        return response()->json([
            'mensaje' => 'Tipo de incidente actualizado correctamente',
            'tipo' => $tipo,
        ]);
    }

    public function destroy(int $id)
    {
        $tipo = TiposIncidenteTransporte::find($id);
        if (!$tipo) {
            return response()->json(['error' => 'Tipo de incidente no encontrado'], Response::HTTP_NOT_FOUND);
        }

        if ($tipo->incidentes()->exists()) {
            return response()->json([
                'error' => 'No se puede eliminar, está siendo utilizado en incidentes registrados.',
            ], Response::HTTP_CONFLICT);
        }

        $tipo->delete();

        return response()->json(['mensaje' => 'Tipo de incidente eliminado correctamente']);
    }
}

