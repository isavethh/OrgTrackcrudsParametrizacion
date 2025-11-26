<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiposIncidenteTransporte extends Model
{
    use HasFactory;

    protected $table = 'tipos_incidente_transporte';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
    ];

    public function incidentes()
    {
        return $this->hasMany(IncidentesTransporte::class, 'id_tipo_incidente', 'id');
    }
}

