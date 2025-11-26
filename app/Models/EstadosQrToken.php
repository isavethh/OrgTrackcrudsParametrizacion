<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosQrToken extends Model
{
    use HasFactory;

    protected $table = 'estados_qrtoken';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class, 'id_estado_qrtoken', 'id');
    }
}

