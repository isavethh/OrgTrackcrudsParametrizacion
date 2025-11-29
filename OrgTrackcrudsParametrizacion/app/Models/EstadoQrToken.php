<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoQrToken extends Model
{
    use HasFactory;

    protected $table = 'estados_qrtoken';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class, 'id_estado_qrtoken');
    }
}
