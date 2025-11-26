<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosAsignacionMultiple extends Model
{
    use HasFactory;

    protected $table = 'estados_asignacion_multiple';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_estado_asignacion', 'id');
    }
}

