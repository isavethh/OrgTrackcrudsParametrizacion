<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RutaTiempoRealController extends Controller
{
    /**
     * Display ALL envios with their tracking status.
     */
    public function index()
    {
        $envios = Envio::with(['direccion', 'usuario.persona'])
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return view('rutas-tiempo-real.index', compact('envios'));
    }

    /**
     * Start tracking for a specific envio.
     */
    public function start($id)
    {
        $envio = Envio::findOrFail($id);

        // Initialize tracking at origin location
        $envio->update([
            'estado_tracking' => 'en_ruta',
            'fecha_inicio_tracking' => now(),
            'ubicacion_actual_lng' => $envio->direccion->origen_lng,
            'ubicacion_actual_lat' => $envio->direccion->origen_lat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tracking iniciado exitosamente',
            'envio' => $envio->load('direccion')
        ]);
    }

    /**
     * Update current location for tracking.
     */
    public function updateLocation(Request $request, $id)
    {
        $validated = $request->validate([
            'lng' => 'required|numeric',
            'lat' => 'required|numeric',
        ]);

        // Optimizado: actualización directa sin Eloquent
        DB::table('envios')
            ->where('id', $id)
            ->update([
                'ubicacion_actual_lng' => $validated['lng'],
                'ubicacion_actual_lat' => $validated['lat'],
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Mark route as completed.
     */
    public function complete($id)
    {
        // Optimizado: solo actualizar campos necesarios sin cargar relaciones
        DB::table('envios')
            ->where('id', $id)
            ->update([
                'estado_tracking' => 'completada',
                'fecha_fin_tracking' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Ruta completada exitosamente'
        ]);
    }

    /**
     * Get current status of an envio (for polling).
     */
    public function getStatus($id)
    {
        $envio = Envio::with('direccion')->findOrFail($id);

        return response()->json([
            'success' => true,
            'envio' => $envio,
            'estado' => $envio->estado_tracking,
            'ubicacion_actual' => [
                'lng' => $envio->ubicacion_actual_lng,
                'lat' => $envio->ubicacion_actual_lat,
            ]
        ]);
    }

    /**
     * Calculate distance between two coordinates (in kilometers).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
