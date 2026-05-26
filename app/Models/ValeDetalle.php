<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValeDetalle extends Model
{
    protected $table = 'vale_detalles';
    public $timestamps = false;
    
    protected $fillable = [
        'vale_id', 'herramienta_id', 'cantidad_prestada', 'cantidad_devuelta'
    ];

    public function herramienta() {
        return $this->belongsTo(Herramienta::class)->withTrashed();
    }

    public function vale() {
        return $this->belongsTo(Vale::class);
    }
}
