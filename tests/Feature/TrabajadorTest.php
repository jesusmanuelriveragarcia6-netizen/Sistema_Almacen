<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Trabajador;
use App\Models\Usuario;

class TrabajadorTest extends TestCase
{
    // Usamos DatabaseTransactions para interactuar con tu BD real en pruebas sin afectar los datos
    use DatabaseTransactions;

    /**
     * Ejemplo de test: Verificar que un bug donde se creaban trabajadores con DNI duplicado
     * ha sido resuelto y ahora devuelve un error de validación.
     */
    public function test_no_se_puede_crear_trabajador_con_dni_duplicado()
    {
        $admin = Usuario::factory()->create(['rol' => 'Administrador']);
        $dniFalso = '99998888';
        
        Trabajador::create([
            'nombre' => 'Juan',
            'apellidos' => 'Perez',
            'dni' => $dniFalso,
            'cargo' => 'Operario',
            'estado' => 'Activo'
        ]);

        $response = $this->actingAs($admin)->post('/trabajadores', [
            'nombre' => 'Maria',
            'apellidos' => 'Gomez',
            'dni' => $dniFalso, // DNI DUPLICADO
            'cargo' => 'Supervisora',
            'estado' => 'Activo'
        ]);

        $response->assertSessionHasErrors('dni');
        
        // Comprobamos que en la BD solo exista 1 trabajador con este DNI
        $this->assertEquals(1, Trabajador::where('dni', $dniFalso)->count());
    }

    /**
     * Ejemplo de test: Evaluar una nueva modificación (Ej. Inactivar trabajador mediante DELETE)
     */
    public function test_se_puede_cambiar_el_estado_de_un_trabajador_a_inactivo()
    {
        $admin = Usuario::factory()->create(['rol' => 'Administrador']);
        
        $trabajador = Trabajador::create([
            'nombre' => 'Carlos',
            'apellidos' => 'Lopez',
            'dni' => '88887777',
            'cargo' => 'Tecnico',
            'estado' => 'Activo'
        ]);

        // Para inactivar usamos la ruta DELETE (destroy) según el controlador
        $response = $this->actingAs($admin)->delete('/trabajadores/' . $trabajador->id);

        $trabajador->refresh();

        $response->assertRedirect(route('trabajadores.index'));
        $this->assertEquals('Inactivo', $trabajador->estado);
    }
}
