<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    use HasFactory;

    protected $table = 'transportista';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'ci',
        'placa',
        'telefono',
        'estado_id',
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación con Estado
    public function estado()
    {
        return $this->belongsTo(EstadoTransportista::class, 'estado_id');
    }

    // Relación con Asignaciones
    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'transportista_id');
    }
}
