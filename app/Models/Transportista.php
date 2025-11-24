<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    use HasFactory;

    protected $table = 'transportistas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'ci',
        'licencia',
        'telefono',
        'licencia',
        'id_estado_transportista',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    public function estadoTransportista()
    {
        return $this->belongsTo(EstadoTransportista::class, 'id_estado_transportista');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionMultiple::class, 'id_transportista');
    }
}
