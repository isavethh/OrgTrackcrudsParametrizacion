<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoEnvio extends Model
{
    protected $table = 'estados_envio';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'id_estado_envio');
    }
}
