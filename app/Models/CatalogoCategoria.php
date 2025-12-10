<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoCategoria extends Model
{
    public $timestamps = false;

    protected $table = 'catalogo_categorias';

    protected $fillable = ['nombre'];

    public function productos()
    {
        return $this->hasMany(CatalogoProducto::class, 'id_categoria');
    }

    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_categoria');
    }
}
