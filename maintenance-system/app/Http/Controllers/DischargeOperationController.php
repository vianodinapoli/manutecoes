<?php

namespace App\Http\Controllers;

use App\Models\DischargeOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DischargeOperationController extends Controller
{
    /** Operações abertas (planeadas + em curso) */
    public function index()
    {
        $operations = DischargeOperation::open()
            ->with('items')
            ->withCount([
                'discharges',
                'discharges as pendentes_count'   => fn ($q) => $q->whereIn('status', ['pending', 'in_transit']),
                'discharges as confirmadas_count' => fn ($q) => $q->where('status', 'confirmed'),
            ])
            ->withSum('discharges', 'peso_liquido')
            ->orderByRaw("CASE status WHEN 'active' THEN 0 ELSE 1 END")
            ->latest()
            ->get();

        return view('operations.index', compact('operations'));
    }

    /** Arquivo */
    public function archive()
    {
        $operations = DischargeOperation::archived()
            ->withCount('discharges')
            ->withSum('discharges', 'peso_liquido')
            ->with('archivedBy')
            ->orderByDesc('archived_at')
            ->get();

        return view('operations.archive', compact('operations'));
    }

    public function store(Request $request)
    {
        $data  = $this->validated($request, editPlan: true, needMotivo: false);
        $items = $data['items'];
        unset($data['items']);

        $op = DB::transaction(function () use ($data, $items) {
            $op = DischargeOperation::create($data + ['status' => 'planned', 'quantidade_esperada' => 0]);
            $this->syncItems($op, $items);
            return $op;
        });

        return redirect()->route('operations.show', $op)
            ->with('success', "Operação do navio {$op->navio} planeada. Reveja o plano e clique em “Iniciar descarga”.");
    }

    /**
     * Planeada  → ecrã do plano (ainda sem camiões)
     * Em curso / Arquivada → lista de camiões da operação
     */
    public function show(DischargeOperation $operation)
    {
        $operation->load('items');

        if ($operation->isPlanned()) {
            return view('operations.plan', compact('operation'));
        }

        $operation->loadSum('discharges', 'peso_liquido');

        $discharges = $operation->discharges()
            ->with(['items.operationItem', 'registeredBy', 'confirmedBy'])
            ->orderByDesc('id')
            ->get();

        // Plano vs realizado, por produto
        $planItems = $operation->items()
            ->withSum('dischargeItems as registado', 'quantidade')
            ->withSum('dischargeItems as confirmado', 'quantidade_confirmada')
            ->get();

        return view('discharges.index', compact('operation', 'discharges', 'planItems'));
    }

    /** Relatório completo da operação (página própria, pronta a imprimir / guardar em PDF). */
    public function report(DischargeOperation $operation)
    {
        if ($operation->isPlanned()) {
            return redirect()->route('operations.show', $operation)
                ->withErrors(['archive' => 'A operação ainda não foi iniciada: não há dados para o relatório.']);
        }

        $operation->load(['items', 'startedBy', 'archivedBy']);

        $discharges = $operation->discharges()
            ->with(['items.operationItem', 'registeredBy', 'confirmedBy'])
            ->orderBy('data')->orderBy('id')
            ->get();

        $kgUn        = fn ($it) => $it->unidade === 'tonelada' ? 1000 : (float) $it->peso_unitario_kg;
        $confirmadas = $discharges->where('status', 'confirmed');
        $pendentes   = $discharges->whereIn('status', ['pending', 'in_transit']);

        // ── 1. Resumo ──
        $portoKg    = (float) $discharges->sum('peso_total_estimado');
        $balancaKg  = (float) $confirmadas->sum('peso_liquido');
        $estConfKg  = (float) $confirmadas->sum('peso_total_estimado');
        $esperadoKg = (float) $operation->quantidade_esperada * 1000;
        $tempo      = $discharges->whereNotNull('tempo_transporte')->avg('tempo_transporte');

        $resumo = [
            'esperado_ton'  => (float) $operation->quantidade_esperada,
            'porto_ton'     => round($portoKg / 1000, 3),
            'balanca_ton'   => round($balancaKg / 1000, 3),
            'falta_ton'     => round(($esperadoKg - $balancaKg) / 1000, 3),
            'lancado_pct'   => $esperadoKg > 0 ? min(100, round($portoKg / $esperadoKg * 100, 1)) : 0,
            'progresso'     => $esperadoKg > 0 ? min(100, round($balancaKg / $esperadoKg * 100, 1)) : 0,
            'camioes'       => $discharges->count(),
            'confirmados'   => $confirmadas->count(),
            'por_confirmar' => $pendentes->count(),
            'divergencias'  => $discharges->filter(fn ($d) => $d->hasDivergence())->count(),
            'tempo_medio'   => $tempo ? (int) round($tempo) : null,
            'dif_peso_kg'   => round($balancaKg - $estConfKg, 1),   // pesado vs estimado (só confirmados)
        ];

        // ── 2. Plano vs realizado por produto ──
        $porProduto = $operation->items->map(function ($it) use ($discharges, $kgUn) {
            $reg = $regConf = $conf = 0.0;

            foreach ($discharges as $d) {
                foreach ($d->items as $l) {
                    if ($l->operation_item_id != $it->id) {
                        continue;
                    }
                    $reg += (float) $l->quantidade;
                    if ($d->status === 'confirmed') {
                        $regConf += (float) $l->quantidade;
                        $conf    += (float) $l->quantidade_confirmada;
                    }
                }
            }

            $kg = $kgUn($it);

            return [
                'item'          => $it,
                'plano'         => (float) $it->quantidade,
                'registado'     => $reg,
                'confirmado'    => $conf,
                'falta'         => round((float) $it->quantidade - $reg, 3),
                'dif'           => round($conf - $regConf, 3),      // balança vs porto (confirmados)
                'plano_kg'      => (float) $it->quantidade * $kg,
                'porto_kg'      => $reg * $kg,
                'confirmado_kg' => $conf * $kg,
            ];
        });

        // ── 3. Por transportadora ──
        $extras = $operation->transportadorasExtra();

        $porTransp = $discharges
            ->groupBy(fn ($d) => trim($d->transportadora))
            ->map(function ($g, $nome) use ($extras) {
                $t = $g->whereNotNull('tempo_transporte')->avg('tempo_transporte');
                return [
                    'nome'         => $nome,
                    'extra'        => in_array(mb_strtolower($nome), $extras, true),
                    'camioes'      => $g->count(),
                    'confirmados'  => $g->where('status', 'confirmed')->count(),
                    'sacos'        => (int) $g->sum('total_sacos'),
                    'estimado_kg'  => (float) $g->sum('peso_total_estimado'),
                    'liquido_kg'   => (float) $g->where('status', 'confirmed')->sum('peso_liquido'),
                    'tempo_medio'  => $t ? (int) round($t) : null,
                    'divergencias' => $g->filter(fn ($d) => $d->hasDivergence())->count(),
                ];
            })
            ->sortByDesc('camioes')
            ->values();

        // transportadoras previstas que não tiveram camiões
        $usadas = $porTransp->pluck('nome')->map(fn ($n) => mb_strtolower($n))->all();
        $semCamioes = collect($operation->transportadoras ?? [])
            ->reject(fn ($t) => in_array(mb_strtolower($t['nome']), $usadas, true))
            ->pluck('nome');

        // ── 4. Histórico da operação ──
        $historico = collect([['quando' => $operation->created_at, 'texto' => 'Operação criada (planeada).']]);

        if ($operation->started_at) {
            $historico->push([
                'quando' => $operation->started_at,
                'texto'  => 'Descarga iniciada' . ($operation->startedBy ? ' por ' . $operation->startedBy->name : '') . '. Plano bloqueado.',
            ]);
        }
        foreach (($operation->plan_log ?? []) as $log) {
            $historico->push([
                'quando' => Carbon::parse($log['em']),
                'texto'  => 'Plano alterado por ' . ($log['user'] ?? '—') . '. Motivo: ' . ($log['motivo'] ?? '—'),
            ]);
        }
        if ($operation->archived_at) {
            $historico->push([
                'quando' => $operation->archived_at,
                'texto'  => 'Operação arquivada' . ($operation->archivedBy ? ' por ' . $operation->archivedBy->name : '') . '.',
            ]);
        }
        $historico = $historico->sortBy('quando')->values();

        return view('operations.report', compact(
            'operation', 'discharges', 'resumo', 'porProduto', 'porTransp', 'semCamioes', 'historico'
        ));
    }

    public function update(Request $request, DischargeOperation $operation)
    {
        abort_if($operation->isArchived(), 403, 'Operação arquivada.');

        $isSuper  = auth()->user()->hasRole('super-admin');
        // Plano editável: livremente se Planeada; só super-admin (com motivo) se Em curso
        $editPlan = $operation->isPlanned() || ($operation->isActive() && $isSuper);
        $motivo   = $operation->isActive();

        $data   = $this->validated($request, editPlan: $editPlan, needMotivo: $motivo);
        $items  = $data['items'] ?? null;
        $razao  = $data['motivo_alteracao'] ?? null;
        unset($data['items'], $data['motivo_alteracao']);

        DB::transaction(function () use ($operation, $data, $items, $editPlan, $motivo, $razao) {
            $operation->update($data);

            if ($editPlan && $items !== null) {
                if ($motivo) {
                    $log   = $operation->plan_log ?? [];
                    $log[] = [
                        'user'   => auth()->user()->name,
                        'em'     => now()->format('Y-m-d H:i'),
                        'motivo' => $razao,
                    ];
                    $operation->update(['plan_log' => $log]);
                }
                $this->syncItems($operation, $items);
            }
        });

        return redirect()->route('operations.show', $operation)
            ->with('success', 'Operação actualizada.');
    }

    /** Planeada → Em curso (bloqueia o plano) */
    public function start(DischargeOperation $operation)
    {
        abort_unless($operation->isPlanned(), 403, 'Só operações planeadas podem ser iniciadas.');

        if (! $operation->items()->where('quantidade', '>', 0)->exists()) {
            return back()->withErrors(['archive' => 'Defina pelo menos um produto com quantidade antes de iniciar.']);
        }

        $operation->update([
            'status'     => 'active',
            'started_at' => now(),
            'started_by' => auth()->id(),
        ]);

        return redirect()->route('operations.show', $operation)
            ->with('success', 'Descarga iniciada. O plano ficou bloqueado.');
    }

    public function archiveOperation(DischargeOperation $operation)
    {
        if (! $operation->isActive()) {
            return back()->withErrors(['archive' => 'Só operações em curso podem ser arquivadas.']);
        }

        $pendentes = $operation->discharges()->whereIn('status', ['pending', 'in_transit'])->count();

        if ($pendentes > 0) {
            return back()->withErrors([
                'archive' => "Existem {$pendentes} camião(ões) por confirmar na balança. Confirme-os antes de arquivar.",
            ]);
        }

        $operation->update([
            'status'      => 'archived',
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        return redirect()->route('operations.index')
            ->with('success', "Operação {$operation->navio} enviada para o arquivo.");
    }

    public function restore(DischargeOperation $operation)
    {
        $operation->update([
            'status'      => 'active',
            'archived_at' => null,
            'archived_by' => null,
        ]);

        return redirect()->route('operations.show', $operation)
            ->with('success', 'Operação reactivada.');
    }

    public function destroy(DischargeOperation $operation)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        if ($operation->discharges()->exists()) {
            return back()->withErrors(['archive' => 'Não é possível eliminar uma operação com descargas registadas.']);
        }

        $operation->delete();

        return redirect()->route('operations.index')->with('success', 'Operação eliminada.');
    }

    /* ───────────────────────── helpers ───────────────────────── */

    private function validated(Request $request, bool $editPlan, bool $needMotivo): array
    {
        $rules = [
            'navio'             => 'required|string|max:150',
            'bl'                => 'nullable|string|max:100',
            'factura'           => 'nullable|string|max:100',
            'empresa'           => 'required|string|max:150',
            'data_chegada'      => 'nullable|date',
            'observacoes'       => 'nullable|string|max:2000',
            'metadata_keys'     => 'nullable|array',
            'metadata_keys.*'   => 'nullable|string|max:60',
            'metadata_values'   => 'nullable|array',
            'metadata_values.*' => 'nullable|string|max:255',
            'transportadoras'         => 'nullable|array|max:50',
            'transportadoras.*'       => 'nullable|string|max:100',
            'transportadoras_extra'   => 'nullable|array',
            'transportadoras_extra.*' => 'nullable|string|max:100',
        ];

        if ($editPlan) {
            $rules += [
                'items'                      => 'required|array|min:1',
                'items.*.id'                 => 'nullable|integer',
                'items.*.nome'               => 'required|string|max:100|distinct:ignore_case',
                'items.*.unidade'            => 'required|in:saco,tonelada',
                'items.*.peso_unitario_kg'   => 'required_if:items.*.unidade,saco|nullable|numeric|gt:0',
                'items.*.quantidade'         => 'required|numeric|gt:0',
            ];
            if ($needMotivo) {
                $rules['motivo_alteracao'] = 'required|string|max:255';
            }
        }

        $data = $request->validate($rules);

        // metadata (pares chave/valor)
        $meta = [];
        foreach (($data['metadata_keys'] ?? []) as $i => $k) {
            $k = trim((string) $k);
            $v = trim((string) ($data['metadata_values'][$i] ?? ''));
            if ($k !== '' && $v !== '') {
                $meta[$k] = $v;
            }
        }
        unset($data['metadata_keys'], $data['metadata_values']);
        $data['metadata'] = $meta ?: null;

        // transportadoras (stickers): sem duplicados, com a marca "extra" preservada
        $extras = collect($data['transportadoras_extra'] ?? [])
            ->map(fn ($n) => mb_strtolower(trim((string) $n)))
            ->all();

        $lista  = [];
        $vistos = [];
        foreach (($data['transportadoras'] ?? []) as $n) {
            $n = trim(preg_replace('/\s+/', ' ', (string) $n));
            $k = mb_strtolower($n);
            if ($n === '' || in_array($k, $vistos, true)) {
                continue;
            }
            $vistos[] = $k;
            $lista[]  = ['nome' => $n, 'extra' => in_array($k, $extras, true)];
        }

        unset($data['transportadoras'], $data['transportadoras_extra']);
        $data['transportadoras'] = $lista ?: null;

        return $data;
    }

    /** Cria/actualiza/remove itens do plano e recalcula o peso esperado (t). */
    private function syncItems(DischargeOperation $op, array $rows): void
    {
        $keep = [];

        foreach ($rows as $row) {
            $unidade = $row['unidade'];
            $attrs = [
                'nome'             => trim($row['nome']),
                'unidade'          => $unidade,
                'peso_unitario_kg' => $unidade === 'tonelada' ? 1000 : $row['peso_unitario_kg'],
                'quantidade'       => $row['quantidade'],
            ];

            $item = ! empty($row['id']) ? $op->items()->find($row['id']) : null;

            if ($item) {
                $item->update($attrs);
                $keep[] = $item->id;
            } else {
                $keep[] = $op->items()->create($attrs)->id;
            }
        }

        foreach ($op->items()->whereNotIn('id', $keep)->get() as $item) {
            if ($item->dischargeItems()->exists()) {
                throw ValidationException::withMessages([
                    'items' => "O produto “{$item->nome}” já tem camiões lançados e não pode ser removido.",
                ]);
            }
            $item->delete();
        }

        $op->load('items');
        $op->update(['quantidade_esperada' => $op->calcPesoEsperadoTon()]);
    }
}