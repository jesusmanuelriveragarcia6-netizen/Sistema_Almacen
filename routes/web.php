<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HerramientasController;
use App\Http\Controllers\TrabajadoresController;
use App\Http\Controllers\ValesController;
use App\Http\Controllers\IncidenciasController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/global-search', [\App\Http\Controllers\SearchController::class, 'global'])->name('api.global-search');

    // Cortex Assistant (Centro de Monitoreo IA)
    Route::get('/cortex', [\App\Http\Controllers\CortexController::class, 'index'])->name('cortex.index');
    Route::get('/cortex/scan', [\App\Http\Controllers\CortexController::class, 'runFullScan'])->name('cortex.scan');
    Route::post('/cortex/repair/{component}', [\App\Http\Controllers\CortexController::class, 'repair'])->name('cortex.repair');
    Route::post('/cortex/incident/dismiss', [\App\Http\Controllers\CortexController::class, 'dismissIncident'])->name('cortex.dismiss');
    Route::post('/cortex/incident/restore', [\App\Http\Controllers\CortexController::class, 'restoreIncidents'])->name('cortex.restore');

    // Herramientas
    Route::get('herramientas/ubicaciones', [HerramientasController::class, 'ubicaciones'])->name('herramientas.ubicaciones');
    Route::post('herramientas/{id}/restock', [HerramientasController::class, 'restock'])->name('herramientas.restock');
    Route::resource('herramientas', HerramientasController::class);

    // Almacenes & Categorías
    Route::resource('almacenes', \App\Http\Controllers\AlmacenesController::class);
    Route::resource('almacenes.categorias', \App\Http\Controllers\CategoriasController::class);

    // Trabajadores
    Route::get('trabajadores/inactivos', [TrabajadoresController::class, 'inactivos'])->name('trabajadores.inactivos');
    Route::resource('trabajadores', TrabajadoresController::class);
    Route::post('trabajadores/{id}/reactivar', [TrabajadoresController::class, 'reactivar'])->name('trabajadores.reactivar');

    // Vales
    Route::get('vales/historial', [ValesController::class, 'historial'])->name('vales.historial');
    Route::get('vales/devolver', [ValesController::class, 'devolver'])->name('vales.devolver_form');
    Route::post('vales/buscar', [ValesController::class, 'buscarVale'])->name('vales.buscar');
    Route::get('vales/procesar_devolucion/{vale}', [ValesController::class, 'procesar_devolucion'])->name('vales.procesar_devolucion');
    Route::post('vales/procesar_devolucion/{vale}', [ValesController::class, 'guardar_devolucion'])->name('vales.guardar_devolucion');
    Route::resource('vales', ValesController::class)->only(['index', 'create', 'store', 'show']);

    // Incidencias
    Route::get('incidencias', [IncidenciasController::class, 'index'])->name('incidencias.index');
    Route::post('incidencias', [IncidenciasController::class, 'store'])->name('incidencias.store');

    // Mantenimiento
    Route::get('mantenimiento', [MantenimientoController::class, 'index'])->name('mantenimiento.index');
    Route::post('mantenimiento/{id}/reparar', [MantenimientoController::class, 'reparar'])->name('mantenimiento.reparar');

    // Reportes
    Route::get('reportes', [ReportesController::class, 'index'])->name('reportes.index');
    Route::get('reportes/herramientas', [ReportesController::class, 'herramientas'])->name('reportes.herramientas');
    Route::get('reportes/usuarios', [ReportesController::class, 'usuarios'])->name('reportes.usuarios');
    Route::get('reportes/personal', [ReportesController::class, 'personal'])->name('reportes.personal');
    Route::get('reportes/temporal', [ReportesController::class, 'temporal'])->name('reportes.temporal');

    // Usuarios
    Route::resource('usuarios', UsuariosController::class);
});

require __DIR__.'/auth.php';
