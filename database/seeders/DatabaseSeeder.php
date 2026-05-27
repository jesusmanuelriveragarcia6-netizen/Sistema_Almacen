<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Crea un usuario Administrador por defecto para que
     * cualquier persona que clone el repo pueda iniciar sesión.
     */
    public function run(): void
    {
        // Crear el usuario administrador por defecto (solo si no existe)
        if (!Usuario::where('usuario', 'admin')->exists()) {
            Usuario::create([
                'nombre' => 'Administrador',
                'usuario' => 'admin',
                'password' => Hash::make('Admin123!'),
                'rol' => 'Administrador',
                'creado_en' => now(),
            ]);
        }
    }
}
