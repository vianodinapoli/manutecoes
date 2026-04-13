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
    Schema::table('stock_items', function (Blueprint $table) {
        // Remove o índice único simples se existir
        try { $table->dropUnique(['referencia']); } catch (\Exception $e) {}

        // Adiciona índice composto
        $table->unique(['referencia', 'marca_fabricante'], 'stock_items_ref_marca_unique');
    });
}

public function down(): void
{
    Schema::table('stock_items', function (Blueprint $table) {
        $table->dropUnique('stock_items_ref_marca_unique');
        $table->unique('referencia');
    });
}
};