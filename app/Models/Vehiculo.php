<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'placa',
        'capacidad',
        'estado',
        'fecha_registro'
    ];

    protected $casts = [
        'capacidad' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    // Constantes para tipos de vehículo
    public const TIPOS = [
        'Pesado - Ventilado',
        'Pesado - Aislado',
        'Pesado - Refrigerado',
        'Mediano - Ventilado',
        'Mediano - Aislado',
        'Mediano - Refrigerado',
        'Ligero - Ventilado',
        'Ligero - Aislado',
        'Ligero - Refrigerado',
    ];

    // Constantes para estados
    public const ESTADOS = [
        'Disponible',
        'En ruta',
        'No Disponible',
        'Mantenimiento',
    ];
}
