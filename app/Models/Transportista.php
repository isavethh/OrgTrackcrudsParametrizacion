<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transportista extends Model
{
    use HasFactory;

    protected $table = 'transportistas';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_estado_transportista',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    /**
     * Relación con el estado del transportista
     */
    public function estadoTransportista(): BelongsTo
    {
        return $this->belongsTo(EstadosTransportista::class, 'id_estado_transportista', 'id');
    }

    /**
     * Relación con las asignaciones
     */
    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_transportista', 'id');
    }

    /**
     * Relación con las calificaciones recibidas
     */
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_transportista');
    }

    /**
     * Calcular promedio de calificaciones
     */
    public function promedioCalificacion()
    {
        return $this->calificaciones()->avg('puntuacion');
    }

    /**
     * Scope para obtener transportistas disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->whereHas('estadoTransportista', function($q) {
            $q->where('nombre', 'Disponible');
        });
    }

    /**
     * Scope para obtener transportistas por estado
     */
    public function scopePorEstado($query, $estadoNombre)
    {
        return $query->whereHas('estadoTransportista', function($q) use ($estadoNombre) {
            $q->where('nombre', $estadoNombre);
        });
    }
}
