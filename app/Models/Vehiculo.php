<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculo';
    public $timestamps = false;

    protected $fillable = [
        'transportista_id',
        'tipo_transporte_id',
        'tamano_transporte_id',
        'placa',
        'marca',
        'capacidad_carga',
        'unidad_medida_carga_id',
        'estado'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    // Relación con Transportista
    public function transportista()
    {
        return $this->belongsTo(Transportista::class);
    }

    // Relación con Tipo de Transporte
    public function tipoTransporte()
    {
        return $this->belongsTo(TipoTransporte::class, 'tipo_transporte_id');
    }

    // Relación con Tamaño de Transporte
    public function tamanoTransporte()
    {
        return $this->belongsTo(TamanoTransporte::class, 'tamano_transporte_id');
    }

    // Relación con Peso Soportado
    public function pesoSoportado()
    {
        return $this->hasOne(PesoSoportado::class);
    }

    // Relación con Unidad de Medida de Carga
    public function unidadMedidaCarga()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_carga_id');
    }
}
