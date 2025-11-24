<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioProducto extends Model
{
    protected $table = 'envio_productos';

    protected $fillable = [
        'id_envio',
        'categoria',
        'producto',
        'cantidad',
        'peso_por_unidad',
        'peso_total',
        'costo_unitario',
        'costo_total',
        'id_tipo_empaque',
        'id_unidad_medida',
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
        return $this->belongsTo(Envio::class, 'id_envio');
    }

    public function tipoEmpaque()
    {
        return $this->belongsTo(TipoEmpaque::class, 'id_tipo_empaque');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }
}
