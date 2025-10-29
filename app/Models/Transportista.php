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
        'ci',
        'telefono',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación con el modelo Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    /**
     * Scope para obtener transportistas disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'Disponible');
    }

    /**
     * Scope para obtener transportistas por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }
}
