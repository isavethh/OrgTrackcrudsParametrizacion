<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTransportista extends Model
{
    use HasFactory;

    protected $table = 'estado_transportista';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    // Relación con Transportistas
    public function transportistas()
    {
        return $this->hasMany(Transportista::class, 'estado_id');
    }
}
