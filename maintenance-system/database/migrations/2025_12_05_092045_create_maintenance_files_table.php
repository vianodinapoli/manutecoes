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
        Schema::create('maintenance_files', function (Blueprint $table) {
            $table->id();
            // Chave estrangeira para a manutenção, apaga o ficheiro se a manutenção for apagada
            $table->foreignId('maintenance_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // Nome original do ficheiro
            $table->string('filepath'); // Caminho guardado no storage (ex: maintenances/1/abc.pdf)
            $table->string('mime_type')->nullable(); // Tipo MIME
            $table->unsignedBigInteger('filesize')->nullable(); // Tamanho em bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_files');
    }
};