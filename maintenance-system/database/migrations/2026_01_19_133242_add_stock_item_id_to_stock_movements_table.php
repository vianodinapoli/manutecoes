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
        Schema::table('stock_movements', function (Blueprint $table) {
            // Verifica se a coluna já não existe para evitar erros
            if (!Schema::hasColumn('stock_movements', 'stock_item_id')) {
                $table->foreignId('stock_item_id')->after('id')->constrained('stock_items')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['stock_item_id']);
            $table->dropColumn('stock_item_id');
        });
    }
};