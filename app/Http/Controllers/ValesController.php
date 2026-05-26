<?php

namespace App\Http\Controllers;

use App\Models\Vale;
use App\Models\ValeDetalle;
use App\Models\Trabajador;
use App\Models\Herramienta;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValesController extends Controller
{
    public function index()
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $vales = Vale::with('trabajador')->whereIn('estado', ['Activo', 'Parcial'])->orderBy('id', 'desc')->get();
        return view('vales.index', compact('vales'));
    }

    public function historial()
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $vales = Vale::with(['trabajador' => function($q) {
            $q->withTrashed();
        }, 'usuario'])->orderBy('id', 'desc')->get();
        return view('vales.historial', compact('vales'));
    }

    public function create()
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $trabajadores = Trabajador::where('estado', 'Activo')->get();
        $herramientas = Herramienta::where('stock_disponible', '>', 0)->get();

        return view('vales.create', compact('trabajadores', 'herramientas'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'cantidades' => 'required|array',
        ]);

        $cantidades = array_filter($request->cantidades, function ($cant) {
            return (int)$cant > 0;
        });

        if (empty($cantidades)) {
            return back()->withErrors(['error' => 'Debe agregar al menos una herramienta con cantidad mayor a 0.']);
        }

        DB::beginTransaction();
        try {
            // Generar código V-0001
            $lastVale = Vale::orderBy('id', 'desc')->first();
            $numero = $lastVale ? (int)str_replace('V-', '', $lastVale->codigo_vale) : 0;
            $codigo = 'V-' . str_pad($numero + 1, 4, '0', STR_PAD_LEFT);

            $vale = Vale::create([
                'codigo_vale' => $codigo,
                'trabajador_id' => $request->trabajador_id,
                'usuario_id' => Auth::id(),
                'fecha_creacion' => now(),
                'fecha_limite' => now()->addHours(24),
                'estado' => 'Activo'
            ]);

            foreach ($cantidades as $herramienta_id => $cantidad) {
                $cantidad = (int)$cantidad;
                $herramienta = Herramienta::lockForUpdate()->find($herramienta_id);

                if (!$herramienta || $herramienta->stock_disponible < $cantidad) {
                    throw new \Exception("Stock insuficiente para: " . ($herramienta ? $herramienta->nombre : "ID $herramienta_id"));
                }

                ValeDetalle::create([
                    'vale_id' => $vale->id,
                    'herramienta_id' => $herramienta_id,
                    'cantidad_prestada' => $cantidad,
                    'cantidad_devuelta' => 0
                ]);

                $herramienta->decrement('stock_disponible', $cantidad);
            }

            Log::create([
                'usuario_id' => Auth::id(),
                'accion' => 'CREAR',
                'tabla' => 'vales',
                'item_id' => $vale->id,
                'descripcion' => "Generó vale {$codigo} para el trabajador ID {$request->trabajador_id}",
                'fecha' => now()
            ]);

            DB::commit();

            return redirect()->route('vales.show', $vale->id)->with('exito', '1');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Vale $vale)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $vale->load(['detalles.herramienta', 'trabajador', 'usuario']);
        return view('vales.show', compact('vale'));
    }

    public function devolver()
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        return view('vales.devolver');
    }

    public function buscarVale(Request $request)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $request->validate(['codigo_vale' => 'required|string']);

        $vale = Vale::where('codigo_vale', $request->codigo_vale)->first();

        if ($vale) {
            return redirect()->route('vales.procesar_devolucion', $vale->id);
        }

        return back()->withErrors(['error' => 'Vale no encontrado.']);
    }

    public function procesar_devolucion(Vale $vale)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        if ($vale->estado === 'Cerrado') {
            return redirect()->route('vales.devolver')->withErrors(['error' => 'El vale ya está cerrado.']);
        }

        $vale->load('detalles.herramienta');

        return view('vales.procesar', compact('vale'));
    }

    public function guardar_devolucion(Request $request, Vale $vale)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $cantidades = $request->devolver_cant ?? [];

        DB::beginTransaction();
        try {
            $todasDevueltas = true;
            $algunoDevuelto = false;

            foreach ($vale->detalles as $detalle) {
                $pendiente = $detalle->cantidad_prestada - $detalle->cantidad_devuelta;
                $cant_a_devolver = (int)($cantidades[$detalle->id] ?? 0);

                if ($cant_a_devolver > 0 && $cant_a_devolver <= $pendiente) {
                    $detalle->cantidad_devuelta += $cant_a_devolver;
                    
                    $detalle->save();

                    $herramienta = Herramienta::lockForUpdate()->find($detalle->herramienta_id);
                    if ($herramienta) {
                        $herramienta->increment('stock_disponible', $cant_a_devolver);
                        $algunoDevuelto = true;
                    }
                }

                if ($detalle->cantidad_devuelta < $detalle->cantidad_prestada) {
                    $todasDevueltas = false;
                }
            }

            if ($algunoDevuelto) {
                $vale->estado = $todasDevueltas ? 'Cerrado' : 'Parcial';
                $vale->save();

                Log::create([
                    'usuario_id' => Auth::id(),
                    'accion' => 'EDITAR',
                    'tabla' => 'vales',
                    'item_id' => $vale->id,
                    'descripcion' => "Devolución registrada en vale {$vale->codigo_vale}",
                    'fecha' => now()
                ]);

                DB::commit();
                return redirect()->route('vales.show', $vale->id)->with('devolucion', '1');
            } else {
                DB::rollBack();
                return back()->withErrors(['error' => 'No se registraron devoluciones válidas.']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al procesar la devolución.']);
        }
    }
}
