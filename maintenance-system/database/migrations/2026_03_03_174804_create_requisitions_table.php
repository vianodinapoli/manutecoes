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
        Schema::create('requisitions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained();
    $table->date('date');
    $table->decimal('total_liquid', 15, 2);
    $table->decimal('tax_amount', 15, 2)->default(0);
    $table->decimal('total_final', 15, 2);
    $table->boolean('has_tax')->default(false);
    $table->string('status')->default('PENDENTE'); // PENDENTE, APROVADO, CANCELADO
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
