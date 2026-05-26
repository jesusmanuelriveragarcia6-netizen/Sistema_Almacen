<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use App\Models\Trabajador;
use App\Models\Vale;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function global(Request $request)
    {
        $query = substr(strip_tags($request->get('q', '')), 0, 100);
        if (!$query) return response()->json([]);

        // Escapar caracteres especiales de LIKE para evitar DoS o resultados inesperados
        $query_safe = str_replace(['%', '_'], ['\%', '\_'], $query);

        // 1. Buscar Herramientas
        $herramientas = Herramienta::where('nombre', 'LIKE', "%{$query_safe}%")
            ->orWhere('codigo', 'LIKE', "%{$query_safe}%")
            ->take(5)
            ->get();
        
        foreach ($herramientas as $h) {
            $results[] = [
                'type' => 'Activo',
                'title' => $h->nombre,
                'subtitle' => "Código: {$h->codigo} - {$h->estado}",
                'url' => route('herramientas.show', $h->id),
                'icon' => 'fa-wrench'
            ];
        }

        // 2. Buscar Trabajadores
        $trabajadores = Trabajador::where('nombre', 'LIKE', "%{$query_safe}%")
            ->orWhere('apellidos', 'LIKE', "%{$query_safe}%")
            ->orWhere('dni', 'LIKE', "%{$query_safe}%")
            ->take(5)
            ->get();
        
        foreach ($trabajadores as $t) {
            $results[] = [
                'type' => 'Personal',
                'title' => "{$t->nombre} {$t->apellidos}",
                'subtitle' => "DNI: {$t->dni} - {$t->cargo}",
                'url' => route('trabajadores.show', $t->id),
                'icon' => 'fa-user'
            ];
        }

        // 3. Buscar Vales
        $vales = Vale::where('codigo_vale', 'LIKE', "%{$query_safe}%")
            ->take(5)
            ->get();
        
        foreach ($vales as $v) {
            $results[] = [
                'type' => 'Vale',
                'title' => "Vale #{$v->codigo_vale}",
                'subtitle' => "Estado: {$v->estado}",
                'url' => route('vales.show', $v->id),
                'icon' => 'fa-file-invoice'
            ];
        }

        return response()->json($results);
    }
}
