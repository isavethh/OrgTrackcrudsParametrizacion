<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Admin;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Transportista;
use App\Models\Envio;
use App\Models\Direccion;
use App\Models\EstadoTransportista;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_usuarios' => Usuario::count(),
            'total_admins' => Admin::count(),
            'total_clientes' => Cliente::count(),
            'total_transportistas' => Transportista::count(),
            'total_vehiculos' => Vehiculo::count(),
            'total_envios' => Envio::count(),
            'total_direcciones' => Direccion::count(),
            
            // Estados de vehículos
            'vehiculos_disponibles' => Vehiculo::whereHas('estadoVehiculo', function($q) {
                $q->where('nombre', 'Disponible');
            })->count(),
            'vehiculos_en_ruta' => Vehiculo::whereHas('estadoVehiculo', function($q) {
                $q->where('nombre', 'En ruta');
            })->count(),
            'vehiculos_mantenimiento' => Vehiculo::whereHas('estadoVehiculo', function($q) {
                $q->where('nombre', 'Mantenimiento');
            })->count(),
            
            // Estados de transportistas
            'transportistas_disponibles' => Transportista::whereHas('estadoTransportista', function($q) {
                $q->where('nombre', 'Disponible');
            })->count(),
            'transportistas_en_ruta' => Transportista::whereHas('estadoTransportista', function($q) {
                $q->where('nombre', 'En ruta');
            })->count(),
            
            // Estados de envíos (basado en último estado en historialestados)
            'envios_pendientes' => Envio::whereHas('historialEstados', function($q) {
                $q->whereHas('estadoEnvio', function($sq) {
                    $sq->where('nombre', 'Pendiente');
                })->whereRaw('fecha = (SELECT MAX(fecha) FROM historialestados WHERE id_envio = envios.id)');
            })->count(),
            'envios_en_curso' => Envio::whereHas('historialEstados', function($q) {
                $q->whereHas('estadoEnvio', function($sq) {
                    $sq->where('nombre', 'En curso');
                })->whereRaw('fecha = (SELECT MAX(fecha) FROM historialestados WHERE id_envio = envios.id)');
            })->count(),
            'envios_entregados' => Envio::whereHas('historialEstados', function($q) {
                $q->whereHas('estadoEnvio', function($sq) {
                    $sq->where('nombre', 'Entregado');
                })->whereRaw('fecha = (SELECT MAX(fecha) FROM historialestados WHERE id_envio = envios.id)');
            })->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
