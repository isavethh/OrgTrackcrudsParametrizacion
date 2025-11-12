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
        'ruta_geojson'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime'
    ];

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }
}
