<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistCondicionTransporte extends Model
{
    use HasFactory;

    protected $table = 'checklistcondicionestransporte';
    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'temperatura_controlada',
        'embalaje_adecuado',
        'carga_segura',
        'vehiculo_limpio',
        'documentos_presentes',
        'ruta_conocida',
        'combustible_completo',
        'gps_operativo',
        'comunicacion_funcional',
        'estado_general_aceptable',
        'observaciones',
    ];

    protected $casts = [
        'temperatura_controlada' => 'boolean',
        'embalaje_adecuado' => 'boolean',
        'carga_segura' => 'boolean',
        'vehiculo_limpio' => 'boolean',
        'documentos_presentes' => 'boolean',
        'ruta_conocida' => 'boolean',
        'combustible_completo' => 'boolean',
        'gps_operativo' => 'boolean',
        'comunicacion_funcional' => 'boolean',
        'estado_general_aceptable' => 'boolean',
        'fecha' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }
}
