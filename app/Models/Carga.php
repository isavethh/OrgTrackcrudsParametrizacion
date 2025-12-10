<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
    use HasFactory;

    protected $table = 'carga';
    public $timestamps = false;

    protected $fillable = [
        'cantidad',
        'peso',
        // 'id_unidad_medida', // Deprecated
        // Referencias a catálogos
        'id_categoria',
        'id_producto',
        'id_tipo_empaque',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'peso' => 'decimal:2',
    ];

    /*
    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'id_unidad_medida');
    }
    */

    public function asignaciones()
    {
        return $this->belongsToMany(
            AsignacionMultiple::class,
            'asignacioncarga',
            'id_carga',
            'id_asignacion'
        );
    }

    public function categoria()
    {
        return $this->belongsTo(CatalogoCategoria::class, 'id_categoria');
    }

    public function producto()
    {
        return $this->belongsTo(CatalogoProducto::class, 'id_producto');
    }

    public function tipoEmpaque()
    {
        return $this->belongsTo(CatalogoTipoEmpaque::class, 'id_tipo_empaque');
    }

    // Relaciones a tablas de especificaciones
    public function especificacionTamanoConteo()
    {
        return $this->hasOne(EspecificacionTamanoConteo::class, 'id_carga');
    }

    public function especificacionMedidasPeso()
    {
        return $this->hasOne(EspecificacionMedidasPeso::class, 'id_carga');
    }

    public function especificacionFormaPedido()
    {
        return $this->hasOne(EspecificacionFormaPedido::class, 'id_carga');
    }
}


