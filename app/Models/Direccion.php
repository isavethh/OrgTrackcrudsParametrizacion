<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direccion';
    public $timestamps = false;

    protected $fillable = [
        'envio_id',
        'nombre_ruta',
        'descripcion',
        'latitud',
        'longitud',
        'orden'
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'orden' => 'integer'
    ];

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }
}
