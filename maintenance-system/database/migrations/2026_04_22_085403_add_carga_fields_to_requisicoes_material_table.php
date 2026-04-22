<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    Schema::table('requisicoes_material', function (Blueprint $table) {
        $table->decimal('peso_confirmado', 10, 3)->nullable()->after('total_final');
        $table->decimal('valor_carga', 15, 2)->nullable()->after('peso_confirmado');
    });
}
public function down() {
    Schema::table('requisicoes_material', function (Blueprint $table) {
        $table->dropColumn(['peso_confirmado', 'valor_carga']);
    });
}
};
