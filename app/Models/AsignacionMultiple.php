<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionMultiple extends Model
{
    use HasFactory;

    protected $table = 'asignacionmultiple';
    public $timestamps = false;

    protected $fillable = [
        'envio_id',
        'tipo_transporte_id',
        'fecha_asignacion'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime'
    ];

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }

    // Relación con Tipo de Transporte
    public function tipoTransporte()
    {
        return $this->belongsTo(TipoTransporte::class, 'tipo_transporte_id');
    }
}
