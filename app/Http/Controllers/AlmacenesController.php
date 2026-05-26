<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlmacenesController extends Controller
{
    public function index()
    {
        $almacenes = Almacen::withCount('herramientas')->get();
        return view('almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        if (Auth::user()->rol !== 'Administrador') abort(403);
        return view('almacenes.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->rol !== 'Administrador') abort(403);
        
        $request->validate([
            'nombre' => 'required|string|unique:almacenes|max:100',
            'ubicacion_general' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
        ]);

        Almacen::create($request->all());

        return redirect()->route('almacenes.index')->with('exito', 'Almacén creado correctamente.');
    }

    public function edit(Almacen $almacene) // Laravel resource name for plural ending in 's'
    {
        if (Auth::user()->rol !== 'Administrador') abort(403);
        return view('almacenes.edit', ['almacen' => $almacene]);
    }

    public function update(Request $request, Almacen $almacene)
    {
        if (Auth::user()->rol !== 'Administrador') abort(403);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:almacenes,nombre,' . $almacene->id,
            'ubicacion_general' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
        ]);

        $almacene->update($request->all());

        return redirect()->route('almacenes.index')->with('exito', 'Almacén actualizado.');
    }

    public function destroy(Almacen $almacene)
    {
        if (Auth::user()->rol !== 'Administrador') abort(403);
        $almacene->delete();
        return redirect()->route('almacenes.index')->with('exito', 'Almacén enviado a papelera.');
    }
}
