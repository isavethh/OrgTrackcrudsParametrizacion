<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;

    protected $table = 'qrtoken';
    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'id_usuario_cliente',
        'token',
        'imagenqr',
        'usado',
        'fecha_creacion',
        'fecha_expiracion',
    ];

    protected $casts = [
        'usado' => 'boolean',
        'fecha_creacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }

    public function usuarioCliente()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_cliente', 'id');
    }
}
