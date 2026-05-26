<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->foreignId('almacen_id')->nullable()->after('id')->constrained('almacenes')->onDelete('set null');
            $table->foreignId('categoria_id')->nullable()->after('almacen_id')->constrained('categorias')->onDelete('set null');
            $table->json('metadata')->nullable()->after('uso');
        });
    }

    public function down(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->dropForeign(['almacen_id']);
            $table->dropForeign(['categoria_id']);
            $table->dropColumn(['almacen_id', 'categoria_id', 'metadata']);
        });
    }
};
