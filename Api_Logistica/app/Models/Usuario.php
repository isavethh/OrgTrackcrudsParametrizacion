<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'correo',
        'contrasena',
        'id_rol',
        'nombre',
        'apellido',
        'ci',
        'telefono',
        'fecha_registro',
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_usuario');
    }
}

