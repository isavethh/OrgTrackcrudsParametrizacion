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
        'admin_id',
        'tipo_transporte_id',
        'tamano_transporte_id',
        'placa',
        'marca',
        'modelo',
        'estado'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    // Relación con Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
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
}
