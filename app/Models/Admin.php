<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'nivel_acceso'
    ];

    protected $casts = [
        'nivel_acceso' => 'integer',
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación con Vehículos
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'admin_id');
    }

    // Relación con Envíos
    public function envios()
    {
        return $this->hasMany(Envio::class, 'admin_id');
    }
}
