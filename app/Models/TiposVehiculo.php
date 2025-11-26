<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposVehiculo extends Model
{
    use HasFactory;

    protected $table = 'tipos_vehiculo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_tipo_vehiculo', 'id');
    }
}

