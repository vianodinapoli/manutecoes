<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cabeçalho do upload: uma lista "carta nova" por DO dentro de uma operação
        Schema::create('cartas_novas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('discharge_operations')->cascadeOnDelete();
            $table->string('do_numero', 40);                 // ex.: DO-0004785 (célula C2 do Excel)
            $table->string('ficheiro')->nullable();          // nome original do Excel
            $table->unsignedInteger('total_camioes')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Voltar a carregar o mesmo DO substitui a lista anterior
            $table->unique(['operation_id', 'do_numero']);
        });

        // Uma linha por camião. A matrícula NÃO é única: o mesmo camião pode aparecer
        // com motoristas diferentes.
        Schema::create('carta_nova_camioes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carta_nova_id')->constrained('cartas_novas')->cascadeOnDelete();
            $table->string('matricula', 20);                 // normalizada: maiúsculas, sem espaços/hífenes
            $table->string('primeiro_nome', 100);
            $table->string('ultimo_nome', 100);
            $table->string('carta_conducao', 40);            // texto, ex.: 10146384/6
            $table->timestamps();

            $table->index('matricula');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carta_nova_camioes');
        Schema::dropIfExists('cartas_novas');
    }
};
