<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'fecha_creacion',
        'fecha_inicio',
        'fecha_entrega',
        'fecha_entrega_aproximada',
        'hora_entrega_aproximada',
        'id_direccion',
        'id_tipo_vehiculo',
        'id_transportista_asignado',
        'peso_total_envio',
        'costo_total_envio',
        'codigo_qr',
        'estado_tracking',
        'estado_aprobacion',
        'motivo_rechazo',
        'fecha_inicio_tracking',
        'fecha_fin_tracking',
        'ubicacion_actual_lat',
        'ubicacion_actual_lng',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_entrega_aproximada' => 'date',
        'fecha_inicio_tracking' => 'datetime',
        'fecha_fin_tracking' => 'datetime',
        'peso_total_envio' => 'decimal:2',
        'costo_total_envio' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_envio');
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'id_envio')->orderBy('fecha', 'desc');
    }

    public function productos()
    {
        return $this->hasMany(EnvioProducto::class, 'id_envio');
    }

    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'id_tipo_vehiculo');
    }

    public function transportistaAsignado()
    {
        return $this->belongsTo(Usuario::class, 'id_transportista_asignado');
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->fecha_creacion) {
                $model->fecha_creacion = now();
            }
        });
    }
}
