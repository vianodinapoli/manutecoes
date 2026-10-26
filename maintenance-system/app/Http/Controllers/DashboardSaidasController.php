<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardSaidasController extends Controller
{
    // Diferença aceitável (%) entre a quantidade de sacos e o peso líquido
    private const TOLERANCIA_PCT = 2;

    public function index()
    {
        // ── Listas de saída ──
        $saidas    = DB::table('saidas')->get(['id', 'codigo', 'cliente', 'status', 'operation_id', 'data']);
        $saidaInfo = $saidas->keyBy('id');

        // Navio por operação e peso unitário (kg) por tipo
        $navios   = DB::table('discharge_operations')->pluck('navio', 'id');
        $pesoItem = DB::table('discharge_operation_items')->pluck('peso_unitario_kg', 'id');

        // ── Camiões das saídas ──
        // Carregado = tem carregado_em, ou status 'carregado'
        $camioes = DB::table('saidas_carga')->get()->map(function ($c) use ($saidaInfo, $navios, $pesoItem) {
            $saida = $c->saida_id ? ($saidaInfo[$c->saida_id] ?? null) : null;

            $c->feito = $c->carregado_em !== null || $c->status === 'carregado';
            $c->data  = Carbon::parse($c->carregado_em ?? $c->peso_cheio_em ?? $c->data ?? $c->created_at);

            $opId       = $c->operation_id ?? ($saida->operation_id ?? null);
            $c->navio   = $opId ? ($navios[$opId] ?? 'Sem navio') : 'Sem navio';
            $c->lista   = $saida->codigo ?? '—';
            $c->cliente = $saida->cliente ?? $c->empresa;
            $c->motorista = trim(($c->nome ?? '') . ' ' . ($c->apelido ?? ''));
            $c->qtd     = (float) $c->quantidade;

            // Peso líquido: o gravado, ou cheio − vazio
            $liquido = $c->peso_liquido !== null ? (float) $c->peso_liquido : null;
            if ($liquido === null && $c->peso_cheio !== null && $c->peso_vazio !== null) {
                $liquido = (float) $c->peso_cheio - (float) $c->peso_vazio;
            }
            $c->liquido = $liquido;

            // Diferença entre sacos e peso líquido (só se houver peso unitário)
            $pesoUn     = (float) ($pesoItem[$c->operation_item_id] ?? 0);
            $esperadoKg = $c->qtd * $pesoUn;
            if ($liquido !== null && $esperadoKg > 0) {
                $diff         = ($liquido - $esperadoKg) / $esperadoKg * 100;
                $c->diff_pct  = round($diff, 1);
                $c->alerta    = abs($diff) > self::TOLERANCIA_PCT;
            } else {
                $c->diff_pct  = null;
                $c->alerta    = false;
            }

            return $c;
        });

        $carregados = $camioes->where('feito', true)->values();
        $inicioMes  = Carbon::now()->startOfMonth();
        $doMes      = $carregados->filter(fn ($c) => $c->data->gte($inicioMes));

        // ── KPIs ──
        $abertasIds    = $saidas->where('status', 'aberta')->pluck('id');
        $saidasAbertas = $abertasIds->count();
        $porCarregar   = $camioes->where('feito', false)->filter(fn ($c) => $abertasIds->contains($c->saida_id))->count();
        $camioesHoje   = $carregados->filter(fn ($c) => $c->data->isToday())->count();
        $qtdMes        = (int) round($doMes->sum('qtd'));
        $pesoMesTon    = round($doMes->sum(fn ($c) => $c->liquido ?? 0) / 1000, 1);
        $alertasMes    = $doMes->where('alerta', true)->count();

        // ── Gráfico 1: quantidade saída por mês (12 meses) ──
        $inicio = Carbon::now()->startOfMonth()->subMonths(11);
        $porMes = $carregados
            ->filter(fn ($c) => $c->data->gte($inicio))
            ->groupBy(fn ($c) => $c->data->format('Y-m'))
            ->map(fn ($g) => (float) $g->sum('qtd'));

        $meses = [];
        $qtdMensal = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $inicio->copy()->addMonths($i);
            $meses[]     = $m->locale('pt')->translatedFormat('M/y');
            $qtdMensal[] = round($porMes[$m->format('Y-m')] ?? 0);
        }

        // ── Gráfico 2: saídas por navio ──
        $porNavio = $carregados
            ->groupBy('navio')
            ->map(fn ($g, $navio) => ['nome' => $navio, 'total' => (int) round($g->sum('qtd'))])
            ->sortByDesc('total')
            ->values();

        // ── Gráfico 3: camiões carregados por dia (últimos 14 dias) ──
        $dias = [];
        $camioesDia = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dias[]       = $d->format('d/m');
            $camioesDia[] = $carregados->filter(fn ($c) => $c->data->isSameDay($d))->count();
        }

        // ── Tabela: últimos camiões carregados ──
        $ultimas = $carregados->sortByDesc('data')->take(300)->values();

        return view('dashboard.saidas', compact(
            'saidasAbertas', 'porCarregar', 'camioesHoje', 'qtdMes', 'pesoMesTon', 'alertasMes',
            'meses', 'qtdMensal', 'porNavio', 'dias', 'camioesDia', 'ultimas'
        ))->with('tolerancia', self::TOLERANCIA_PCT);
    }
}