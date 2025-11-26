<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\TiposVehiculo;
use App\Models\EstadosVehiculo;
use App\Models\Tipotransporte;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with(['tipoVehiculo', 'estadoVehiculo', 'tipoTransporte'])
            ->orderByDesc('id')
            ->get();
        
        $vehiculos = $vehiculos->map(function ($vehiculo) {
            return [
                'id' => $vehiculo->id,
                'tipo' => $vehiculo->tipoVehiculo?->nombre,
                'placa' => $vehiculo->placa,
                'capacidad' => $vehiculo->capacidad,
                'estado' => $vehiculo->estadoVehiculo?->nombre,
                'tipo_transporte' => [
                    'id' => $vehiculo->tipoTransporte?->id,
                    'nombre' => $vehiculo->tipoTransporte?->nombre,
                    'descripcion' => $vehiculo->tipoTransporte?->descripcion,
                ],
                'fecha_registro' => $vehiculo->fecha_registro,
            ];
        });
        
        return response()->json($vehiculos);
    }

    public function show(int $id)
    {
        $vehiculo = Vehiculo::with(['tipoVehiculo', 'estadoVehiculo', 'tipoTransporte'])->find($id);
        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'id' => $vehiculo->id,
            'tipo' => $vehiculo->tipoVehiculo?->nombre,
            'placa' => $vehiculo->placa,
            'capacidad' => $vehiculo->capacidad,
            'estado' => $vehiculo->estadoVehiculo?->nombre,
            'tipo_transporte' => [
                'id' => $vehiculo->tipoTransporte?->id,
                'nombre' => $vehiculo->tipoTransporte?->nombre,
                'descripcion' => $vehiculo->tipoTransporte?->descripcion,
            ],
            'fecha_registro' => $vehiculo->fecha_registro,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_tipo_vehiculo' => ['nullable', 'integer', 'exists:tipos_vehiculo,id'],
            'tipo_vehiculo' => ['nullable', 'string', 'max:50'],
            'id_tipo_transporte' => ['nullable', 'integer', 'exists:tipotransporte,id'],
            'tipo_transporte' => ['nullable', 'string', 'max:50'],
            'descripcion_tipo_transporte' => ['nullable', 'string', 'max:255'],
            'placa' => ['required', 'string', 'max:20', 'unique:vehiculos,placa'],
            'capacidad' => ['required', 'numeric'],
        ]);

        // Validar que se proporcione tipo_vehiculo (ID o nombre)
        if (empty($validated['id_tipo_vehiculo']) && empty($validated['tipo_vehiculo'])) {
            return response()->json([
                'error' => 'Debe proporcionar id_tipo_vehiculo o tipo_vehiculo'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validar que se proporcione tipo_transporte (ID o nombre)
        if (empty($validated['id_tipo_transporte']) && empty($validated['tipo_transporte'])) {
            return response()->json([
                'error' => 'Debe proporcionar id_tipo_transporte o tipo_transporte'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Obtener o crear tipo de vehículo
        if (!empty($validated['id_tipo_vehiculo'])) {
            $idTipoVehiculo = $validated['id_tipo_vehiculo'];
        } else {
            $tipoVehiculo = TiposVehiculo::firstOrCreate(
                ['nombre' => $validated['tipo_vehiculo']],
                ['descripcion' => null]
            );
            $idTipoVehiculo = $tipoVehiculo->id;
        }

        // Obtener o crear tipo de transporte
        if (!empty($validated['id_tipo_transporte'])) {
            $idTipoTransporte = $validated['id_tipo_transporte'];
        } else {
            $tipoTransporte = Tipotransporte::firstOrCreate(
                ['nombre' => $validated['tipo_transporte']],
                ['descripcion' => $validated['descripcion_tipo_transporte'] ?? null]
            );
            $idTipoTransporte = $tipoTransporte->id;
        }

        $idEstadoDisponible = EstadoHelper::obtenerEstadoVehiculoPorNombre('Disponible');
        $vehiculo = Vehiculo::create([
            'id_tipo_vehiculo' => $idTipoVehiculo,
            'id_tipo_transporte' => $idTipoTransporte,
            'placa' => $validated['placa'],
            'capacidad' => $validated['capacidad'],
            'id_estado_vehiculo' => $idEstadoDisponible,
        ]);
        
        $vehiculo->load(['tipoVehiculo', 'estadoVehiculo', 'tipoTransporte']);
        return response()->json([
            'mensaje' => 'Vehículo creado correctamente',
            'data' => [
                'id' => $vehiculo->id,
                'tipo' => $vehiculo->tipoVehiculo?->nombre,
                'placa' => $vehiculo->placa,
                'capacidad' => $vehiculo->capacidad,
                'estado' => $vehiculo->estadoVehiculo?->nombre,
                'tipo_transporte' => [
                    'id' => $vehiculo->tipoTransporte?->id,
                    'nombre' => $vehiculo->tipoTransporte?->nombre,
                    'descripcion' => $vehiculo->tipoTransporte?->descripcion,
                ],
            ]
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $vehiculo = Vehiculo::with('estadoVehiculo')->find($id);
        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], Response::HTTP_NOT_FOUND);
        }

        if ($vehiculo->estadoVehiculo?->nombre === 'En ruta') {
            return response()->json(['error' => 'No se puede modificar un vehículo que está en ruta'], Response::HTTP_BAD_REQUEST);
        }

        $validated = $request->validate([
            'id_tipo_vehiculo' => ['nullable', 'integer', 'exists:tipos_vehiculo,id'],
            'tipo_vehiculo' => ['nullable', 'string', 'max:50'],
            'id_tipo_transporte' => ['nullable', 'integer', 'exists:tipotransporte,id'],
            'tipo_transporte' => ['nullable', 'string', 'max:50'],
            'descripcion_tipo_transporte' => ['nullable', 'string', 'max:255'],
            'placa' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('vehiculos', 'placa')->ignore($vehiculo->id)],
            'capacidad' => ['sometimes', 'required', 'numeric'],
        ]);

        // Procesar tipo de vehículo si se proporciona
        if (isset($validated['id_tipo_vehiculo']) || isset($validated['tipo_vehiculo'])) {
            if (!empty($validated['id_tipo_vehiculo'])) {
                $vehiculo->id_tipo_vehiculo = $validated['id_tipo_vehiculo'];
            } else {
                $tipoVehiculo = TiposVehiculo::firstOrCreate(
                    ['nombre' => $validated['tipo_vehiculo']],
                    ['descripcion' => null]
                );
                $vehiculo->id_tipo_vehiculo = $tipoVehiculo->id;
            }
        }

        // Procesar tipo de transporte si se proporciona
        if (isset($validated['id_tipo_transporte']) || isset($validated['tipo_transporte'])) {
            if (!empty($validated['id_tipo_transporte'])) {
                $vehiculo->id_tipo_transporte = $validated['id_tipo_transporte'];
            } else {
                $tipoTransporte = Tipotransporte::firstOrCreate(
                    ['nombre' => $validated['tipo_transporte']],
                    ['descripcion' => $validated['descripcion_tipo_transporte'] ?? null]
                );
                $vehiculo->id_tipo_transporte = $tipoTransporte->id;
            }
        }

        // Actualizar otros campos
        if (isset($validated['placa'])) {
            $vehiculo->placa = $validated['placa'];
        }
        if (isset($validated['capacidad'])) {
            $vehiculo->capacidad = $validated['capacidad'];
        }

        $vehiculo->save();
        $vehiculo->load(['tipoVehiculo', 'estadoVehiculo', 'tipoTransporte']);

        return response()->json([
            'mensaje' => 'Vehículo actualizado correctamente',
            'data' => [
                'id' => $vehiculo->id,
                'tipo' => $vehiculo->tipoVehiculo?->nombre,
                'placa' => $vehiculo->placa,
                'capacidad' => $vehiculo->capacidad,
                'estado' => $vehiculo->estadoVehiculo?->nombre,
                'tipo_transporte' => [
                    'id' => $vehiculo->tipoTransporte?->id,
                    'nombre' => $vehiculo->tipoTransporte?->nombre,
                    'descripcion' => $vehiculo->tipoTransporte?->descripcion,
                ],
            ]
        ]);
    }

    public function destroy(int $id)
    {
        $vehiculo = Vehiculo::with('estadoVehiculo')->find($id);
        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], Response::HTTP_NOT_FOUND);
        }
        if ($vehiculo->estadoVehiculo?->nombre === 'En ruta') {
            return response()->json(['error' => 'No se puede eliminar un vehículo que está en ruta'], Response::HTTP_BAD_REQUEST);
        }
        $vehiculo->delete();
        return response()->json(['mensaje' => 'Vehículo eliminado correctamente']);
    }
}


