<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('company');                                                          // empresa que devolveu
            $table->decimal('quantity', 10, 2);                                                // litros devolvidos
            $table->date('date');                                                               // data da devolução
            $table->foreignId('tank_id')->constrained('tanks')->onDelete('cascade');           // tanque destino
            $table->string('notes')->nullable();                                                // observação opcional
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // quem registou
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_settlements');
    }
};