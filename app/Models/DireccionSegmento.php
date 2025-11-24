<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DireccionSegmento extends Model
{
    protected $table = 'direccionsegmento';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'direccion_id',
        'segmentogeojson',
    ];

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }
}
