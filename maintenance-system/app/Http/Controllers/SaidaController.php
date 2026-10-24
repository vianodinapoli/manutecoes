<?php

namespace App\Http\Controllers;

use App\Models\DischargeOperation;
use App\Models\Saida;
use App\Models\SaidaCarga;
use App\Services\SaidaExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use App\Models\SaidaCargaAtrelado;

/**
 * Saídas de carga: cada saída tem cliente, data, operação de origem (navio)
 * e a lista de camiões registados. Só os camiões registados podem carregar.
 * O registo/carregamento/devolução de camiões está em SaidaCargaController.
 */
class SaidaController extends Controller
{
    private function fmt(): \Closure
    {
        return fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    }

    private function excel(): SaidaExcel
    {
        return app(SaidaExcel::class);
    }

    /* ───────────────────────── Lista ───────────────────────── */
    public function index(Request $request)
    {
        $f = $request->only(['q', 'status', 'operation_id', 'de', 'ate']);

        $saidas = Saida::filtrar($f)
            ->with('operation')
            ->withCount([
                'camioes',
                'camioes as carregados_count' => fn ($q) => $q->where('status', 'carregada'),
                'camioes as pendentes_count'  => fn ($q) => $q->where('status', 'pendente'),
            ])
            ->orderByDesc('data')->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // KPIs e resumo sobre TODOS os resultados filtrados (não só a página)
        $ids     = Saida::filtrar($f)->pluck('id');
        $camBase = SaidaCarga::whereIn('saida_id', $ids);

        $kpi = [
            'saidas'     => $ids->count(),
            'abertas'    => Saida::filtrar($f)->where('status', 'aberta')->count(),
            'camioes'    => (clone $camBase)->count(),
            'carregados' => (clone $camBase)->where('status', 'carregada')->count(),
            'pendentes'  => (clone $camBase)->where('status', 'pendente')->count(),
        ];

        $itemMap = $this->itemMap($f);

        // total carregado por produto, em todos os resultados filtrados
        $resumoProdutos = SaidaCarga::whereIn('saida_id', $ids)
            ->where('status', 'carregada')
            ->whereNotNull('operation_item_id')
            ->selectRaw('operation_item_id, SUM(quantidade) as total')
            ->groupBy('operation_item_id')->get()
            ->map(function ($r) use ($itemMap) {
                $it = $itemMap[$r->operation_item_id] ?? null;
                return $it ? ['nome' => $it->nome, 'label' => $it->unidade_label, 'total' => (float) $r->total] : null;
            })
            ->filter()
            ->groupBy(fn ($x) => $x['nome'] . '|' . $x['label'])
            ->map(fn ($g) => [
                'nome'  => $g->first()['nome'],
                'label' => $g->first()['label'],
                'total' => $g->sum('total'),
            ])
            ->values();

        // carregado por saída e produto (só para as saídas desta página)
        $porSaida = SaidaCarga::whereIn('saida_id', $saidas->pluck('id'))
            ->where('status', 'carregada')
            ->selectRaw('saida_id, operation_item_id, SUM(quantidade) as total')
            ->groupBy('saida_id', 'operation_item_id')
            ->get()->groupBy('saida_id');

        return view('saidas.index', [
            'saidas'            => $saidas,
            'f'                 => $f,
            'kpi'               => $kpi,
            'resumoProdutos'    => $resumoProdutos,
            'porSaida'          => $porSaida,
            'itemMap'           => $itemMap,
            'operations'        => DischargeOperation::orderByDesc('id')->get(),
            'operationsAbertas' => $this->operacoesAbertas(),
            'fmt'               => $this->fmt(),
            'stCor'             => ['aberta' => '#198754', 'fechada' => '#64748b'],
            'stTxt'             => ['aberta' => 'Aberta',  'fechada' => 'Fechada'],
        ]);
    }

    /* ───────────────────────── Criar ───────────────────────── */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente'      => 'required|string|max:120',
            'data'         => 'required|date',
            'operation_id' => 'required|integer|exists:discharge_operations,id',
            'observacoes'  => 'nullable|string|max:1000',
        ]);

        $saida = DB::transaction(fn () => Saida::create([
            'codigo'       => Saida::gerarCodigo(),
            'cliente'      => trim($dados['cliente']),
            'data'         => $dados['data'],
            'operation_id' => $dados['operation_id'],
            'status'       => 'aberta',
            'observacoes'  => $dados['observacoes'] ?? null,
            'criado_por'   => auth()->id(),
        ]));

        return redirect()->route('saidas.show', $saida)
            ->with('success', "✅ Saída {$saida->codigo} criada. Registe os camiões ou carregue o Excel.");
    }

    public function show(Request $request, Saida $saida)
{
    $q      = trim((string) $request->query('q', ''));
    $estado = (string) $request->query('estado', '');

    $op = $saida->operation()->with('items')->first();

    // todos os camiões (a verificação de matrícula usa a lista completa)
    $todos = $saida->camioes()->with('carregadoPor')
        ->orderByRaw("CASE WHEN status = 'carregada' THEN 1 ELSE 0 END")
        ->orderBy('id')
        ->get();

    $atrelados = SaidaCargaAtrelado::whereIn('saida_carga_id', $todos->pluck('id'))
        ->orderBy('id')->get()->groupBy('saida_carga_id');

    $camioes = $todos
        ->when($estado !== '', fn ($c) => $c->where('status', $estado))
        ->when($q !== '', function ($c) use ($q, $atrelados) {
            $t = mb_strtolower($q);
            return $c->filter(fn ($x) => str_contains(mb_strtolower(
                $x->matricula . ' ' . $x->nome . ' ' . $x->apelido . ' ' . $x->empresa . ' '
                . ($atrelados[$x->id] ?? collect())->pluck('matricula')->implode(' ')
            ), $t));
        })
        ->values();

    $kpi = [
        'registados' => $todos->count(),
        'carregados' => $todos->where('status', 'carregada')->count(),
        'em_carga'   => $todos->where('status', 'em_carga')->count(),
        'pendentes'  => $todos->where('status', 'pendente')->count(),
    ];

    $itens = $op ? $op->items->keyBy('id') : collect();

    // carregado nesta saída, por produto
    $nestaSaida = $todos->where('status', 'carregada')
        ->groupBy('operation_item_id')
        ->map(fn ($g) => (float) $g->sum('quantidade'));

    // aviso de diferença entre sacos e peso líquido
    $avisos = [];
    foreach ($todos->where('status', 'carregada') as $c) {
        $it = $itens[$c->operation_item_id] ?? null;
        if ($it && ($it->unidade ?? null) === 'saco' && $c->peso_liquido !== null) {
            $a = SaidaPesagemController::avisoPeso((float) $c->quantidade, (float) $c->peso_liquido);
            if ($a) {
                $avisos[$c->id] = $a;
            }
        }
    }

    // stock da operação: stock disponível, saído (todas as saídas) e recebido
    $stock   = collect();
    $itensJs = [];
    if ($op) {
        $saiuPorItem = SaidaCarga::where('operation_id', $op->id)
            ->where('status', 'carregada')
            ->selectRaw('operation_item_id, SUM(quantidade) as total')
            ->groupBy('operation_item_id')->pluck('total', 'operation_item_id');

        foreach ($op->items as $item) {
            $disp = (float) SaidaCarga::disponivelOperacao($op, $item->id);
            $saiu = (float) ($saiuPorItem[$item->id] ?? 0);

            $stock->push((object) [
                'item'     => $item,
                'recebido' => $disp + $saiu,
                'saiu'     => $saiu,
                'stock'    => $disp,
            ]);

            $itensJs[] = [
                'id'         => $item->id,
                'nome'       => $item->nome,
                'label'      => $item->unidade_label,
                'unidade'    => $item->unidade ?? null,
                'disponivel' => $disp,
            ];
        }
    }

    $camioesJs = $todos->mapWithKeys(fn ($c) => [$c->id => [
        'id'           => $c->id,
        'matricula'    => $c->matricula,
        'nome'         => $c->nome_completo,
        'status'       => $c->status,
        'atrelado'     => $c->tipo_atrelado,
        'pedido'       => (float) ($c->quantidade_pedida ?? $c->quantidade),
        'item'         => $c->operation_item_id,
        'peso_vazio'   => $c->peso_vazio !== null ? (float) $c->peso_vazio : null,
        'carregado_em' => $c->carregado_em?->format('d/m/Y H:i'),
    ]]);

    return view('saidas.show', [
        'saida'             => $saida,
        'op'                => $op,
        'camioes'           => $camioes,
        'atrelados'         => $atrelados,
        'avisos'            => $avisos,
        'kpi'               => $kpi,
        'itens'             => $itens,
        'stock'             => $stock,
        'nestaSaida'        => $nestaSaida,
        'itensJs'           => $itensJs,
        'camioesJs'         => $camioesJs,
        'q'                 => $q,
        'estado'            => $estado,
        'operationsAbertas' => $this->operacoesAbertas($saida->operation_id),
    ]);
}

    /* ───────────────────────── Editar ───────────────────────── */
    public function update(Request $request, Saida $saida)
    {
        abort_if($saida->isFechada(), 403, 'Saída fechada.');

        $dados = $request->validate([
            'cliente'      => 'required|string|max:120',
            'data'         => 'required|date',
            'operation_id' => 'nullable|integer|exists:discharge_operations,id',
            'observacoes'  => 'nullable|string|max:1000',
        ]);

        $temCarregados = $saida->camioes()->where('status', 'carregada')->exists();

        $saida->update([
            'cliente'      => trim($dados['cliente']),
            'data'         => $dados['data'],
            // com camiões já carregados a operação de origem não muda
            'operation_id' => $temCarregados ? $saida->operation_id : ($dados['operation_id'] ?? null),
            'observacoes'  => $dados['observacoes'] ?? null,
        ]);

        // mantém a data dos camiões ainda por carregar alinhada com a saída
        $saida->camioes()->where('status', 'pendente')->update(['data' => $saida->data]);

        return redirect()->route('saidas.show', $saida)->with('success', '💾 Saída atualizada.');
    }

    /* ───────────────────────── Fechar / reabrir ───────────────────────── */
    public function fechar(Saida $saida)
    {
        $saida->update(['status' => 'fechada', 'fechada_em' => now()]);

        return redirect()->route('saidas.show', $saida)->with('success', '🔒 Saída fechada.');
    }

    public function reabrir(Saida $saida)
    {
        $saida->update(['status' => 'aberta', 'fechada_em' => null]);

        return redirect()->route('saidas.show', $saida)->with('success', '🔓 Saída reaberta.');
    }

    /* ───────────────────────── Apagar ───────────────────────── */
    public function destroy(Saida $saida)
    {
        if ($saida->camioes()->where('status', 'carregada')->exists()) {
            return redirect()->route('saidas.show', $saida)
                ->with('error', 'Não é possível apagar: já há camiões carregados. Devolva-os primeiro.');
        }

        DB::transaction(function () use ($saida) {
            $saida->camioes()->delete();
            $saida->delete();
        });

        return redirect()->route('saidas.index')->with('success', '🗑️ Saída apagada.');
    }

    /* ───────────────────────── Relatório ───────────────────────── */
    public function relatorio(Request $request, Saida $saida)
    {
        $saida->load('operation.items');

        $camioes = $saida->camioes()->with('carregadoPor')->orderBy('id')->get();
        $itens   = $saida->operation ? $saida->operation->items->keyBy('id') : collect();

        $resumo = $camioes->whereNotNull('operation_item_id')
            ->groupBy('operation_item_id')
            ->map(function ($g, $itemId) use ($itens) {
                $it = $itens[$itemId] ?? null;
                return [
                    'nome'      => $it->nome ?? '—',
                    'label'     => $it->unidade_label ?? '',
                    'pedido'    => (float) $g->sum(fn ($c) => $c->quantidade_pedida ?? $c->quantidade),
                    'carregado' => (float) $g->where('status', 'carregada')->sum('quantidade'),
                ];
            })
            ->values();

        if ($request->query('formato') === 'xlsx') {
            $excel = $this->excel();
            $ss    = $excel->relatorioSaida($saida, $camioes, $itens, $resumo);

            return $excel->download($ss, "relatorio-{$saida->codigo}.xlsx");
        }

        return view('saidas.relatorio', compact('saida', 'camioes', 'itens', 'resumo'));
    }

    /* ───────────────────────── Excel ───────────────────────── */

    /** Modelo Excel para preencher os camiões (com ou sem saída). */
    public function modelo(?Saida $saida = null)
    {
        $excel = $this->excel();
        $ss    = $excel->modelo($saida);

        return $excel->download($ss, $saida ? "modelo-{$saida->codigo}.xlsx" : 'modelo-saidas.xlsx');
    }

    /** Exporta a lista de saídas com os filtros atuais. */
    public function exportar(Request $request)
    {
        $f = $request->only(['q', 'status', 'operation_id', 'de', 'ate']);

        $saidas = Saida::filtrar($f)
            ->with('operation')
            ->withCount([
                'camioes',
                'camioes as carregados_count' => fn ($q) => $q->where('status', 'carregada'),
                'camioes as pendentes_count'  => fn ($q) => $q->where('status', 'pendente'),
            ])
            ->orderByDesc('data')->orderByDesc('id')
            ->get();

        $porSaidaItem = SaidaCarga::whereIn('saida_id', $saidas->pluck('id'))
            ->where('status', 'carregada')
            ->whereNotNull('operation_item_id')
            ->selectRaw('saida_id, operation_item_id, SUM(quantidade) as total')
            ->groupBy('saida_id', 'operation_item_id')
            ->get();

        $excel = $this->excel();
        $ss    = $excel->listaSaidas($saidas, $porSaidaItem, $this->itemMap($f));

        return $excel->download($ss, 'saidas-' . now()->format('Y-m-d') . '.xlsx');
    }

    /** Upload do Excel: linhas com erro são ignoradas e listadas, as restantes entram. */
    public function importar(Request $request, Saida $saida)
    {
        abort_if($saida->isFechada(), 403, 'Saída fechada.');

        $request->validate(['ficheiro' => 'required|file|mimes:xlsx,xls|max:5120']);

        try {
            $res = $this->excel()->ler($request->file('ficheiro'), $saida);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('saidas.show', $saida)->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('saidas.show', $saida)
                ->with('error', 'Não foi possível ler o ficheiro. Use o modelo Excel.');
        }

        DB::transaction(function () use ($res, $saida) {
            foreach ($res['linhas'] as $l) {
                SaidaCarga::create([
                    'saida_id'          => $saida->id,
                    'data'              => $saida->data,
                    'empresa'           => $l['empresa'],
                    'nome'              => $l['nome'],
                    'apelido'           => $l['apelido'],
                    'matricula'         => $l['matricula'],
                    'tipo_atrelado'     => $l['tipo_atrelado'],
                    'quantidade'        => $l['quantidade'],
                    'quantidade_pedida' => $l['quantidade'],
                    'operation_item_id' => $l['operation_item_id'],
                    'metadata'          => $l['metadata'] ?: null,
                    'status'            => 'pendente',
                    'registado_por'     => auth()->id(),
                ]);
            }
        });

        $criados  = count($res['linhas']);
        $redirect = redirect()->route('saidas.show', $saida)
            ->with('success', "📥 {$criados} camião(ões) registado(s).");

        if (! empty($res['erros'])) {
            $redirect->with('import_erros', $res['erros']);
        }

        return $redirect;
    }

    /* ───────────────────────── Auxiliares ───────────────────────── */

    /** Produtos (itens) das operações envolvidas nas saídas filtradas, por id. */
    private function itemMap(array $f)
    {
        $opIds = Saida::filtrar($f)->whereNotNull('operation_id')->pluck('operation_id')->unique();

        return DischargeOperation::with('items')->whereIn('id', $opIds)->get()
            ->flatMap(fn ($o) => $o->items)->keyBy('id');
    }

    /** Operações onde ainda se pode criar saída (não arquivadas); inclui a atual da saída. */
    private function operacoesAbertas(?int $incluir = null)
    {
        return DischargeOperation::orderByDesc('id')->get()
            ->filter(fn ($o) => ! $o->isArchived() || $o->id === $incluir)
            ->values();
    }
}