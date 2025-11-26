<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstados extends Model
{
    use HasFactory;

    protected $table = 'historialestados';
    public $timestamps = false;

    protected $fillable = [
        'id_envio',
        'id_estado_envio',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio', 'id');
    }

    public function estadoEnvio()
    {
        return $this->belongsTo(EstadosEnvio::class, 'id_estado_envio', 'id');
    }
}

