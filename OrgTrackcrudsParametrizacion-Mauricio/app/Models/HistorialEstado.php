<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
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
    
    protected $attributes = [
        'fecha' => null, // Se asignará en el boot
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->fecha) {
                $model->fecha = now();
            }
        });
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio');
    }

    public function estadoEnvio()
    {
        return $this->belongsTo(EstadoEnvio::class, 'id_estado_envio');
    }
}
