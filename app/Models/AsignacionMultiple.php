<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsignacionMultiple extends Model
{
    protected $table = 'asignacionmultiple';
    public $timestamps = false;

    protected $fillable = [
        'id_envio',
        'id_transportista',
        'id_vehiculo',
        'id_recogida_entrega',
        'id_tipo_transporte',
        'id_estado_asignacion',
        'codigo_acceso',
        'fecha_asignacion',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio', 'id');
    }

    public function transportista()
    {
        return $this->belongsTo(Transportista::class, 'id_transportista', 'id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id');
    }

    public function recogidaEntrega()
    {
        return $this->belongsTo(RecogidaEntrega::class, 'id_recogida_entrega', 'id');
    }

    public function tipoTransporte()
    {
        return $this->belongsTo(Tipotransporte::class, 'id_tipo_transporte', 'id');
    }

    public function cargas()
    {
        return $this->belongsToMany(
            Carga::class,
            'asignacioncarga',
            'id_asignacion',
            'id_carga'
        );
    }

    public function estadoAsignacion()
    {
        return $this->belongsTo(EstadosAsignacionMultiple::class, 'id_estado_asignacion', 'id');
    }

    public function checklistCondicion()
    {
        return $this->hasOne(ChecklistCondicion::class, 'id_asignacion', 'id');
    }

    public function checklistIncidente()
    {
        return $this->hasOne(ChecklistIncidente::class, 'id_asignacion');
    }

    public function incidentes()
    {
        return $this->hasMany(IncidentesTransporte::class, 'id_asignacion', 'id');
    }

    public function firmaEnvio()
    {
        return $this->hasOne(FirmaEnvio::class, 'id_asignacion', 'id');
    }

    public function firmaTransportista()
    {
        return $this->hasOne(FirmaTransportista::class, 'id_asignacion', 'id');
    }

    public function qrToken()
    {
        return $this->hasOne(QrToken::class, 'id_asignacion', 'id');
    }
}
