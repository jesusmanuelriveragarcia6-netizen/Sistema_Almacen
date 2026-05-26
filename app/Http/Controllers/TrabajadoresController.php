<?php

namespace App\Http\Controllers;

use App\Models\Trabajador;
use App\Models\Log;
use App\Models\Vale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrabajadoresController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = substr(strip_tags($request->query('search', '')), 0, 100);
        $busqueda_segura = str_replace(['%', '_'], ['\%', '\_'], $busqueda);

        $trabajadores = Trabajador::where('estado', 'Activo')
            ->when($busqueda_segura, function ($query, $busqueda_segura) {
                return $query->where('nombre', 'like', "%{$busqueda_segura}%")
                             ->orWhere('apellidos', 'like', "%{$busqueda_segura}%")
                             ->orWhere('dni', 'like', "%{$busqueda_segura}%");
            })->paginate(10);

        return view('trabajadores.index', compact('trabajadores', 'busqueda'));
    }

    public function inactivos(Request $request)
    {
        $busqueda = substr(strip_tags($request->query('search', '')), 0, 100);
        $busqueda_segura = str_replace(['%', '_'], ['\%', '\_'], $busqueda);

        $trabajadores = Trabajador::where('estado', '!=', 'Activo')
            ->when($busqueda_segura, function ($query, $busqueda_segura) {
                return $query->where('nombre', 'like', "%{$busqueda_segura}%")
                             ->orWhere('apellidos', 'like', "%{$busqueda_segura}%")
                             ->orWhere('dni', 'like', "%{$busqueda_segura}%");
            })->paginate(10);

        return view('trabajadores.inactivos', compact('trabajadores', 'busqueda'));
    }

    public function create()
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        return view('trabajadores.create');
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $request->validate([
            'dni' => 'required|string|size:8|unique:trabajadores,dni',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);

        $trabajador = Trabajador::create([
            'dni' => $request->dni,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'cargo' => $request->cargo,
            'telefono' => $request->telefono,
            'estado' => 'Activo'
        ]);

        Log::create([
            'usuario_id' => Auth::id(),
            'accion' => 'CREAR',
            'tabla' => 'trabajadores',
            'item_id' => $trabajador->id,
            'descripcion' => "Trabajador registrado: {$trabajador->nombre} {$trabajador->apellidos}",
            'fecha' => now()
        ]);

        return redirect()->route('trabajadores.index')->with('exito', '1');
    }

    public function edit(Trabajador $trabajadore) // La variable que Laravel inyecta automáticamente puede llamarse $trabajadore por el singular de 'trabajadores'
    {
        $trabajador = $trabajadore; // Alias por comodidad
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        return view('trabajadores.edit', compact('trabajador'));
    }

    public function update(Request $request, Trabajador $trabajadore)
    {
        $trabajador = $trabajadore;
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) {
            abort(403);
        }

        $request->validate([
            'dni' => 'required|string|size:8|unique:trabajadores,dni,' . $trabajador->id,
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);

        $trabajador->update([
            'dni' => $request->dni,
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'cargo' => $request->cargo,
            'telefono' => $request->telefono,
        ]);

        Log::create([
            'usuario_id' => Auth::id(),
            'accion' => 'EDITAR',
            'tabla' => 'trabajadores',
            'item_id' => $trabajador->id,
            'descripcion' => "Actualizado: {$trabajador->nombre} {$trabajador->apellidos}",
            'fecha' => now()
        ]);

        return redirect()->route('trabajadores.index')->with('modificado', '1');
    }

    public function show(Trabajador $trabajadore)
    {
        $trabajador = $trabajadore;
        $vales = Vale::where('trabajador_id', $trabajador->id)->orderBy('id', 'desc')->get();

        return view('trabajadores.show', compact('trabajador', 'vales'));
    }

    public function destroy(Trabajador $trabajadore)
    {
        $trabajador = $trabajadore;
        if (Auth::user()->rol !== 'Administrador') {
            abort(403);
        }

        $trabajador->update(['estado' => 'Inactivo']);
        $trabajador->delete(); // Soft delete

        Log::create([
            'usuario_id' => Auth::id(),
            'accion' => 'ELIMINAR',
            'tabla' => 'trabajadores',
            'item_id' => $trabajador->id,
            'descripcion' => "Dado de baja: {$trabajador->nombre} {$trabajador->apellidos}",
            'fecha' => now()
        ]);

        return redirect()->route('trabajadores.index')->with('eliminado', '1');
    }

    public function reactivar($id)
    {
        if (Auth::user()->rol !== 'Administrador') {
            abort(403);
        }

        $trabajador = Trabajador::withTrashed()->findOrFail($id);
        $trabajador->restore();
        $trabajador->update(['estado' => 'Activo']);

        Log::create([
            'usuario_id' => Auth::id(),
            'accion' => 'EDITAR',
            'tabla' => 'trabajadores',
            'item_id' => $trabajador->id,
            'descripcion' => "Reactivado: {$trabajador->nombre} {$trabajador->apellidos}",
            'fecha' => now()
        ]);

        return redirect()->route('trabajadores.inactivos')->with('exito', '1');
    }
}
