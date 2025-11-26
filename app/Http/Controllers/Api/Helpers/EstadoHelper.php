<?php

namespace App\Http\Controllers\Api\Helpers;

use App\Models\EstadosAsignacionMultiple;
use App\Models\EstadosEnvio;
use App\Models\EstadosVehiculo;
use App\Models\EstadosTransportista;
use App\Models\HistorialEstados;
use App\Models\Envio;
use App\Models\AsignacionMultiple;

class EstadoHelper
{
    /**
     * Obtener o crear estado por nombre
     */
    public static function obtenerEstadoAsignacionPorNombre(string $nombre): int
    {
        $estado = EstadosAsignacionMultiple::firstOrCreate(['nombre' => $nombre]);
        return $estado->id;
    }

    public static function obtenerEstadoEnvioPorNombre(string $nombre): int
    {
        $estado = EstadosEnvio::firstOrCreate(['nombre' => $nombre]);
        return $estado->id;
    }

    public static function obtenerEstadoVehiculoPorNombre(string $nombre): int
    {
        $estado = EstadosVehiculo::firstOrCreate(['nombre' => $nombre]);
        return $estado->id;
    }

    public static function obtenerEstadoTransportistaPorNombre(string $nombre): int
    {
        $estado = EstadosTransportista::firstOrCreate(['nombre' => $nombre]);
        return $estado->id;
    }

    public static function obtenerEstadoQrTokenPorNombre(string $nombre): int
    {
        $estado = \App\Models\EstadosQrToken::firstOrCreate(['nombre' => $nombre]);
        return $estado->id;
    }

    /**
     * Actualizar estado de envío creando registro en historial
     */
    public static function actualizarEstadoEnvio(int $id_envio, string $nombreEstado): void
    {
        $id_estado = self::obtenerEstadoEnvioPorNombre($nombreEstado);
        HistorialEstados::create([
            'id_envio' => $id_envio,
            'id_estado_envio' => $id_estado,
            'fecha' => now(),
        ]);
    }

    /**
     * Obtener estado actual de un envío
     */
    public static function obtenerEstadoActualEnvio(int $id_envio): ?string
    {
        $historial = HistorialEstados::where('id_envio', $id_envio)
            ->orderBy('fecha', 'desc')
            ->first();
        
        return $historial?->estadoEnvio?->nombre;
    }

    /**
     * Actualizar estado global del envío basado en asignaciones
     */
    public static function actualizarEstadoGlobalEnvio(int $id_envio): void
    {
        $asignaciones = AsignacionMultiple::where('id_envio', $id_envio)->get();
        
        if ($asignaciones->isEmpty()) {
            self::actualizarEstadoEnvio($id_envio, 'Pendiente');
            return;
        }

        $estados = $asignaciones->map(function ($asig) {
            return $asig->estadoAsignacion?->nombre;
        })->filter()->toArray();

        $nuevoEstado = 'Asignado';

        if (count(array_filter($estados, fn($e) => $e === 'Entregado')) === count($estados)) {
            $nuevoEstado = 'Entregado';
        } elseif (count(array_filter($estados, fn($e) => $e === 'Pendiente')) === count($estados)) {
            $nuevoEstado = 'Asignado';
        } elseif (in_array('Entregado', $estados) && count(array_filter($estados, fn($e) => $e !== 'Entregado')) > 0) {
            $nuevoEstado = 'Parcialmente entregado';
        } elseif (in_array('En curso', $estados)) {
            $nuevoEstado = 'En curso';
        }

        self::actualizarEstadoEnvio($id_envio, $nuevoEstado);
    }
}

