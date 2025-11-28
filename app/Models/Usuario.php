<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    public $timestamps = false;

    protected $fillable = [
        'correo',
        'contrasena',
        'id_rol',
        'fecha_registro',
        'id_persona',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(RolesUsuario::class, 'id_rol', 'id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_usuario', 'id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_usuario', 'id');
    }

    public function transportista()
    {
        return $this->hasOne(Transportista::class, 'id_usuario', 'id');
    }

    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_usuario', 'id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_usuario');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'id_usuario');
    }
}


