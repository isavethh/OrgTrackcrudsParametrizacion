<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envios';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'fecha_creacion',
        'fecha_inicio',
        'fecha_entrega',
        'id_direccion',
        'cancelado',
        'id_motivo_cancelacion',
        'fecha_cancelacion',
        'observacion_cancelacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_entrega' => 'datetime',
        'fecha_cancelacion' => 'datetime',
        'cancelado' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id');
    }

    public function motivoCancelacion()
    {
        return $this->belongsTo(MotivoCancelacion::class, 'id_motivo_cancelacion');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_envio', 'id');
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstados::class, 'id_envio', 'id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_envio');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_envio');
    }

    /**
     * Obtener el estado actual del envío
     */
    public function estadoActual()
    {
        return $this->historialEstados()
            ->orderBy('fecha', 'desc')
            ->first()
            ?->estadoEnvio;
    }
}


