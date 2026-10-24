<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saidas_carga', function (Blueprint $t) {
            $t->string('ticket_numero', 20)->nullable()->unique();

            // 1ª pesagem: camião vazio
            $t->decimal('peso_vazio', 12, 3)->nullable();
            $t->dateTime('peso_vazio_em')->nullable();
            $t->unsignedBigInteger('peso_vazio_por')->nullable();

            // 2ª pesagem: camião cheio
            $t->decimal('peso_cheio', 12, 3)->nullable();
            $t->dateTime('peso_cheio_em')->nullable();
            $t->unsignedBigInteger('peso_cheio_por')->nullable();

            $t->decimal('peso_liquido', 12, 3)->nullable();
        });

        Schema::create('saida_carga_atrelados', function (Blueprint $t) {
            $t->id();
            $t->foreignId('saida_carga_id')->constrained('saidas_carga')->cascadeOnDelete();
            $t->string('matricula', 20);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saida_carga_atrelados');

        Schema::table('saidas_carga', function (Blueprint $t) {
            $t->dropUnique(['ticket_numero']);
            $t->dropColumn([
                'ticket_numero',
                'peso_vazio', 'peso_vazio_em', 'peso_vazio_por',
                'peso_cheio', 'peso_cheio_em', 'peso_cheio_por',
                'peso_liquido',
            ]);
        });
    }
};
