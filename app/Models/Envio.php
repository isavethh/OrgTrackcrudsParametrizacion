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
        'id_direccion',
        'fecha_creacion',
        'fecha_inicio',
        'fecha_entrega',
        'fecha_entrega_aproximada',
        'hora_entrega_aproximada',
        'peso_total_envio',
        'costo_total_envio',
        'ubicacion_actual_lng',
        'ubicacion_actual_lat',
        'estado_tracking',
        'fecha_inicio_tracking',
        'fecha_fin_tracking',
        'codigo_qr',
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
}
