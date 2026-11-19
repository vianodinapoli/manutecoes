<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Fuso em que as horas H:i do pátio foram escritas à mão. */
    private const FUSO_LOCAL = 'Africa/Maputo';

    private array $etapas = ['carregado', 'saiu_porto', 'chegou_balanca', 'saiu_descarga'];

    public function up(): void
    {
        $existentes = Schema::getColumnListing('descargas');

        Schema::table('descargas', function (Blueprint $t) use ($existentes) {
            foreach ($this->etapas as $e) {
                if (! in_array($e . '_em', $existentes, true)) {
                    $t->timestamp($e . '_em')->nullable();
                }
                if (! in_array($e . '_por', $existentes, true)) {
                    $t->unsignedBigInteger($e . '_por')->nullable();
                }
            }
        });

        $this->preencherHistorico();
    }

    public function down(): void
    {
        $existentes = Schema::getColumnListing('descargas');
        $apagar = [];
        foreach ($this->etapas as $e) {
            foreach (['_em', '_por'] as $suf) {
                if (in_array($e . $suf, $existentes, true)) {
                    $apagar[] = $e . $suf;
                }
            }
        }
        if ($apagar) {
            Schema::table('descargas', fn (Blueprint $t) => $t->dropColumn($apagar));
        }
    }

    /**
     * Converte as horas soltas (H:i, hora local de Maputo) em datetime completo,
     * guardado no fuso da aplicação (UTC).
     */
    private function preencherHistorico(): void
    {
        $temChegada = Schema::hasColumn('descargas', 'hora_chegada_balanca');

        DB::table('descargas')->orderBy('id')->chunkById(200, function ($rows) use ($temChegada) {
            foreach ($rows as $r) {
                if (! $r->data) {
                    continue;
                }
                $dia   = Carbon::parse($r->data)->format('Y-m-d');
                $upd   = [];
                $saida = null;

                if (! empty($r->hora_saida_porto)) {
                    $saida = $this->momento($dia, $r->hora_saida_porto);
                    if ($saida) {
                        $upd['saiu_porto_em'] = $this->paraBd($saida);
                    }
                }

                if ($temChegada && ! empty($r->hora_chegada_balanca)) {
                    $ch = $this->momento($dia, $r->hora_chegada_balanca);
                    if ($ch) {
                        if ($saida && $ch->lt($saida)) {
                            $ch->addDay(); // chegou depois da meia-noite
                        }
                        $upd['chegou_balanca_em'] = $this->paraBd($ch);
                    }
                }

                if ($upd) {
                    DB::table('descargas')->where('id', $r->id)->update($upd);
                }
            }
        });
    }

    /** Hora local de Maputo (fuso explícito). */
    private function momento(string $dia, $hora): ?Carbon
    {
        try {
            return Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $dia . ' ' . substr((string) $hora, 0, 5) . ':00',
                self::FUSO_LOCAL
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Valor a gravar: no fuso da aplicação (UTC). */
    private function paraBd(Carbon $c): string
    {
        return $c->copy()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }
};
