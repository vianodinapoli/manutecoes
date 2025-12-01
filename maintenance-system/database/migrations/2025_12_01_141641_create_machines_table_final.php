<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_machines_table_final.php

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
        Schema::create('machines', function (Blueprint $table) {
            $table->id(); // ID
            
            // CAMPOS NOVOS
            $table->string('numero_interno', 50)->unique(); // Número Interno (Com índice único, substituindo serial_number)
            $table->string('tipo_equipamento', 100)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('localizacao', 100)->nullable();
            $table->string('operador', 100)->nullable();
            
            // STATUS FINAL (ENUM)
            $table->enum('status', ['Operacional', 'Avariada', 'Em Manutenção', 'Desativada'])->default('Operacional');
            
            $table->text('observacoes')->nullable(); // Observações
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
