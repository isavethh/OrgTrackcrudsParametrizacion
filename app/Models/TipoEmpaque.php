<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEmpaque extends Model
{
    use HasFactory;

    protected $table = 'tipo_empaque';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    // Relación con Envíos
    public function envios()
    {
        return $this->hasMany(Envio::class, 'tipo_empaque_id');
    }
}
