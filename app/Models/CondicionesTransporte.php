<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CondicionesTransporte extends Model
{
    use HasFactory;

    protected $table = 'condiciones_transporte';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
    ];

    public function checklistDetalles()
    {
        return $this->hasMany(ChecklistCondicionDetalle::class, 'id_condicion', 'id');
    }
}

