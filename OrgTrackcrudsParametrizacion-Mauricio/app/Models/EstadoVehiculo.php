<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoVehiculo extends Model
{
    protected $table = 'estados_vehiculo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_estado_vehiculo');
    }
}
