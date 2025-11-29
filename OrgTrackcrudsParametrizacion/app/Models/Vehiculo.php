<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_vehiculo',
        'placa',
        'capacidad',
        'id_estado_vehiculo',
    ];

    protected $casts = [
        'capacidad' => 'decimal:2',
        'fecha_registro' => 'datetime',
    ];

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'id_tipo_vehiculo');
    }

    public function estadoVehiculo()
    {
        return $this->belongsTo(EstadoVehiculo::class, 'id_estado_vehiculo');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_vehiculo');
    }
}
