<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoIncidenteTransporte extends Model
{
    use HasFactory;

    protected $table = 'tipos_incidente_transporte';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
    ];

    public function incidentes()
    {
        return $this->hasMany(IncidenteTransporte::class, 'id_tipo_incidente');
    }
}
