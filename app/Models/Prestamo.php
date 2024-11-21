<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'receptor_id', 
        'emisor_id',
        'herramienta_id', 
        'cantidad', 
        'fecha_inicio',
        'fecha_limite', 
        'comentarios',
        'status',
        'created_at',
        'updated_at'  
         // Agregar aquí el campo
    ];

    public function herramienta()
    {
        return $this->belongsTo(Inventario::class, 'herramienta_id');
    }

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}
