<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    
    protected $fillable = [
        'nombre',
        'categoria',
        'peso_por_unidad',
        'descripcion',
    ];

    protected $casts = [
        'peso_por_unidad' => 'decimal:3',
    ];

    // Categorías disponibles (hardcodeadas)
    public static function getCategorias()
    {
        return ['Frutas', 'Verduras'];
    }
}
