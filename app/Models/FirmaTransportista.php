<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmaTransportista extends Model
{
    use HasFactory;

    protected $table = 'firmatransportista';
    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'imagenfirma',
        'fechafirma',
    ];

    protected $casts = [
        'fechafirma' => 'datetime',
    ];

    public function asignacion()
    {
        return $this->belongsTo(AsignacionMultiple::class, 'id_asignacion', 'id');
    }
}
