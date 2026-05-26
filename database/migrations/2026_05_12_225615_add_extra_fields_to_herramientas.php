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
        Schema::table('herramientas', function (Blueprint $table) {
            $table->integer('stock_minimo')->default(2)->after('stock_disponible');
            $table->string('almacen')->nullable()->after('ubicacion');
            $table->string('tamano')->nullable()->after('almacen');
            $table->string('uso')->nullable()->after('tamano');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->dropColumn(['stock_minimo', 'almacen', 'tamano', 'uso']);
        });
    }
};
