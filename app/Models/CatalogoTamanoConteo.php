<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTamanoConteo extends Model
{
    protected $table = 'catalogo_tamano_conteo';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'conteo_por_empaque',
        'peso_promedio_unidad',
        'id_producto'
    ];

    public function producto()
    {
        return $this->belongsTo(CatalogoProducto::class, 'id_producto');
    }
}
