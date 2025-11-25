<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecogidaEntrega extends Model
{
    use HasFactory;

    protected $table = 'recogidaentrega';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'fecha_recogida',
        'hora_recogida',
        'hora_entrega',
        'instrucciones_recogida',
        'instrucciones_entrega',
    ];

    protected $casts = [
        'fecha_recogida' => 'date',
    ];

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_recogida_entrega');
    }
}
