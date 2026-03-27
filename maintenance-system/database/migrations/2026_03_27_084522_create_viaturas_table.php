<?php

// database/migrations/xxxx_create_viaturas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('viaturas', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo')->nullable();
            $table->string('matricula')->unique();
            $table->string('numero_chassi')->nullable();
            $table->string('cor')->nullable();
            $table->date('seguro');
            $table->date('ipo');
            $table->text('observacoes')->nullable();
            $table->json('metadata')->nullable(); // tags
            $table->timestamps();
        });

        Schema::create('viatura_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viatura_id')
                  ->constrained('viaturas')
                  ->onDelete('cascade');
            $table->string('nome');       // nome original do ficheiro
            $table->string('path');       // caminho em storage/app/public
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('tamanho')->nullable(); // bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viatura_documentos');
        Schema::dropIfExists('viaturas');
    }
};
