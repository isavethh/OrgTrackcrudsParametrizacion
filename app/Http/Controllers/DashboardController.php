<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Vehiculo;
use App\Models\Transportista;
use App\Models\Carga;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_usuarios' => Usuario::count(),
            'total_vehiculos' => Vehiculo::count(),
            'total_transportistas' => Transportista::count(),
            'total_cargas' => Carga::count(),
            'vehiculos_disponibles' => Vehiculo::where('estado', 'Disponible')->count(),
            'vehiculos_en_ruta' => Vehiculo::where('estado', 'En ruta')->count(),
            'transportistas_disponibles' => Transportista::where('estado', 'Disponible')->count(),
            'transportistas_en_ruta' => Transportista::where('estado', 'En ruta')->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
