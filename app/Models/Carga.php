<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
    use HasFactory;

    protected $table = 'carga';
    public $timestamps = false;

    protected $fillable = [
        'id_catalogo_carga',
        'cantidad',
        'peso',
        'id_unidad_medida',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'peso' => 'decimal:2',
    ];

    public function catalogoCarga()
    {
        return $this->belongsTo(CatalogoCarga::class, 'id_catalogo_carga', 'id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }

    public function asignaciones()
    {
        return $this->belongsToMany(
            AsignacionMultiple::class,
            'asignacioncarga',
            'id_carga',
            'id_asignacion'
        );
    }
}


