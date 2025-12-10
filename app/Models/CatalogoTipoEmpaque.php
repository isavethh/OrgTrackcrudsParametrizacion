<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTipoEmpaque extends Model
{
    public $timestamps = false;

    protected $table = 'catalogo_tipos_empaque';

    protected $fillable = [
        'nombre',
        'largo',
        'ancho',
        'alto',
        'tara',
        'capacidad',
        'unidades_por_pallet'
    ];

    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_tipo_empaque');
    }
}
