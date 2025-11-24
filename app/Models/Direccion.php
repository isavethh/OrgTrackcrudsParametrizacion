<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direccion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombreorigen',
        'origen_lng',
        'origen_lat',
        'nombredestino',
        'destino_lng',
        'destino_lat',
        'rutageojson',
    ];

    protected $casts = [
        'origen_lat' => 'decimal:8',
        'origen_lng' => 'decimal:11',
        'destino_lat' => 'decimal:8',
        'destino_lng' => 'decimal:11',
    ];

    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_direccion');
    }

    public function segmentos()
    {
        return $this->hasMany(DireccionSegmento::class, 'direccion_id');
    }
}
