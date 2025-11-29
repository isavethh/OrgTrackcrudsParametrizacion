<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoEnvio extends Model
{
    use HasFactory;

    protected $table = 'estados_envio';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
    ];

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'id_estado_envio');
    }
}

