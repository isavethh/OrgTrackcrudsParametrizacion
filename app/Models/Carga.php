<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carga extends Model
{
    use HasFactory;

    protected $table = 'carga';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_catalogo_carga',
        'cantidad',
        'peso',
    ];

    protected $casts = [
        'peso' => 'decimal:2',
    ];

    public function catalogoCarga()
    {
        return $this->belongsTo(CatalogoCarga::class, 'id_catalogo_carga');
    }

    public function asignaciones()
    {
        return $this->belongsToMany(AsignacionMultiple::class, 'asignacioncarga', 'id_carga', 'id_asignacion');
    }
}
