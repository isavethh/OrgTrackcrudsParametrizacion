<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAsignacionMultiple extends Model
{
    use HasFactory;

    protected $table = 'estados_asignacion_multiple';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_estado_asignacion');
    }
}
