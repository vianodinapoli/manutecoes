<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saidas_carga', function (Blueprint $t) {
            // preenchido nas linhas de carregamento (viagens); vazio nas linhas de registo
            $t->unsignedBigInteger('registo_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('saidas_carga', function (Blueprint $t) {
            $t->dropColumn('registo_id');
        });
    }
};
