<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('requisicoes_material', function (Blueprint $table) {
        // Criamos o campo como string e nullable (pode ser vazio no início)
        $table->string('numero_guia')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('requisicoes_material', function (Blueprint $table) {
        $table->dropColumn('numero_guia');
    });
}
};
