<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipotransporte extends Model
{
    use HasFactory;

    protected $table = 'tipotransporte';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_tipo_transporte', 'id');
    }
}


