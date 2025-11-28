<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    public $timestamps = false;
    
    protected $table = 'notificaciones';
    
    protected $fillable = [
        'id_usuario',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
        'id_envio',
        'fecha',
    ];
    
    protected $casts = [
        'leida' => 'boolean',
        'fecha' => 'datetime',
    ];
    
    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
    
    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio');
    }
    
    // Scopes
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }
    
    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha', 'desc');
    }
}
