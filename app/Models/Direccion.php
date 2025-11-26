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
        'id_usuario',
        'nombreorigen',
        'origen_lng',
        'origen_lat',
        'nombredestino',
        'destino_lng',
        'destino_lat',
        'rutageojson',
    ];

    protected $casts = [
        'origen_lng' => 'double',
        'origen_lat' => 'double',
        'destino_lng' => 'double',
        'destino_lat' => 'double',
    ];

    public function segmentos()
    {
        return $this->hasMany(DireccionSegmento::class, 'direccion_id', 'id');
    }

    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_direccion', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }
}


