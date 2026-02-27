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
    Schema::create('suppliers', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // Código do Fornecedor
        $table->string('name');
        $table->string('address')->nullable(); // Morada
        $table->string('nuit')->nullable();
        $table->string('contact')->nullable();
        $table->string('email')->nullable();
        $table->json('metadata')->nullable(); // Campo flexível para dados extras
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
