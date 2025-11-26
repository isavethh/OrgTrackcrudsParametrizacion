<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentesTransporte extends Model
{
    use HasFactory;

    protected $table = 'incidentes_transporte';
    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'id_tipo_incidente',
        'descripcion_incidente',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }

    public function tipoIncidente()
    {
        return $this->belongsTo(TiposIncidenteTransporte::class, 'id_tipo_incidente', 'id');
    }
}

