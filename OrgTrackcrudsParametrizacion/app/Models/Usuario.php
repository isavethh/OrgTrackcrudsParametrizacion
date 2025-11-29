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
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(RolUsuario::class, 'id_rol');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_usuario');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_usuario');
    }

    public function transportista()
    {
        return $this->hasOne(Transportista::class, 'id_usuario');
    }

    public function envios()
    {
        return $this->hasMany(Envio::class, 'id_usuario');
    }
}
