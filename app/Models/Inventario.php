<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    // Especificar la tabla asociada
    protected $table = 'inventarios';

    // Permitir modificación masiva de estos campos
    protected $fillable = [
        'cantidad_stock'
    ];

    // Relación con la tabla catalogos
    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class);
    }

    /**
     * Actualiza el stock restando la cantidad prestada.
     * 
     * @param int $cantidadPrestada
     * @return bool
     */
    public function restarStock(int $cantidadPrestada)
    {

        $this->cantidad_stock -= $cantidadPrestada;
        return $this->save();
    }
}
