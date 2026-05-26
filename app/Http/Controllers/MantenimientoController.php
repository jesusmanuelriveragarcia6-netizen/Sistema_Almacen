<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MantenimientoController extends Controller
{
    private $estados_taller = ['Mantenimiento', 'Dañado', 'Revision', 'Perdido', 'Falla técnica'];

    public function index()
    {
        $herramientas = Herramienta::whereIn('estado', $this->estados_taller)->get();
        return view('mantenimiento.index', compact('herramientas'));
    }

    public function reparar(Request $request, $id)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $herramienta = Herramienta::findOrFail($id);
        
        if (!in_array($herramienta->estado, $this->estados_taller)) {
            return back()->withErrors(['error' => 'La herramienta no se encuentra en el taller.']);
        }

        $herramienta->estado = 'Disponible';
        $herramienta->save();

        Log::create([
            'usuario_id' => Auth::id(),
            'accion' => 'MANTENIMIENTO',
            'tabla' => 'herramientas',
            'item_id' => $herramienta->id,
            'descripcion' => "Herramienta marcada como Reparada/Disponible: " . $herramienta->nombre,
            'fecha' => now()
        ]);

        return redirect()->route('mantenimiento.index')->with('exito', '1');
    }
}
