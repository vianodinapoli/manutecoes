<?php

use App\Models\DischargeOperation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $opTable = (new DischargeOperation)->getTable();

        Schema::create('locations', function (Blueprint $t) {
            $t->id();
            $t->string('nome', 100)->unique();
            $t->enum('tipo', ['armazem', 'yard']);
            $t->decimal('capacidade_ton', 12, 3)->nullable();
            $t->boolean('ativo')->default(true);
            $t->timestamps();
        });

        Schema::create('discharge_operation_location', function (Blueprint $t) use ($opTable) {
            $t->foreignId('discharge_operation_id')->constrained($opTable)->cascadeOnDelete();
            $t->foreignId('location_id')->constrained('locations');
            $t->boolean('principal')->default(false);
            $t->primary(['discharge_operation_id', 'location_id']);
        });

        Schema::table('descargas', function (Blueprint $t) {
            $t->foreignId('location_id')->nullable()->constrained('locations');
            $t->boolean('location_extra')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('descargas', function (Blueprint $t) {
            $t->dropConstrainedForeignId('location_id');
            $t->dropColumn('location_extra');
        });
        Schema::dropIfExists('discharge_operation_location');
        Schema::dropIfExists('locations');
    }
};
