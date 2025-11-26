<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoCarga extends Model
{
    use HasFactory;

    protected $table = 'catalogo_carga';
    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'variedad',
        'empaque',
        'descripcion',
    ];

    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_catalogo_carga', 'id');
    }
}

