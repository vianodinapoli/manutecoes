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
            $table->string('numero_guia')->unique();
            $table->date('data');
            $table->string('motorista');
            $table->string('matricula');
            $table->string('transportadora');
            $table->integer('numero_sacos')->default(0);
            $table->integer('sacos_alta')->default(0);
            $table->integer('sacos_baixa')->default(0);
            $table->decimal('peso_saco_alta', 8, 2)->nullable();
            $table->decimal('peso_saco_baixa', 8, 2)->nullable();
            $table->decimal('peso_total_porto', 10, 2)->default(0);
            $table->time('hora_saida_porto')->nullable();
            $table->time('hora_chegada_balanca')->nullable();
            $table->integer('tempo_transporte')->nullable();
            $table->decimal('peso_bruto', 10, 2)->nullable();
            $table->decimal('tara', 10, 2)->nullable();
            $table->decimal('peso_liquido', 10, 2)->nullable();
            $table->integer('sacos_confirmados')->nullable();
            $table->string('status')->default('pending');
            $table->text('observacoes')->nullable();
            $table->foreignId('registado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descargas');
    }
};