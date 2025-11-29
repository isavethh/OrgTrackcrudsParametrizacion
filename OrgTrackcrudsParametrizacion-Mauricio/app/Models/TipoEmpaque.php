<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEmpaque extends Model
{
    use HasFactory;

    protected $table = 'tipo_empaque';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function envioProductos()
    {
        return $this->hasMany(EnvioProducto::class, 'id_tipo_empaque');
    }
}
