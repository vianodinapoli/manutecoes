<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardDescargasController extends Controller
{
    // Estados de uma descarga (camião) ainda NÃO confirmada na balança
    // (os mesmos que o menu lateral usa para o contador)
    private const ESTADOS_POR_CONFIRMAR = ['pending', 'in_transit'];

    public function index()
    {
        // ── Operações (navios) ──
        $operacoes = DB::table('discharge_operations')
            ->orderByDesc('created_at')
            ->get();

        $opIds = $operacoes->pluck('id');

        // ── Tipos definidos por operação (quantidade esperada por tipo) ──
        $itens = DB::table('discharge_operation_items')
            ->whereIn('operation_id', $opIds)
            ->get();

        $esperadoItens = $itens->groupBy('operation_id')
            ->map(fn ($g) => (float) $g->sum('quantidade'));

        // ── Linhas descarregadas (camião × tipo) com os dados do camião ──
        $linhas = DB::table('discharge_items as di')
            ->join('descargas as d', 'd.id', '=', 'di.discharge_id')
            ->join('discharge_operation_items as oi', 'oi.id', '=', 'di.operation_item_id')
            ->whereIn('oi.operation_id', $opIds)
            ->get([
                'di.discharge_id', 'di.quantidade', 'di.quantidade_confirmada',
                'd.data', 'd.status', 'd.created_at',
                'oi.operation_id', 'oi.nome', 'oi.peso_unitario_kg',
            ])
            ->map(function ($l) {
                // Valor que conta: o confirmado na balança; se não existir, o registado
                $l->valor = (float) ($l->quantidade_confirmada ?? $l->quantidade);
                $l->data_ref = Carbon::parse($l->data ?? $l->created_at);
                return $l;
            });

        $porConfirmar = $linhas->filter(fn ($l) => in_array($l->status, self::ESTADOS_POR_CONFIRMAR, true));
        $confirmadas  = $linhas->reject(fn ($l) => in_array($l->status, self::ESTADOS_POR_CONFIRMAR, true));

        $recebidoOp = $confirmadas->groupBy('operation_id')
            ->map(fn ($g) => (float) $g->sum('valor'));

        $camioesOp = $confirmadas->groupBy('operation_id')
            ->map(fn ($g) => $g->pluck('discharge_id')->unique()->count());

        $transitoOp = $porConfirmar->groupBy('operation_id')
            ->map(fn ($g) => (float) $g->sum('quantidade'));

        $toneladasOp = $confirmadas->groupBy('operation_id')
            ->map(fn ($g) => $g->sum(fn ($l) => $l->valor * (float) $l->peso_unitario_kg) / 1000);

        // ── Enriquecer cada operação ──
        $operacoes = $operacoes->map(function ($op) use ($esperadoItens, $recebidoOp, $camioesOp, $transitoOp, $toneladasOp) {
            $esperado = (float) $op->quantidade_esperada;
            if ($esperado <= 0) {
                $esperado = (float) ($esperadoItens[$op->id] ?? 0);
            }

            $op->esperado   = $esperado;
            $op->recebido   = (float) ($recebidoOp[$op->id] ?? 0);
            $op->transito   = (float) ($transitoOp[$op->id] ?? 0);
            $op->camioes    = (int) ($camioesOp[$op->id] ?? 0);
            $op->toneladas  = (float) ($toneladasOp[$op->id] ?? 0);
            $op->pct        = $esperado > 0 ? (int) round($op->recebido / $esperado * 100) : 0;

            $arquivada = $op->archived_at !== null || $op->status === 'archived';
            if ($arquivada) {
                $op->badge = 'concluida';
                $op->estado_label = 'Arquivada';
            } elseif ($op->status === 'active') {
                $op->badge = 'ativa';
                $op->estado_label = 'Em curso';
            } else {
                $op->badge = 'outra';
                $op->estado_label = ucfirst(str_replace('_', ' ', (string) $op->status));
            }

            return $op;
        });

        $ativas   = $operacoes->where('badge', 'ativa')->values();
        $ativaIds = $ativas->pluck('id');

        // ── KPIs (operações em curso) ──
        $opsEmCurso      = $ativas->count();
        $esperadoAtivas  = (float) $ativas->sum('esperado');
        $recebidoAtivas  = (float) $ativas->sum('recebido');
        $emFalta         = max(0, $esperadoAtivas - $recebidoAtivas);
        $transitoAtivas  = (float) $ativas->sum('transito');
        $camioesAtivas   = (int) $ativas->sum('camioes');
        $toneladasAtivas = round($ativas->sum('toneladas'), 1);
        $opsArquivadas   = $operacoes->where('badge', 'concluida')->count();
        $opsOutras       = $operacoes->where('badge', 'outra')->count();

        // ── Gráfico 1: quantidade recebida por mês (12 meses) ──
        $inicio = Carbon::now()->startOfMonth()->subMonths(11);

        $porMes = $confirmadas
            ->filter(fn ($l) => $l->data_ref->gte($inicio))
            ->groupBy(fn ($l) => $l->data_ref->format('Y-m'))
            ->map(fn ($g) => (float) $g->sum('valor'));

        $meses = [];
        $contagemMensal = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $inicio->copy()->addMonths($i);
            $meses[]          = $m->locale('pt')->translatedFormat('M/y');
            $contagemMensal[] = round($porMes[$m->format('Y-m')] ?? 0);
        }

        // ── Gráfico 2: recebido por tipo (operações em curso) ──
        $porTipo = $confirmadas
            ->filter(fn ($l) => $ativaIds->contains($l->operation_id))
            ->groupBy('nome')
            ->map(fn ($g, $nome) => ['nome' => $nome, 'total' => round((float) $g->sum('valor'))])
            ->sortByDesc('total')
            ->values();

        // ── Gráfico 3: esperado vs recebido por operação em curso ──
        $topAtivas   = $ativas->take(8);
        $labelsOps   = $topAtivas->pluck('navio')->values();
        $esperadoOps = $topAtivas->pluck('esperado')->map(fn ($v) => round($v))->values();
        $recebidoOps = $topAtivas->pluck('recebido')->map(fn ($v) => round($v))->values();

        return view('dashboard.descargas', compact(
            'opsEmCurso', 'esperadoAtivas', 'recebidoAtivas', 'emFalta', 'transitoAtivas',
            'camioesAtivas', 'toneladasAtivas', 'opsArquivadas', 'opsOutras',
            'meses', 'contagemMensal', 'porTipo',
            'labelsOps', 'esperadoOps', 'recebidoOps', 'operacoes'
        ));
    }
}