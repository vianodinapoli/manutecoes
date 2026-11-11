<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_items')) {
            return;
        }

        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->default('');
            $table->string('numero_armazem')->nullable();
            $table->string('seccao_armazem')->nullable();
            $table->string('referencia')->nullable();
            $table->string('marca_fabricante')->nullable();
            $table->string('modelo')->nullable();
            $table->string('categoria')->nullable();
            $table->string('sistema_maquina')->nullable();
            $table->string('estado')->nullable();
            $table->integer('quantidade')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['referencia', 'marca_fabricante'], 'stock_items_ref_marca_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};