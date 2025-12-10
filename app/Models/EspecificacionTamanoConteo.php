<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspecificacionTamanoConteo extends Model
{
    public $timestamps = false;

    protected $table = 'especificacion_tamano_conteo';

    protected $fillable = [
        'id_carga',
        'conteo_por_empaque',
        'peso_promedio_unidad',
        'capacidad_por_empaque',
    ];

    protected $casts = [
        'conteo_por_empaque' => 'integer',
        'peso_promedio_unidad' => 'decimal:3',
        'capacidad_por_empaque' => 'integer',
    ];

    public function carga()
    {
        return $this->belongsTo(Carga::class, 'id_carga');
    }
}
