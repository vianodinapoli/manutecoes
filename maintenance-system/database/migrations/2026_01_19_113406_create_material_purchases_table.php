<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela Principal (A Requisição)
        Schema::create('material_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('fornecedor')->nullable();
            $table->string('urgencia')->default('Normal');
            $table->text('description')->nullable();
            $table->string('status')->default('Pendente');
            $table->timestamps();
        });

        // 2. Tabela de Itens (Múltiplos produtos por requisição)
        Schema::create('material_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_purchase_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('destino');
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
        });

        // 3. Tabela de Anexos (Múltiplos ficheiros por requisição)
        Schema::create('material_purchase_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_purchase_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_purchase_attachments');
        Schema::dropIfExists('material_purchase_items');
        Schema::dropIfExists('material_purchases');
    }
};