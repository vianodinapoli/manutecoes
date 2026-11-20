<?php

namespace App\Http\Controllers;

use App\Models\DischargeOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperationReportController extends Controller
{
    /** Horas em trânsito (desde a saída do porto) a partir das quais o camião conta como atrasado. */
    private const HORAS_ATRASO = 4;

    public function confirmed(Request $request, DischargeOperation $operation)
    {
        $data = $request->validate([
            'de'  => ['required', 'date'],
            'ate' => ['required', 'date', 'after_or_equal:de'],
        ]);

        // A hora escolhida é hora de Maputo; converte para o fuso da aplicação
        // (onde os datetimes estão guardados).
        $appTz = config('app.timezone');
        $de  = Carbon::parse($data['de'], 'Africa/Maputo')->setTimezone($appTz);
        $ate = Carbon::parse($data['ate'], 'Africa/Maputo')->setTimezone($appTz);

        // Confirmado = pesagem completa. A hora de confirmação é a mais tardia
        // entre peso_bruto_em e tara_em, e tem de cair dentro do intervalo.
        $q = $operation->discharges()
            ->where('status', 'confirmed')
            ->whereNotNull('peso_bruto_em')
            ->whereNotNull('tara_em')
            ->where('peso_bruto_em', '<=', $ate)
            ->where('tara_em', '<=', $ate)
            ->where(function ($w) use ($de) {
                $w->where('peso_bruto_em', '>=', $de)
                  ->orWhere('tara_em', '>=', $de);
            });

        $tot = (clone $q)->selectRaw(
            'COUNT(*) as camioes,
             COALESCE(SUM(COALESCE(sacos_confirmados, total_sacos)), 0) as sacos,
             COALESCE(SUM(peso_liquido), 0) as peso_kg'
        )->first();

        // Estado ACTUAL dos camiões da operação (não depende do intervalo).
        $porEstado = $operation->discharges()
            ->selectRaw('status, COUNT(*) as n')
            ->whereIn('status', ['loaded', 'in_transit', 'weighing'])
            ->groupBy('status')
            ->pluck('n', 'status');

        $emTransito = $operation->discharges()->where('status', 'in_transit')->get();

        // Atrasados: em trânsito há mais de HORAS_ATRASO horas.
        $limiteAtraso = now()->subHours(self::HORAS_ATRASO);
        $atrasados = $emTransito->filter(
            fn ($d) => $d->saiu_porto_em && Carbon::parse($d->saiu_porto_em)->lt($limiteAtraso)
        )->count();

        // Camiões confirmados no intervalo (carregados uma só vez para
        // divergências e produtos).
        $confirmados = (clone $q)->with('items')->get();

        // Divergências: camiões confirmados no intervalo com divergência na balança.
        $divergencias = $confirmados
            ->filter(fn ($d) => (bool) data_get($d, 'divergencia'))
            ->count();

                // Tipo de carga: produtos definidos no plano da operação, com a quantidade
        // confirmada de cada um nos camiões do intervalo.
        $linhas = $confirmados->flatMap(fn ($d) => $d->items);

        $produtos = $operation->items()->get()->map(function ($p) use ($linhas) {
            $qtd = $linhas
                ->filter(function ($i) use ($p) {
                    // Liga a linha do camião ao produto do plano (tenta os nomes de coluna mais comuns).
                    $ref = data_get($i, 'operation_item_id')
                        ?? data_get($i, 'discharge_operation_item_id')
                        ?? data_get($i, 'item_id');

                    return $ref !== null
                        ? (int) $ref === (int) $p->id
                        : data_get($i, 'nome') === $p->nome;
                })
                ->sum(fn ($i) => (float) (data_get($i, 'quantidade_confirmada') ?? data_get($i, 'quantidade') ?? 0));

            return [
                'nome'          => $p->nome,
                'unidade_label' => $p->unidade_label
                    ?? (($p->unidade ?? null) === 'tonelada' ? 'toneladas' : 'sacos'),
                'quantidade'    => round($qtd, 3),
            ];
        })->values();

        return [
            'camioes'   => (int) $tot->camioes,
            'sacos'     => (int) $tot->sacos,
            'toneladas' => round(((float) $tot->peso_kg) / 1000, 3),

            'carregados'  => (int) ($porEstado['loaded'] ?? 0),
            'em_transito' => (int) ($porEstado['in_transit'] ?? 0),
            'a_pesar'     => (int) ($porEstado['weighing'] ?? 0),

            'esperada_ton'    => $operation->esperado_ton ?? null,
            'em_transito_ton' => round(((float) $emTransito->sum('peso_total_estimado')) / 1000, 3),

            'atrasados'    => $atrasados,
            'divergencias' => $divergencias,

            'produtos' => $produtos,
        ];
    }
}