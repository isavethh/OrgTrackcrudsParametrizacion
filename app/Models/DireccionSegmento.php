<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DireccionSegmento extends Model
{
    use HasFactory;

    protected $table = 'direccionsegmento';
    public $timestamps = false;

    protected $fillable = [
        'direccion_id',
        'segmentogeojson',
    ];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id', 'id');
    }
}


