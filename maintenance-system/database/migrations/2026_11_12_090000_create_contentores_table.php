<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contentores')) {
            return;
        }

        Schema::create('contentores', function (Blueprint $t) {
            $t->id();
            $t->string('numero', 11)->unique();
            $t->string('tipo', 10)->default('20GP');
            $t->unsignedTinyInteger('tamanho')->default(20);
            $t->string('estado', 20)->default('no_parque')->index();
            $t->string('armador')->nullable();
            $t->string('cliente')->nullable();
            $t->string('navio')->nullable();
            $t->string('bl')->nullable();
            $t->string('selo')->nullable();
            $t->decimal('peso_kg', 10, 2)->nullable();
            $t->string('localizacao')->nullable();
            $t->date('data_entrada')->nullable();
            $t->date('data_saida')->nullable();
            $t->text('observacoes')->nullable();
            $t->unsignedBigInteger('criado_por')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contentores');
    }
};
