<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistCondicion extends Model
{
    use HasFactory;

    protected $table = 'checklist_condicion';
    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(ChecklistCondicionDetalle::class, 'id_checklist', 'id');
    }
}

