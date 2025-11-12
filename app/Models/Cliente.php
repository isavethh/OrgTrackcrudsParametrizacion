<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'telefono',
        'direccion_entrega'
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación con QR Tokens
    public function qrtokens()
    {
        return $this->hasMany(QrToken::class, 'cliente_id');
    }
}
