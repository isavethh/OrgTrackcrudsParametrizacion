<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    public $timestamps = false;
    
    protected $table = 'calificaciones';
    
    protected $fillable = [
        'id_envio',
        'id_usuario',
        'id_transportista',
        'puntuacion',
        'comentario',
        'fecha',
    ];
    
    protected $casts = [
        'fecha' => 'datetime',
        'puntuacion' => 'integer',
    ];
    
    // Relaciones
    public function envio()
    {
        return $this->belongsTo(Envio::class, 'id_envio');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
    
    public function transportista()
    {
        return $this->belongsTo(Transportista::class, 'id_transportista');
    }
}
