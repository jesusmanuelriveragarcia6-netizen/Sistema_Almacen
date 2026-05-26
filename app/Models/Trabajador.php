<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'trabajadores';
    public $timestamps = false;
    
    protected $fillable = [
        'dni', 'nombre', 'apellidos', 'telefono', 
        'cargo', 'estado', 'creado_en'
    ];

    public function vales()
    {
        return $this->hasMany(Vale::class);
    }
}
