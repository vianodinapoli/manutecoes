<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descargas', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->date('data');
            $table->string('motorista');
            $table->string('matricula');
            $table->string('transportadora');
            $table->string('numero_guia')->unique();
            $table->time('hora_saida_porto')->nullable();
            $table->foreignId('registado_por')->nullable()->constrained('users')->nullOnDelete();

            // Carga Alta (azul)
            $table->integer('sacos_alta')->default(0);
            $table->decimal('peso_saco_alta', 10, 2)->default(0);
            $table->decimal('total_alta', 12, 2)->default(0);

            // Carga Baixa (vermelho)
            $table->integer('sacos_baixa')->default(0);
            $table->decimal('peso_saco_baixa', 10, 2)->default(0);
            $table->decimal('total_baixa', 12, 2)->default(0);

            // Total geral (alta + baixa)
            $table->integer('total_sacos')->default(0);
            $table->decimal('peso_total_estimado', 12, 2)->default(0);

            // Confirmação Balança
            $table->time('hora_chegada_balanca')->nullable();
            $table->integer('tempo_transporte')->nullable();
            $table->decimal('peso_bruto', 12, 2)->nullable();
            $table->decimal('tara', 12, 2)->nullable();
            $table->decimal('peso_liquido', 12, 2)->nullable();
            $table->integer('sacos_confirmados')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('confirmado_por')->nullable()->constrained('users')->nullOnDelete();

            // Estado
            $table->enum('status', ['pending', 'confirmed', 'in_transit'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descargas');
    }
};