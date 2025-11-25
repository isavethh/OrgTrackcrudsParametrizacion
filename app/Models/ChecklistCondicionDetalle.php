<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistCondicionDetalle extends Model
{
    use HasFactory;

    protected $table = 'checklist_condicion_detalle';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_checklist',
        'id_condicion',
        'valor',
        'comentario',
    ];

    protected $casts = [
        'valor' => 'boolean',
    ];

    public function checklist()
    {
        return $this->belongsTo(ChecklistCondicion::class, 'id_checklist');
    }

    public function condicion()
    {
        return $this->belongsTo(CondicionTransporte::class, 'id_condicion');
    }
}
