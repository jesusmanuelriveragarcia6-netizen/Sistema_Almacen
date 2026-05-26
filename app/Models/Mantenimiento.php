<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';
    public $timestamps = false;
    
    protected $fillable = [
        'herramienta_id', 'usuario_id', 'fecha_inicio', 'fecha_fin', 
        'descripcion', 'costo', 'estado'
    ];
}
