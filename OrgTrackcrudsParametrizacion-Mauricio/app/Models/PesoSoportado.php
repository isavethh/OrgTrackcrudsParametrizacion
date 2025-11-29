<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesoSoportado extends Model
{
    use HasFactory;

    protected $table = 'peso_soportado';
    public $timestamps = false;

    protected $fillable = [
        'vehiculo_id',
        'peso_maximo'
    ];

    protected $casts = [
        'peso_maximo' => 'decimal:2'
    ];

    // Relación con Vehículo
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
