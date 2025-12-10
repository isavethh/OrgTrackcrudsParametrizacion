<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspecificacionFormaPedido extends Model
{
    public $timestamps = false;

    protected $table = 'especificacion_forma_pedido';

    protected $fillable = [
        'id_carga',
        'forma_pedido',
        'cantidad_pedido',
        'empaques_calculados',
        'unidades_por_pallet',
        'numero_pallets',
    ];

    protected $casts = [
        'cantidad_pedido' => 'integer',
        'empaques_calculados' => 'integer',
        'unidades_por_pallet' => 'integer',
        'numero_pallets' => 'integer',
    ];

    public function carga()
    {
        return $this->belongsTo(Carga::class, 'id_carga');
    }
}
