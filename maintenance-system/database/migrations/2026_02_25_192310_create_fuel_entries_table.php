<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('fuel_entries', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->integer('quantity'); // Litros adicionados
        $table->string('supplier')->nullable(); // Fornecedor (Ex: Galp, Petromoc)
        $table->string('invoice_no')->nullable(); // Nº da Fatura
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_entries');
    }
};
