<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistCondicionTransportista extends Model
{
    use HasFactory;

    protected $table = 'checklistcondiciontransportista';
    public $timestamps = false;

    protected $fillable = [
        'envio_id',
        'condicion',
        'observaciones'
    ];

    // Relación con Envío
    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }
}
