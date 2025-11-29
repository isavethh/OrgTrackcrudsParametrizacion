<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamanoTransporte extends Model
{
    use HasFactory;

    protected $table = 'tamano_transporte';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    // Relación con Vehículos
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tamano_transporte_id');
    }
}
