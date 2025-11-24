<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTransportista extends Model
{
    use HasFactory;

    protected $table = 'estados_transportista';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function transportistas()
    {
        return $this->hasMany(Transportista::class, 'id_estado_transportista');
    }
}
