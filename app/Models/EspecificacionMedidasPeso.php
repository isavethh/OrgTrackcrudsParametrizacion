<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspecificacionMedidasPeso extends Model
{
    public $timestamps = false;

    protected $table = 'especificacion_medidas_peso';

    protected $fillable = [
        'id_carga',
        'largo_cm',
        'ancho_cm',
        'alto_cm',
        'peso_neto_kg',
        'tara_kg',
        'peso_bruto_kg',
    ];

    protected $casts = [
        'largo_cm' => 'decimal:2',
        'ancho_cm' => 'decimal:2',
        'alto_cm' => 'decimal:2',
        'peso_neto_kg' => 'decimal:2',
        'tara_kg' => 'decimal:2',
        'peso_bruto_kg' => 'decimal:2',
    ];

    public function carga()
    {
        return $this->belongsTo(Carga::class, 'id_carga');
    }
}
