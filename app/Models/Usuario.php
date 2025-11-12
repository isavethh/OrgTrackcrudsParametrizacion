<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'contrasena',
        'fecha_registro'
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    // Relación con Admin
    public function admin()
    {
        return $this->hasOne(Admin::class, 'usuario_id');
    }

    // Relación con Cliente
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    // Relación con Transportista
    public function transportista()
    {
        return $this->hasOne(Transportista::class, 'usuario_id');
    }

    // Método helper para determinar el rol
    public function getRolAttribute()
    {
        if ($this->admin) return 'admin';
        if ($this->cliente) return 'cliente';
        if ($this->transportista) return 'transportista';
        return null;
    }
}
