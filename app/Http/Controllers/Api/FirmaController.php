<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FirmaEnvio;
use App\Models\FirmaTransportista;
use App\Models\AsignacionMultiple;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FirmaController extends Controller
{
    /**
     * Guardar firma de envío
     */
    public function guardarFirmaEnvio(Request $request, int $id_asignacion): JsonResponse
    {
        try {
            $request->validate([
                'imagenFirma' => 'required|string',
            ]);

            // Verificar que la asignación existe
            $asignacion = AsignacionMultiple::find($id_asignacion);
            if (!$asignacion) {
                return response()->json(['error' => 'Asignación no encontrada'], 404);
            }

            // Verificar si ya existe firma para esta asignación
            $firmaExistente = FirmaEnvio::where('id_asignacion', $id_asignacion)->first();
            if ($firmaExistente) {
                return response()->json(['error' => 'Ya existe una firma para esta asignación'], 400);
            }

            // Guardar nueva firma
            FirmaEnvio::create([
                'id_asignacion' => $id_asignacion,
                'imagenfirma' => $request->imagenFirma,
                'fechafirma' => now(),
            ]);

            return response()->json([
                'mensaje' => 'Firma guardada correctamente',
                'id_asignacion' => $id_asignacion
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al guardar firma: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al guardar la firma'], 500);
        }
    }

    /**
     * Guardar firma de transportista
     */
    public function guardarFirmaTransportista(Request $request, int $id_asignacion): JsonResponse
    {
        try {
            $request->validate([
                'imagenFirma' => 'required|string',
            ]);

            // Verificar que la asignación existe
            $asignacion = AsignacionMultiple::find($id_asignacion);
            if (!$asignacion) {
                return response()->json(['error' => 'Asignación no encontrada'], 404);
            }

            // Verificar si ya existe firma para esta asignación
            $firmaExistente = FirmaTransportista::where('id_asignacion', $id_asignacion)->first();
            if ($firmaExistente) {
                return response()->json(['error' => 'Ya existe una firma de transportista para esta asignación'], 400);
            }

            // Guardar nueva firma
            FirmaTransportista::create([
                'id_asignacion' => $id_asignacion,
                'imagenfirma' => $request->imagenFirma,
                'fechafirma' => now(),
            ]);

            return response()->json([
                'mensaje' => 'Firma de transportista guardada correctamente',
                'id_asignacion' => $id_asignacion
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al guardar firma de transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al guardar la firma'], 500);
        }
    }

    /**
     * Obtener firma de envío
     */
    public function obtenerFirmaEnvio(int $id_asignacion): JsonResponse
    {
        try {
            $firma = FirmaEnvio::where('id_asignacion', $id_asignacion)->first();

            if (!$firma) {
                return response()->json(['error' => 'Firma no encontrada'], 404);
            }

            return response()->json([
                'id_asignacion' => $firma->id_asignacion,
                'imagenFirma' => $firma->imagenfirma,
                'fechaFirma' => $firma->fechafirma,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener firma: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener la firma'], 500);
        }
    }

    /**
     * Obtener firma de transportista
     */
    public function obtenerFirmaTransportista(int $id_asignacion): JsonResponse
    {
        try {
            $firma = FirmaTransportista::where('id_asignacion', $id_asignacion)->first();

            if (!$firma) {
                return response()->json(['error' => 'Firma de transportista no encontrada'], 404);
            }

            return response()->json([
                'id_asignacion' => $firma->id_asignacion,
                'imagenFirma' => $firma->imagenfirma,
                'fechaFirma' => $firma->fechafirma,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener firma de transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener la firma'], 500);
        }
    }

    /**
     * Obtener firma por asignación (método específico para transportistas)
     */
    public function obtenerFirmaPorAsignacion(int $id_asignacion): JsonResponse
    {
        try {
            if (!is_numeric($id_asignacion)) {
                return response()->json(['error' => 'ID de asignación inválido'], 400);
            }

            $firma = FirmaTransportista::where('id_asignacion', $id_asignacion)->first();

            if (!$firma) {
                return response()->json(['error' => 'No se encontró una firma para esta asignación'], 404);
            }

            return response()->json([
                'id_asignacion' => $firma->id_asignacion,
                'imagenFirma' => $firma->imagenfirma,
                'fechaFirma' => $firma->fechafirma,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener firma del transportista: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al obtener la firma'], 500);
        }
    }

    /**
     * Actualizar firma de envío
     */
    public function actualizarFirmaEnvio(Request $request, int $id_asignacion): JsonResponse
    {
        try {
            $request->validate([
                'imagenFirma' => 'required|string',
            ]);

            $firma = FirmaEnvio::where('id_asignacion', $id_asignacion)->first();

            if (!$firma) {
                return response()->json(['error' => 'Firma no encontrada'], 404);
            }

            $firma->update([
                'imagenfirma' => $request->imagenFirma,
                'fechafirma' => now(),
            ]);

            return response()->json([
                'mensaje' => 'Firma actualizada correctamente',
                'id_asignacion' => $id_asignacion
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos de validación incorrectos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar firma: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al actualizar la firma'], 500);
        }
    }

    /**
     * Eliminar firma de envío
     */
    public function eliminarFirmaEnvio(int $id_asignacion): JsonResponse
    {
        try {
            $firma = FirmaEnvio::where('id_asignacion', $id_asignacion)->first();

            if (!$firma) {
                return response()->json(['error' => 'Firma no encontrada'], 404);
            }

            $firma->delete();

            return response()->json([
                'mensaje' => 'Firma eliminada correctamente',
                'id_asignacion' => $id_asignacion
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al eliminar firma: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al eliminar la firma'], 500);
        }
    }
}
