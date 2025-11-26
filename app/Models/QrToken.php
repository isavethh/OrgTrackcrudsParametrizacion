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
        'id_estado_qrtoken',
        'token',
        'imagenqr',
        'fecha_creacion',
        'fecha_expiracion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }

    public function estadoQrToken()
    {
        return $this->belongsTo(EstadosQrToken::class, 'id_estado_qrtoken', 'id');
    }
}
