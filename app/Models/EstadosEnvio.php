<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosEnvio extends Model
{
    use HasFactory;

    protected $table = 'estados_envio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstados::class, 'id_estado_envio', 'id');
    }
}

