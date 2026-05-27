<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear las tablas base del sistema de almacén.
     * Estas tablas son requeridas por el sistema y deben existir
     * ANTES de las migraciones de alteración (add_extra_fields, etc.).
     *
     * Se usa Schema::hasTable() para que esta migración sea segura
     * tanto para instalaciones nuevas como para BDs existentes
     * donde las tablas se crearon manualmente.
     */
    public function up(): void
    {
        // ── Herramientas ──────────────────────────────────────
        if (!Schema::hasTable('herramientas')) {
            Schema::create('herramientas', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 20)->unique();
                $table->string('nombre', 150);
                $table->text('descripcion')->nullable();
                $table->enum('estado', ['Disponible', 'Prestada', 'En reparación', 'Baja'])->default('Disponible');
                $table->string('ubicacion')->nullable();
                $table->integer('stock_total')->default(1);
                $table->integer('stock_disponible')->default(1);
                $table->softDeletes();
            });
        }

        // ── Trabajadores ──────────────────────────────────────
        if (!Schema::hasTable('trabajadores')) {
            Schema::create('trabajadores', function (Blueprint $table) {
                $table->id();
                $table->string('dni', 20)->unique();
                $table->string('nombre', 100);
                $table->string('apellidos', 100);
                $table->string('telefono', 20)->nullable();
                $table->string('cargo', 100)->nullable();
                $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
                $table->timestamp('creado_en')->nullable();
                $table->softDeletes();
            });
        }

        // ── Vales de préstamo ─────────────────────────────────
        if (!Schema::hasTable('vales')) {
            Schema::create('vales', function (Blueprint $table) {
                $table->id();
                $table->string('codigo_vale', 20)->unique();
                $table->foreignId('trabajador_id')->constrained('trabajadores');
                $table->foreignId('usuario_id')->constrained('usuarios');
                $table->timestamp('fecha_creacion')->nullable();
                $table->timestamp('fecha_limite')->nullable();
                $table->enum('estado', ['Activo', 'Devuelto', 'Parcial', 'Vencido'])->default('Activo');
            });
        }

        // ── Detalles de vale ──────────────────────────────────
        if (!Schema::hasTable('vale_detalles')) {
            Schema::create('vale_detalles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vale_id')->constrained('vales')->onDelete('cascade');
                $table->foreignId('herramienta_id')->constrained('herramientas');
                $table->integer('cantidad_prestada')->default(1);
                $table->integer('cantidad_devuelta')->default(0);
            });
        }

        // ── Incidencias ───────────────────────────────────────
        if (!Schema::hasTable('incidencias')) {
            Schema::create('incidencias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('herramienta_id')->nullable()->constrained('herramientas');
                $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
                $table->foreignId('trabajador_id')->nullable()->constrained('trabajadores');
                $table->foreignId('vale_id')->nullable()->constrained('vales');
                $table->timestamp('fecha')->nullable();
                $table->integer('cantidad_afectada')->default(1);
                $table->string('tipo', 50)->nullable();
                $table->text('descripcion')->nullable();
                $table->decimal('monto_sancion', 10, 2)->nullable();
                $table->string('estado_sancion', 50)->nullable();
                $table->boolean('reparado')->default(false);
            });
        }

        // ── Mantenimientos ────────────────────────────────────
        if (!Schema::hasTable('mantenimientos')) {
            Schema::create('mantenimientos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('herramienta_id')->constrained('herramientas');
                $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
                $table->timestamp('fecha_inicio')->nullable();
                $table->timestamp('fecha_fin')->nullable();
                $table->text('descripcion')->nullable();
                $table->decimal('costo', 10, 2)->nullable();
                $table->enum('estado', ['Pendiente', 'En proceso', 'Completado'])->default('Pendiente');
            });
        }

        // ── Préstamos (registro directo) ──────────────────────
        if (!Schema::hasTable('prestamos')) {
            Schema::create('prestamos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('herramienta_id')->constrained('herramientas');
                $table->foreignId('trabajador_id')->constrained('trabajadores');
                $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
                $table->timestamp('fecha_prestamo')->nullable();
                $table->timestamp('fecha_devolucion_esperada')->nullable();
                $table->timestamp('fecha_devolucion_real')->nullable();
                $table->enum('estado', ['Activo', 'Devuelto', 'Vencido'])->default('Activo');
            });
        }

        // ── Logs de actividad ─────────────────────────────────
        if (!Schema::hasTable('logs')) {
            Schema::create('logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
                $table->string('accion', 50);
                $table->string('tabla', 50)->nullable();
                $table->unsignedBigInteger('item_id')->nullable();
                $table->text('descripcion')->nullable();
                $table->timestamp('fecha')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar en orden inverso por las FK
        Schema::dropIfExists('logs');
        Schema::dropIfExists('prestamos');
        Schema::dropIfExists('mantenimientos');
        Schema::dropIfExists('incidencias');
        Schema::dropIfExists('vale_detalles');
        Schema::dropIfExists('vales');
        Schema::dropIfExists('trabajadores');
        Schema::dropIfExists('herramientas');
    }
};
