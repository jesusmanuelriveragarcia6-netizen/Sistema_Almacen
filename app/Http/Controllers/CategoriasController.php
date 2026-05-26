<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriasController extends Controller
{
    public function index(Almacen $almacene)
    {
        $categorias = $almacene->categorias()->withCount('herramientas')->get();
        return view('categorias.index', ['almacen' => $almacene, 'categorias' => $categorias]);
    }

    public function store(Request $request, Almacen $almacene)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $almacene->categorias()->create($request->all());

        return back()->with('exito', 'Categoría registrada.');
    }

    public function update(Request $request, Almacen $almacene, Categoria $categoria)
    {
        if (!in_array(Auth::user()->rol, ['Administrador', 'Almacenero'])) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($request->all());

        return back()->with('exito', 'Categoría actualizada.');
    }

    public function destroy(Almacen $almacene, Categoria $categoria)
    {
        if (Auth::user()->rol !== 'Administrador') abort(403);
        $categoria->delete();
        return back()->with('exito', 'Categoría eliminada.');
    }
}
