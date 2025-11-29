<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    use HasFactory;

    protected $table = 'historialestados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_envio',
        'id_estado_envio',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio');
    }

    public function estadoEnvio()
    {
        return $this->belongsTo(EstadoEnvio::class, 'id_estado_envio');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->fecha) {
                $model->fecha = now();
            }
        });
    }
}

