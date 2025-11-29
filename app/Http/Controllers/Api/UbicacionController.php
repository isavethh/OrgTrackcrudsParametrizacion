<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Direccion;
use App\Models\DireccionSegmento;
use App\Models\Envio;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UbicacionController extends Controller
{
    private function obtenerUsuarioId(Request $request)
    {
        $authHeader = $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = substr($authHeader, 7);

        $secret = env('SECRET_KEY') ?: env('JWT_SECRET');
        if (!$secret) {
            $secret = (string) config('app.key');
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }
        }

        try {
            $payload = JWT::decode($token, new Key($secret, 'HS256'));
            return (int) ($payload->sub ?? 0);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function index(Request $request)
    {
        $usuarioId = $this->obtenerUsuarioId($request);
        if (!$usuarioId) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        
        // Obtener direcciones del usuario (directamente o a través de envíos)
        $direccionesIds = Envio::where('id_usuario', $usuarioId)
            ->distinct()
            ->pluck('id_direccion');
        
        $items = Direccion::where('id_usuario', $usuarioId)
            ->orWhereIn('id', $direccionesIds)
            ->with('segmentos')
            ->orderByDesc('id')
            ->get();

        // Deduplicar visualmente: Agrupar por nombres y preferir el que tenga coordenadas
        // Esto soluciona el caso donde hay una versión con coordenadas y otra sin ellas (duplicado visual)
        $items = $items->groupBy(function ($item) {
            return strtolower(trim($item->nombreorigen ?? '')) . '|' . strtolower(trim($item->nombredestino ?? ''));
        })->map(function ($group) {
            // De cada grupo de "duplicados por nombre", elegimos el mejor:
            // Prioridad 1: Que tenga coordenadas (latitud no nula)
            // Prioridad 2: El más reciente (mayor ID)
            return $group->sortByDesc(function ($item) {
                $tieneCoordenadas = !empty($item->origen_lat) && !empty($item->origen_lng);
                return ($tieneCoordenadas ? 1000000000 : 0) + $item->id;
            })->first();
        })->values();
        
        return response()->json($items);
    }

    public function show(Request $request, int $id)
    {
        $usuarioId = $this->obtenerUsuarioId($request);
        if (!$usuarioId) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        
        // Verificar que la dirección pertenece al usuario o a un envío del usuario
        $direccion = Direccion::with('segmentos')->find($id);
        if (!$direccion) {
            return response()->json(['error' => 'Dirección no encontrada'], Response::HTTP_NOT_FOUND);
        }
        
        $perteneceAlUsuario = $direccion->id_usuario == $usuarioId || 
            Envio::where('id_usuario', $usuarioId)
                ->where('id_direccion', $id)
                ->exists();
        
        if (!$perteneceAlUsuario) {
            return response()->json(['error' => 'Dirección no encontrada o no autorizada'], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json($direccion);
    }

    public function store(Request $request)
    {
        $usuarioId = $this->obtenerUsuarioId($request);
        if (!$usuarioId) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        $data = $request->validate([
            'nombreOrigen' => ['nullable','string','max:200'],
            'origen_lng' => ['nullable','numeric'],
            'origen_lat' => ['nullable','numeric'],
            'nombreDestino' => ['nullable','string','max:200'],
            'destino_lng' => ['nullable','numeric'],
            'destino_lat' => ['nullable','numeric'],
            'rutaGeoJSON' => ['nullable','string'],
            'segmentos' => ['nullable','array'],
            'segmentos.*.segmentogeojson' => ['required_with:segmentos','string'],
        ]);

        // Verificar si ya existe una dirección idéntica para este usuario
        // Esto evita duplicados si el usuario guarda la misma dirección varias veces
        $existente = Direccion::where('id_usuario', $usuarioId)
            ->where('nombreorigen', $data['nombreOrigen'] ?? null)
            ->where('nombredestino', $data['nombreDestino'] ?? null)
            ->where('origen_lat', $data['origen_lat'] ?? null)
            ->where('origen_lng', $data['origen_lng'] ?? null)
            ->where('destino_lat', $data['destino_lat'] ?? null)
            ->where('destino_lng', $data['destino_lng'] ?? null)
            ->first();

        if ($existente) {
            return response()->json($existente, Response::HTTP_OK);
        }

        $direccion = Direccion::create([
            'id_usuario' => $usuarioId,
            'nombreorigen' => $data['nombreOrigen'] ?? null,
            'origen_lng' => $data['origen_lng'] ?? null,
            'origen_lat' => $data['origen_lat'] ?? null,
            'nombredestino' => $data['nombreDestino'] ?? null,
            'destino_lng' => $data['destino_lng'] ?? null,
            'destino_lat' => $data['destino_lat'] ?? null,
            'rutageojson' => $data['rutaGeoJSON'] ?? null,
        ]);

        // Si vienen segmentos explícitos (móvil), guardarlos
        if (!empty($data['segmentos'])) {
            foreach ($data['segmentos'] as $seg) {
                DireccionSegmento::create([
                    'direccion_id' => $direccion->id,
                    'segmentogeojson' => $seg['segmentogeojson'],
                ]);
            }
        } 
        // Si NO vienen segmentos pero SÍ hay rutaGeoJSON (web), crear un segmento automático
        // Esto asegura compatibilidad con el móvil que espera segmentos
        elseif (!empty($data['rutaGeoJSON'])) {
            DireccionSegmento::create([
                'direccion_id' => $direccion->id,
                'segmentogeojson' => $data['rutaGeoJSON'],
            ]);
        }

        // Recargar la relación para que la respuesta incluya los segmentos creados
        $direccion->load('segmentos');

        return response()->json($direccion, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $usuarioId = $this->obtenerUsuarioId($request);
        if (!$usuarioId) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        
        // Verificar que la dirección pertenece al usuario o a un envío del usuario
        $direccion = Direccion::find($id);
        if (!$direccion) {
            return response()->json(['error' => 'Dirección no encontrada'], Response::HTTP_NOT_FOUND);
        }
        
        $perteneceAlUsuario = $direccion->id_usuario == $usuarioId || 
            Envio::where('id_usuario', $usuarioId)
                ->where('id_direccion', $id)
                ->exists();
        
        if (!$perteneceAlUsuario) {
            return response()->json(['error' => 'Dirección no encontrada o no autorizada'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'nombreOrigen' => ['nullable','string','max:200'],
            'origen_lng' => ['nullable','numeric'],
            'origen_lat' => ['nullable','numeric'],
            'nombreDestino' => ['nullable','string','max:200'],
            'destino_lng' => ['nullable','numeric'],
            'destino_lat' => ['nullable','numeric'],
            'rutaGeoJSON' => ['nullable','string'],
        ]);

        $direccion->update([
            'nombreorigen' => $data['nombreOrigen'] ?? $direccion->nombreorigen,
            'origen_lng' => array_key_exists('origen_lng', $data) ? $data['origen_lng'] : $direccion->origen_lng,
            'origen_lat' => array_key_exists('origen_lat', $data) ? $data['origen_lat'] : $direccion->origen_lat,
            'nombredestino' => $data['nombreDestino'] ?? $direccion->nombredestino,
            'destino_lng' => array_key_exists('destino_lng', $data) ? $data['destino_lng'] : $direccion->destino_lng,
            'destino_lat' => array_key_exists('destino_lat', $data) ? $data['destino_lat'] : $direccion->destino_lat,
            'rutageojson' => array_key_exists('rutaGeoJSON', $data) ? $data['rutaGeoJSON'] : $direccion->rutageojson,
        ]);

        return response()->json($direccion);
    }

    public function destroy(Request $request, int $id)
    {
        $usuarioId = $this->obtenerUsuarioId($request);
        if (!$usuarioId) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        
        // Verificar que la dirección pertenece al usuario o a un envío del usuario
        $direccion = Direccion::find($id);
        if (!$direccion) {
            return response()->json(['error' => 'Dirección no encontrada'], Response::HTTP_NOT_FOUND);
        }
        
        $perteneceAlUsuario = $direccion->id_usuario == $usuarioId || 
            Envio::where('id_usuario', $usuarioId)
                ->where('id_direccion', $id)
                ->exists();
        
        if (!$perteneceAlUsuario) {
            return response()->json(['error' => 'Dirección no encontrada o no autorizada'], Response::HTTP_NOT_FOUND);
        }

        // Validar uso en envíos activos (Pendiente, Asignado, En curso)
        $envios = Envio::where('id_direccion', $direccion->id)->get();
        $enUso = false;
        
        foreach ($envios as $envio) {
            $estadoActual = EstadoHelper::obtenerEstadoActualEnvio($envio->id);
            if (in_array($estadoActual, ['Pendiente', 'Asignado', 'En curso'])) {
                $enUso = true;
                break;
            }
        }

        if ($enUso) {
            return response()->json(['error' => 'Esta dirección está en uso por un envío activo y no puede eliminarse.'], Response::HTTP_BAD_REQUEST);
        }

        DireccionSegmento::where('direccion_id', $direccion->id)->delete();
        $direccion->delete();
        return response()->json(['message' => 'Dirección eliminada correctamente']);
    }
}


