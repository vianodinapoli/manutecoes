<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Saídas de carga de clientes.
     *
     * Uma linha nasce "pendente" (cliente na fila, ainda sem descarga associada)
     * e passa a "carregada" quando é arrastada para uma descarga: nessa altura
     * ficam gravados a operação, a descarga e o produto, e a quantidade é
     * descontada do stock dessa descarga/operação.
     */
    public function up(): void
    {
        Schema::create('saidas_carga', function (Blueprint $t) {
            $t->id();

            // dados do cliente / camião que vem carregar
            $t->date('data');
            $t->string('empresa', 120);
            $t->string('nome', 100);
            $t->string('apelido', 100)->nullable();
            $t->decimal('quantidade', 12, 3)->default(0);
            $t->string('matricula', 20)->nullable();
            $t->string('tipo_atrelado', 10)->nullable();   // normal | link
            $t->json('metadata')->nullable();              // campos extra (chave => valor)

            // estado e ligação à descarga de onde sai a carga
            $t->string('status', 12)->default('pendente'); // pendente | carregada
            $t->foreignId('operation_id')->nullable()->constrained('discharge_operations')->nullOnDelete();
            $t->foreignId('discharge_id')->nullable()->constrained('descargas')->nullOnDelete();
            $t->unsignedBigInteger('operation_item_id')->nullable();
            $t->dateTime('carregado_em')->nullable();

            $t->text('observacoes')->nullable();
            $t->unsignedBigInteger('registado_por')->nullable();
            $t->unsignedBigInteger('carregado_por')->nullable();
            $t->timestamps();

            $t->index(['status', 'data']);
            $t->index(['discharge_id', 'operation_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saidas_carga');
    }
};
