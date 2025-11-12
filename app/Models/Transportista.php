<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'fecha_registro'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    // Constantes para estados
    public const ESTADOS = [
        'Disponible',
        'En ruta',
        'No Disponible',
        'Inactivo',
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
