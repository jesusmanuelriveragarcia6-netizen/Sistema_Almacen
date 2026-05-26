<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Herramienta;
use App\Models\Almacen;
use App\Models\Categoria;

return new class extends Migration
{
    public function up(): void
    {
        $herramientas = Herramienta::all();

        foreach ($herramientas as $h) {
            $almacenId = null;
            $categoriaId = null;

            // Migration of Almacen
            if ($h->almacen) {
                $almacen = Almacen::firstOrCreate(['nombre' => $h->almacen]);
                $almacenId = $almacen->id;
            }

            // Migration of Seccion (now Categoria)
            if ($h->seccion && $almacenId) {
                $categoria = Categoria::firstOrCreate([
                    'almacen_id' => $almacenId,
                    'nombre' => $h->seccion
                ]);
                $categoriaId = $categoria->id;
            }

            $h->update([
                'almacen_id' => $almacenId,
                'categoria_id' => $categoriaId
            ]);
        }
    }

    public function down(): void
    {
        // No down migration needed for data move, 
        // as the IDs will just be removed if the table columns are dropped.
    }
};
