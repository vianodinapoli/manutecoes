<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discharge_operations', function (Blueprint $t) {
            $t->id();
            $t->string('navio');
            $t->string('bl')->nullable()->index();
            $t->string('factura')->nullable();
            $t->string('empresa');
            $t->decimal('quantidade_esperada', 12, 3)->default(0); // toneladas
            $t->date('data_chegada')->nullable();
            $t->string('status', 20)->default('active')->index();   // planned | active | archived
            $t->timestamp('archived_at')->nullable();
            $t->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $t->json('metadata')->nullable();                        // dados extra livres (chave => valor)
            $t->text('observacoes')->nullable();
            $t->timestamps();
        });

        Schema::table('descargas', function (Blueprint $t) {
            $t->foreignId('operation_id')->nullable()->after('id')
              ->constrained('discharge_operations')->nullOnDelete();
        });

        // Descargas já existentes ficam agrupadas numa operação "legado"
        if (DB::table('descargas')->count() > 0) {
            $id = DB::table('discharge_operations')->insertGetId([
                'navio'               => 'Operação inicial (legado)',
                'empresa'             => '—',
                'quantidade_esperada' => 0,
                'status'              => 'active',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
            DB::table('descargas')->update(['operation_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('descargas', function (Blueprint $t) {
            $t->dropConstrainedForeignId('operation_id');
        });
        Schema::dropIfExists('discharge_operations');
    }
};
