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
        // ... dentro da função 'up'
Schema::create('maintenances', function (Blueprint $table) {
    $table->id();
    
    // **Chave Estrangeira** (Ligação à tabela 'machines')
    $table->foreignId('machine_id')->constrained()->onDelete('cascade'); 
    
    // Detalhes da Manutenção
    $table->text('failure_description');
    $table->string('status', 30)->default('Pendente'); 
    $table->dateTime('scheduled_date')->nullable();
    $table->dateTime('start_date')->nullable();
    $table->dateTime('end_date')->nullable();
    $table->text('technician_notes')->nullable();
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
