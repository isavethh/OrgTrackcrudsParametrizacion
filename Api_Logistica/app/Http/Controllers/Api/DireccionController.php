<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $direcciones = Direccion::all();
        return response()->json([
            'success' => true,
            'data' => $direcciones
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreorigen' => 'nullable|string|max:200',
            'origen_lat' => 'nullable|numeric|between:-90,90',
            'origen_lng' => 'nullable|numeric|between:-180,180',
            'nombredestino' => 'nullable|string|max:200',
            'destino_lat' => 'nullable|numeric|between:-90,90',
            'destino_lng' => 'nullable|numeric|between:-180,180',
            'rutageojson' => 'nullable|string',
        ]);

        $direccion = Direccion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dirección creada exitosamente',
            'data' => $direccion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Direccion $direccion)
    {
        return response()->json([
            'success' => true,
            'data' => $direccion
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Direccion $direccion)
    {
        $validated = $request->validate([
            'nombreorigen' => 'nullable|string|max:200',
            'origen_lat' => 'nullable|numeric|between:-90,90',
            'origen_lng' => 'nullable|numeric|between:-180,180',
            'nombredestino' => 'nullable|string|max:200',
            'destino_lat' => 'nullable|numeric|between:-90,90',
            'destino_lng' => 'nullable|numeric|between:-180,180',
            'rutageojson' => 'nullable|string',
        ]);

        $direccion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dirección actualizada exitosamente',
            'data' => $direccion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Direccion $direccion)
    {
        $direccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dirección eliminada exitosamente'
        ]);
    }
}
