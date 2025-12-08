<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            
            // --- Localização e Identificação ---
            $table->string('numero_armazem');
            $table->string('seccao_armazem')->nullable();
            $table->string('referencia')->unique(); // Referência / Part Number (Deve ser único)
            
            // --- Detalhes do Item ---
            $table->string('marca_fabricante')->nullable();
            $table->string('modelo')->nullable();
            $table->string('categoria')->nullable();
            $table->string('sistema_maquina')->nullable();
            
            // --- Controlo de Stock ---
            // Enum para garantir que o estado é um dos três valores definidos
            $table->enum('estado', ['Novo', 'Recondicionado', 'Usado'])->default('Novo');
            $table->integer('quantidade')->default(0);
            
            // --- Campos Dinâmicos (Metadata) ---
            // Coluna JSON para armazenar os campos personalizados do utilizador
            $table->json('metadata')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};