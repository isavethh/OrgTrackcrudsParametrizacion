<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistIncidente extends Model
{
    public $timestamps = false;
    
    protected $table = 'checklist_incidente';
    
    protected $fillable = [
        'id_asignacion',
        'fecha',
        'observaciones',
    ];
    
    protected $casts = [
        'fecha' => 'datetime',
    ];
    
    // Relaciones
    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion');
    }
    
    public function detalles()
    {
        return $this->hasMany(ChecklistIncidenteDetalle::class, 'id_checklist');
    }
}
