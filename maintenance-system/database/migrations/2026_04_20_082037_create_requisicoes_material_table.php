<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisicoes_material', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('destino');
            $table->string('matricula')->nullable();
            $table->string('motorista')->nullable();
            $table->string('responsavel')->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('status', ['PENDENTE', 'APROVADO', 'CANCELADO'])->default('PENDENTE');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('requisicao_material_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisicao_material_id')
                  ->constrained('requisicoes_material')
                  ->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 3);
            $table->string('unit', 10);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicao_material_items');
        Schema::dropIfExists('requisicoes_material');
    }
};