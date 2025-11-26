<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'telefono',
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_persona', 'id');
    }
}

