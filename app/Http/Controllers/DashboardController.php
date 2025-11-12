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
            'vehiculos_disponibles' => Vehiculo::where('estado', 'Disponible')->count(),
            'vehiculos_en_ruta' => Vehiculo::where('estado', 'En ruta')->count(),
            'vehiculos_mantenimiento' => Vehiculo::where('estado', 'Mantenimiento')->count(),
            
            // Estados de transportistas (usando la tabla estado_transportista)
            'transportistas_disponibles' => Transportista::whereHas('estado', function($q) {
                $q->where('nombre', 'Disponible');
            })->count(),
            'transportistas_en_ruta' => Transportista::whereHas('estado', function($q) {
                $q->where('nombre', 'En ruta');
            })->count(),
            
            // Estados de envíos
            'envios_pendientes' => Envio::where('estado', 'Pendiente')->count(),
            'envios_en_curso' => Envio::where('estado', 'En curso')->count(),
            'envios_entregados' => Envio::where('estado', 'Entregado')->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
