<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plano da operação: um registo por produto / tipo de saco
        Schema::create('discharge_operation_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('operation_id')->constrained('discharge_operations')->cascadeOnDelete();
            $t->string('nome', 100);
            $t->string('unidade', 10)->default('saco');          // saco | tonelada
            $t->decimal('peso_unitario_kg', 10, 3)->default(0);   // tonelada => 1000
            $t->decimal('quantidade', 12, 3)->default(0);         // nº de sacos (ou toneladas)
            $t->timestamps();
            $t->unique(['operation_id', 'nome']);
        });

        // O que cada camião leva, por produto do plano
        Schema::create('discharge_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('discharge_id')->constrained('descargas')->cascadeOnDelete();
            $t->foreignId('operation_item_id')->constrained('discharge_operation_items')->restrictOnDelete();
            $t->decimal('quantidade', 12, 3)->default(0);                // registado no porto
            $t->decimal('quantidade_confirmada', 12, 3)->nullable();     // confirmado na balança
            $t->timestamps();
            $t->unique(['discharge_id', 'operation_item_id']);
        });

        // Início da descarga + registo de alterações ao plano
        Schema::table('discharge_operations', function (Blueprint $t) {
            $t->timestamp('started_at')->nullable();
            $t->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $t->json('plan_log')->nullable();
        });

        $this->convertLegacy();
    }

    /**
     * Converte sacos_alta / sacos_baixa existentes (tabela descargas) para a estrutura nova.
     * O peso por saco do plano é o peso médio real já registado (ex.: 1200 / 1000 kg).
     */
    private function convertLegacy(): void
    {
        $tipos = [
            'Alta'  => ['sacos' => 'sacos_alta',  'total' => 'total_alta',  'peso' => 'peso_saco_alta',  'default' => 1200],
            'Baixa' => ['sacos' => 'sacos_baixa', 'total' => 'total_baixa', 'peso' => 'peso_saco_baixa', 'default' => 1000],
        ];

        $tipos = array_filter($tipos, fn ($c) => Schema::hasColumn('descargas', $c['sacos']));
        if (! $tipos) {
            return;
        }

        $now = now();

        foreach (DB::table('discharge_operations')->get() as $op) {
            $descargas = DB::table('descargas')->where('operation_id', $op->id)->get();
            if ($descargas->isEmpty()) {
                continue;
            }

            $kgTotal = 0;

            foreach ($tipos as $nome => $c) {
                $sacos = (float) $descargas->sum($c['sacos']);
                if ($sacos <= 0) {
                    continue;
                }

                $totalKg = Schema::hasColumn('descargas', $c['total']) ? (float) $descargas->sum($c['total']) : 0;
                $peso    = $totalKg > 0 ? round($totalKg / $sacos, 3) : 0;

                if ($peso <= 0 && Schema::hasColumn('descargas', $c['peso'])) {
                    $peso = (float) ($descargas->first(fn ($d) => (float) ($d->{$c['peso']} ?? 0) > 0)->{$c['peso']} ?? 0);
                }
                if ($peso <= 0) {
                    $peso = $c['default'];
                }

                $itemId = DB::table('discharge_operation_items')->insertGetId([
                    'operation_id'     => $op->id,
                    'nome'             => $nome,
                    'unidade'          => 'saco',
                    'peso_unitario_kg' => $peso,
                    'quantidade'       => $sacos,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);

                $kgTotal += $sacos * $peso;

                foreach ($descargas as $d) {
                    $q = (float) ($d->{$c['sacos']} ?? 0);
                    if ($q > 0) {
                        DB::table('discharge_items')->insert([
                            'discharge_id'      => $d->id,
                            'operation_item_id' => $itemId,
                            'quantidade'        => $q,
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ]);
                    }
                }
            }

            // Operação legado já está em curso
            DB::table('discharge_operations')->where('id', $op->id)->update([
                'status'              => 'active',
                'started_at'          => $now,
                'quantidade_esperada' => round($kgTotal / 1000, 3),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('discharge_operations', function (Blueprint $t) {
            $t->dropConstrainedForeignId('started_by');
            $t->dropColumn(['started_at', 'plan_log']);
        });
        Schema::dropIfExists('discharge_items');
        Schema::dropIfExists('discharge_operation_items');
    }
};
