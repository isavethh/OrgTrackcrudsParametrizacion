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
        'estado',
        'fecha_creacion',
        'fecha_inicio',
        'fecha_entrega',
        'id_direccion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_entrega' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion', 'id');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_envio', 'id');
    }
}


