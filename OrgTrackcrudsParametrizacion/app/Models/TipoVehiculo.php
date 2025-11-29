<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = 'tipos_vehiculo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_tipo_vehiculo');
    }
}
