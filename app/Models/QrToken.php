<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrToken extends Model
{
    use HasFactory;

    protected $table = 'qrtoken';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'token',
        'fecha_expiracion'
    ];

    protected $casts = [
        'fecha_expiracion' => 'datetime'
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
