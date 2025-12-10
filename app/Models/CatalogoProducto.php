<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoProducto extends Model
{
    public $timestamps = false;

    protected $table = 'catalogo_productos';

    protected $fillable = ['id_categoria', 'nombre'];

    public function categoria()
    {
        return $this->belongsTo(CatalogoCategoria::class, 'id_categoria');
    }

    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_producto');
    }
}
