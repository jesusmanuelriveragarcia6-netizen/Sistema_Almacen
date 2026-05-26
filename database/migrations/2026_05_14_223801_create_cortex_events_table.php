<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cortex_events', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // INFO, SECURITY, SYSTEM, DIAGNOSTIC
            $table->string('severity')->default('INFO'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->text('message');
            $table->json('metadata')->nullable(); // Para guardar detalles técnicos
            $table->foreignId('user_id')->nullable()->constrained('usuarios'); // Quién autorizó o causó el evento
            $table->boolean('is_authorized')->default(false); // Para reparaciones que requieren aval
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cortex_events');
    }
};
