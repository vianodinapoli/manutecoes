<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Assinatura do motorista, recolhida no tablet ao registar o camião no porto.
        // Fica numa tabela própria para não tornar a tabela "descargas" pesada.
        Schema::create('discharge_signatures', function (Blueprint $t) {
            $t->id();
            $t->foreignId('discharge_id')->unique()->constrained('descargas')->cascadeOnDelete();
            $t->string('nome', 100)->nullable();                 // nome do motorista no momento da assinatura
            $t->longText('imagem');                              // PNG em data URL (base64)
            $t->foreignId('assinado_por')->nullable()->constrained('users')->nullOnDelete(); // quem registou no porto
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discharge_signatures');
    }
};
