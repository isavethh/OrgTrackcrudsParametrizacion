<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivoCancelacion extends Model
{
    public $timestamps = false;
    
    protected $table = 'motivos_cancelacion';
    
    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_motivo_cancelacion');
    }
}
