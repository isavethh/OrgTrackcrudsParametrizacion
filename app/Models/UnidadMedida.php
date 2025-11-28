<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    public $timestamps = false;
    
    protected $table = 'unidades_medida';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'descripcion',
    ];
    
    // Relaciones
    public function cargas()
    {
        return $this->hasMany(Carga::class, 'id_unidad_medida');
    }
}
