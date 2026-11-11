<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cada "saída" é uma operação de carga: um cliente, uma operação de origem
        // (o navio de onde sai o material) e uma lista de camiões registados.
        Schema::create('saidas', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 30)->unique();
            $t->string('cliente', 120);
            $t->date('data');
            $t->unsignedBigInteger('operation_id')->nullable()->index();
            $t->string('status', 20)->default('aberta');   // aberta | fechada
            $t->text('observacoes')->nullable();
            $t->unsignedBigInteger('criado_por')->nullable();
            $t->timestamp('fechada_em')->nullable();
            $t->timestamps();
        });

        Schema::table('saidas_carga', function (Blueprint $t) {
            $t->unsignedBigInteger('saida_id')->nullable()->index();
            $t->decimal('quantidade_pedida', 14, 3)->nullable();
        });

        // O que já existia na fila passa para uma saída "legado" (fechada),
        // para não ficar nada solto.
        if (DB::table('saidas_carga')->whereNull('saida_id')->exists()) {
            $id = DB::table('saidas')->insertGetId([
                'codigo'      => 'SAI-LEGADO',
                'cliente'     => 'Registos anteriores',
                'data'        => now()->toDateString(),
                'status'      => 'fechada',
                'fechada_em'  => now(),
                'observacoes' => 'Camiões registados antes de existirem saídas por operação.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            DB::table('saidas_carga')->whereNull('saida_id')->update(['saida_id' => $id]);
        }

        DB::table('saidas_carga')->whereNull('quantidade_pedida')->update(['quantidade_pedida' => DB::raw('quantidade')]);
    }

    public function down(): void
    {
        Schema::table('saidas_carga', function (Blueprint $t) {
            $t->dropColumn(['saida_id', 'quantidade_pedida']);
        });

        Schema::dropIfExists('saidas');
    }
};
