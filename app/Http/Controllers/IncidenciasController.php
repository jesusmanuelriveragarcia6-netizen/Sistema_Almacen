<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Herramienta;
use App\Models\Trabajador;
use App\Models\Vale;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncidenciasController extends Controller
{
    public function index(Request $request)
    {
        $herramientas = Herramienta::all();
        $trabajadores = Trabajador::where('estado', 'Activo')->get();
        $incidencias = Incidencia::with(['herramienta', 'usuario', 'trabajador', 'vale'])->orderBy('id', 'desc')->get();

        return view('incidencias.index', compact('herramientas', 'trabajadores', 'incidencias'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403, 'No tienes permisos para registrar incidencias.');
        }

        $request->validate([
            'herramienta_id' => 'required|exists:herramientas,id',
            'estado_incidencia' => 'required|string|in:Dañado,Mantenimiento,Revision,Perdido,Falla técnica,Otro',
            'cantidad_afectada' => 'required|integer|min:1',
            'descripcion_incidencia' => 'nullable|string',
            'trabajador_id' => 'nullable|exists:trabajadores,id',
            'vale_id' => 'nullable|exists:vales,id',
            'monto_sancion' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $herramienta = Herramienta::lockForUpdate()->findOrFail($request->herramienta_id);
            $nuevo_estado = $request->estado_incidencia;
            $cant_afectada = $request->cantidad_afectada;

            $incidencia = Incidencia::create([
                'herramienta_id' => $herramienta->id,
                'usuario_id' => Auth::id(),
                'trabajador_id' => $request->trabajador_id ?: null,
                'vale_id' => $request->vale_id ?: null,
                'fecha' => now(),
                'cantidad_afectada' => $cant_afectada,
                'tipo' => $nuevo_estado,
                'descripcion' => $request->descripcion_incidencia,
                'monto_sancion' => $request->monto_sancion ?: 0,
                'estado_sancion' => $request->monto_sancion > 0 ? 'Pendiente' : null,
                'reparado' => false
            ]);

            $estado_herramienta = in_array($nuevo_estado, ['Dañado', 'Mantenimiento', 'Perdido', 'Falla técnica']) ? $nuevo_estado : $herramienta->estado;
            $herramienta->estado = $estado_herramienta;

            if ($cant_afectada > 0 && $herramienta->stock_disponible > 0 && in_array($nuevo_estado, ['Dañado', 'Perdido', 'Mantenimiento', 'Falla técnica'])) {
                $reducir = min($cant_afectada, $herramienta->stock_disponible);
                $herramienta->decrement('stock_disponible', $reducir);
            } else {
                $herramienta->save();
            }

            Log::create([
                'usuario_id' => Auth::id(),
                'accion' => 'INCIDENCIA',
                'tabla' => 'incidencias',
                'item_id' => $incidencia->id,
                'descripcion' => "Registrada incidencia para: " . $herramienta->nombre,
                'fecha' => now()
            ]);

            DB::commit();

            return redirect()->route('incidencias.index')->with('exito', '1')->with('resumen', [
                'herramienta' => $herramienta->nombre,
                'estado' => $nuevo_estado,
                'cantidad' => $cant_afectada,
                'usuario' => Auth::user()->nombre,
                'fecha' => now()->format('d/m/Y H:i'),
                'descripcion' => $request->descripcion_incidencia
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al registrar la incidencia.']);
        }
    }
}
