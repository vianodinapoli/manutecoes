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
    Schema::create('fuel_logs', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->string('plate'); // Matrícula
        $table->string('company'); // Empresa
        $table->integer('start_counter'); // Contador Inicial
        $table->integer('end_counter'); // Contador Final
        $table->integer('quantity'); // Quantidade abastecida (calculada)
        $table->string('operator'); // Quem abasteceu
        $table->string('driver'); // Motorista
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_logs');
    }
};
