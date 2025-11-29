<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTransporte extends Model
{
    use HasFactory;

    protected $table = 'tipotransporte';
    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    // Relación con Vehículos
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipo_transporte_id');
    }

    // Relación con Asignaciones
    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'tipo_transporte_id');
    }
}
