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
        'id_tipo_vehiculo',
        'id_tipo_transporte',
        'placa',
        'capacidad',
        'id_estado_vehiculo',
        'fecha_registro',
    ];

    protected $casts = [
        'capacidad' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    public function tipoVehiculo()
    {
        return $this->belongsTo(TiposVehiculo::class, 'id_tipo_vehiculo', 'id');
    }

    public function estadoVehiculo()
    {
        return $this->belongsTo(EstadosVehiculo::class, 'id_estado_vehiculo', 'id');
    }

    public function tipoTransporte()
    {
        return $this->belongsTo(Tipotransporte::class, 'id_tipo_transporte', 'id');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_vehiculo', 'id');
    }
}


