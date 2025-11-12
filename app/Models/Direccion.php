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
        'orden',
        'punto_recogida_lat',
        'punto_recogida_lng',
        'nombre_punto_recogida',
        'punto_entrega_lat',
        'punto_entrega_lng',
        'nombre_punto_entrega'
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'punto_recogida_lat' => 'decimal:8',
        'punto_recogida_lng' => 'decimal:8',
        'punto_entrega_lat' => 'decimal:8',
        'punto_entrega_lng' => 'decimal:8',
        'orden' => 'integer'
    ];

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }
}
