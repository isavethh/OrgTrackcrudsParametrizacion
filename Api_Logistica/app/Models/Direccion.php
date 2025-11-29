<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direccion';
    protected $primaryKey = 'id';
    public $timestamps = false; // Sin timestamps como en OrgTrack

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
        'origen_lng' => 'decimal:8',
        'destino_lat' => 'decimal:8',
        'destino_lng' => 'decimal:8',
    ];

    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_direccion');
    }
}
