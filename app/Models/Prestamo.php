<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table = 'prestamos';
    public $timestamps = false;
    
    protected $fillable = [
        'herramienta_id', 'trabajador_id', 'usuario_id', 
        'fecha_prestamo', 'fecha_devolucion_esperada', 
        'fecha_devolucion_real', 'estado'
    ];
}
