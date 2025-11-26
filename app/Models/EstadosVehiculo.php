<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosVehiculo extends Model
{
    use HasFactory;

    protected $table = 'estados_vehiculo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_estado_vehiculo', 'id');
    }
}

