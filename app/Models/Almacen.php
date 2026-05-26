<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Almacen extends Model
{
    use SoftDeletes;

    protected $table = 'almacenes';
    
    protected $fillable = [
        'nombre', 'ubicacion_general', 'descripcion'
    ];

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }

    public function herramientas(): HasMany
    {
        return $this->hasMany(Herramienta::class);
    }
}
