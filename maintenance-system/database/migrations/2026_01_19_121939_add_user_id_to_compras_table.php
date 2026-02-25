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
    Schema::table('material_purchases', function (Blueprint $table) {
        // Cria a coluna e a relação com a tabela users
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_purchases', function (Blueprint $table) {
            //
        });
    }
};
