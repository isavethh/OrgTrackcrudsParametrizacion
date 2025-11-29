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
        'id_envio',
        'id_transportista',
        'id_vehiculo',
        'id_recogida_entrega',
        'id_tipo_transporte',
        'id_estado_asignacion',
        'fecha_asignacion',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime'
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio');
    }

    public function transportista()
    {
        return $this->belongsTo(Transportista::class, 'id_transportista');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo');
    }

    public function recogidaEntrega()
    {
        return $this->belongsTo(RecogidaEntrega::class, 'id_recogida_entrega');
    }

    public function tipoTransporte()
    {
        return $this->belongsTo(TipoTransporte::class, 'id_tipo_transporte');
    }

    public function estadoAsignacion()
    {
        return $this->belongsTo(EstadoAsignacionMultiple::class, 'id_estado_asignacion');
    }

    public function cargas()
    {
        return $this->belongsToMany(Carga::class, 'asignacioncarga', 'id_asignacion', 'id_carga');
    }

    public function checklistCondicion()
    {
        return $this->hasOne(ChecklistCondicion::class, 'id_asignacion');
    }

    public function incidentes()
    {
        return $this->hasMany(IncidenteTransporte::class, 'id_asignacion');
    }

    public function firmaEnvio()
    {
        return $this->hasOne(FirmaEnvio::class, 'id_asignacion');
    }

    public function firmaTransportista()
    {
        return $this->hasOne(FirmaTransportista::class, 'id_asignacion');
    }

    public function qrToken()
    {
        return $this->hasOne(QrToken::class, 'id_asignacion');
    }
}
