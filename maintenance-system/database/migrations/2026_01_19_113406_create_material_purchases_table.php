<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('material_purchases', function (Blueprint $table) {
        $table->id();
        $table->string('item_name');
        $table->integer('quantity');
        $table->decimal('price', 10, 2)->nullable();
        
        // Status solicitado
        $table->enum('status', ['Pendente', 'Em processo', 'Aprovado', 'Rejeitado'])
              ->default('Pendente');
        
        // Arquivo e Metadados
        $table->string('quotation_file')->nullable();
        $table->json('metadata')->nullable(); 
        
        $table->timestamps();
    });
}
};
