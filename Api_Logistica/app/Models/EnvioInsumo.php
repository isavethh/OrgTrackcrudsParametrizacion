<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioInsumo extends Model
{
    use HasFactory;

    protected $table = 'envio_insumos';

    protected $fillable = [
        'envio_id',
        'nombre_insumo',
        'tipo_insumo',
        'cantidad',
        'peso_por_unidad',
        'peso_total',
        'costo_unitario',
        'costo_total',
        'tipo_empaque',
        'unidad_medida',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'peso_por_unidad' => 'decimal:2',
        'peso_total' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'envio_id');
    }
}
