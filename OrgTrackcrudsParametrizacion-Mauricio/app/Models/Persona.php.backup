<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'persona';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'telefono',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_persona');
    }
}
