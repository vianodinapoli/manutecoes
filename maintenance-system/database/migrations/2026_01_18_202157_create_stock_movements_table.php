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
    Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        // Liga o consumo à manutenção específica
        $table->foreignId('maintenance_id')->constrained()->onDelete('cascade');
        // Liga à máquina (facilitando relatórios diretos por máquina)
        $table->foreignId('machine_id')->constrained();
        // O artigo que saiu do stock
        $table->foreignId('item_id')->constrained();
        $table->decimal('quantity', 10, 2); 
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
