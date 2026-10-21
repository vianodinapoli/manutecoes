<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Só acrescenta o que ainda não existe na tabela descargas.
        Schema::table('descargas', function (Blueprint $t) {
            $add = function (string $col, callable $def) use ($t) {
                if (! Schema::hasColumn('descargas', $col)) {
                    $def($t);
                }
            };

            // Carga registada no porto
            $add('sacos_alta',          fn ($t) => $t->integer('sacos_alta')->default(0));
            $add('peso_saco_alta',      fn ($t) => $t->decimal('peso_saco_alta', 10, 2)->default(0));
            $add('total_alta',          fn ($t) => $t->decimal('total_alta', 14, 2)->default(0));
            $add('sacos_baixa',         fn ($t) => $t->integer('sacos_baixa')->default(0));
            $add('peso_saco_baixa',     fn ($t) => $t->decimal('peso_saco_baixa', 10, 2)->default(0));
            $add('total_baixa',         fn ($t) => $t->decimal('total_baixa', 14, 2)->default(0));
            $add('total_sacos',         fn ($t) => $t->integer('total_sacos')->default(0));
            $add('peso_total_estimado', fn ($t) => $t->decimal('peso_total_estimado', 14, 2)->default(0));

            // Confirmação na balança
            $add('hora_chegada_balanca', fn ($t) => $t->string('hora_chegada_balanca', 10)->nullable());
            $add('tempo_transporte',     fn ($t) => $t->integer('tempo_transporte')->nullable());
            $add('peso_bruto',           fn ($t) => $t->decimal('peso_bruto', 14, 2)->nullable());
            $add('tara',                 fn ($t) => $t->decimal('tara', 14, 2)->nullable());
            $add('peso_liquido',         fn ($t) => $t->decimal('peso_liquido', 14, 2)->nullable());
            $add('sacos_confirmados',    fn ($t) => $t->integer('sacos_confirmados')->nullable());
            $add('observacoes',          fn ($t) => $t->text('observacoes')->nullable());
            $add('confirmado_por',       fn ($t) => $t->unsignedBigInteger('confirmado_por')->nullable());
        });
    }

    public function down(): void
    {
        // Intencionalmente vazio: não removemos colunas que podem já existir noutros ambientes.
    }
};
