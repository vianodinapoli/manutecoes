<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // status passa a VARCHAR (aceita 'loaded' e 'weighing')
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE descargas MODIFY status VARCHAR(20) NOT NULL DEFAULT 'loaded'");
        }

        // Preenche os tempos dos registos antigos (aproximação) para não ficarem
        // bloqueados pela regra de não saltar etapas.
        DB::table('descargas')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $r) {
                $upd   = [];
                $pesos = array_filter([$r->peso_bruto_em, $r->tara_em]);

                if (! $r->carregado_em && $r->created_at) {
                    $upd['carregado_em'] = $r->created_at;
                }
                if (! $r->saiu_porto_em && $r->created_at
                    && in_array($r->status, ['in_transit', 'weighing', 'confirmed'], true)) {
                    $upd['saiu_porto_em'] = $r->created_at;
                }
                if (! $r->chegou_balanca_em && $pesos
                    && in_array($r->status, ['weighing', 'confirmed'], true)) {
                    $upd['chegou_balanca_em'] = min($pesos);
                }
                if (! $r->saiu_descarga_em && $pesos && $r->status === 'confirmed') {
                    $upd['saiu_descarga_em'] = max($pesos);
                }

                if ($upd) {
                    DB::table('descargas')->where('id', $r->id)->update($upd);
                }
            }
        });
    }

    public function down(): void {}
};
