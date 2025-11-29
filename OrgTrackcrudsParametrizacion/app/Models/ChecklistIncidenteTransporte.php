<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistIncidenteTransporte extends Model
{
    use HasFactory;

    protected $table = 'checklistincidentetransporte';
    public $timestamps = false;

    protected $fillable = [
        'envio_id',
        'incidente',
        'descripcion'
    ];

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }
}
