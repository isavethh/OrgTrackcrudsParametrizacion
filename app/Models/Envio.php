<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envio';
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'tipo_empaque_id',
        'unidad_medida_id',
        'estado',
        'peso'
    ];

    protected $casts = [
        'peso' => 'decimal:2'
    ];

    // Relación con Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    // Relación con Tipo de Empaque
    public function tipoEmpaque()
    {
        return $this->belongsTo(TipoEmpaque::class, 'tipo_empaque_id');
    }

    // Relación con Unidad de Medida
    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    // Relación con Direcciones
    public function direcciones()
    {
        return $this->hasMany(Direccion::class);
    }

    // Relación con Asignaciones
    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class);
    }

    // Relación con Checklists
    public function checklistsCondicionCliente()
    {
        return $this->hasMany(ChecklistCondicionCliente::class);
    }

    public function checklistsCondicionTransportista()
    {
        return $this->hasMany(ChecklistCondicionTransportista::class);
    }

    public function checklistsIncidente()
    {
        return $this->hasMany(ChecklistIncidenteTransporte::class);
    }
}
