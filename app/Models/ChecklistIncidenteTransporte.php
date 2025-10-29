<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistIncidenteTransporte extends Model
{
    use HasFactory;

    protected $table = 'checklistincidentestransporte';
    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'retraso',
        'problema_mecanico',
        'accidente',
        'perdida_carga',
        'condiciones_climaticas_adversas',
        'ruta_alternativa_usada',
        'contacto_cliente_dificultoso',
        'parada_imprevista',
        'problemas_documentacion',
        'otros_incidentes',
        'descripcion_incidente',
    ];

    protected $casts = [
        'retraso' => 'boolean',
        'problema_mecanico' => 'boolean',
        'accidente' => 'boolean',
        'perdida_carga' => 'boolean',
        'condiciones_climaticas_adversas' => 'boolean',
        'ruta_alternativa_usada' => 'boolean',
        'contacto_cliente_dificultoso' => 'boolean',
        'parada_imprevista' => 'boolean',
        'problemas_documentacion' => 'boolean',
        'otros_incidentes' => 'boolean',
        'fecha' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }
}
