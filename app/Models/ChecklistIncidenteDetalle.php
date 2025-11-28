<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistIncidenteDetalle extends Model
{
    public $timestamps = false;
    
    protected $table = 'checklist_incidente_detalle';
    
    protected $fillable = [
        'id_checklist',
        'id_tipo_incidente',
        'ocurrio',
        'descripcion',
    ];
    
    protected $casts = [
        'ocurrio' => 'boolean',
    ];
    
    // Relaciones
    public function checklist()
    {
        return $this->belongsTo(ChecklistIncidente::class, 'id_checklist');
    }
    
    public function tipoIncidente()
    {
        return $this->belongsTo(TiposIncidenteTransporte::class, 'id_tipo_incidente');
    }
}
