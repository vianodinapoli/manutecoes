cat > resources/views/saidas/index.blade.php << 'EOF'
<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@php
    $fmt   = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $stCor = ['aberta' => '#198754', 'fechada' => '#64748b'];
    $stTxt = ['aberta' => 'Aberta', 'fechada' => 'Fechada'];
@endphp

<style>
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}
    .painel{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:14px 16px}
    .kpi{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:10px 14px;height:100%}
    .kpi .v{font-size:1.25rem;font-weight:700;color:#1e293b;line-height:1.1}
    .kpi .l{font-size:.6rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;color:#94a3b8}
    .filtros label{font-size:.6rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#868e96;margin-bottom:2px}
    #tabSaidas th{font-size:.58rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#868e96;
                  border-bottom:1px solid #e9ecef;padding:8px;vertical-align:bottom}
    #tabSaidas td{font-size:.76rem;padding:8px;vertical-align:middle;border-bottom:1px solid #f1f3f5}
    #tabSaidas tr:hover td{background:#fafbff}
    .cod{font-weight:700;color:#1a56db;text-decoration:none}
    .cod:hover{text-decoration:underline}
    .estado-pill{font-size:.6rem;font-weight:700;color:#fff;border-radius:20px;padding:2px 9px;white-space:nowrap}
    .prod-chip{display:inline-block;max-width:100%;background:#e6f4ec;color:#198754;border:1px solid #b7dfc7;border-radius:4px;
               font-size:.62rem;font-weight:700;padding:1px 7px;margin:1px 3px 1px 0;white-space:normal;word-break:break-word}
    .barra{height:5px;border-radius:5px;background:#e9ecef;overflow:hidden;min-width:90px}
    .barra > div{height:100%;background:#198754}
    .dica{font-size:.68rem;color:#94a3b8}

    /* cartões (ecrãs pequenos) */
    .cartao{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:12px 14px;margin-bottom:10px}
    .cartao .cliente{font-size:.82rem;font-weight:600;color:#1e293b;word-break:break-word}
    .min-w-0{min-width:0}

    /* paginação nunca alarga a página */
    .paginacao{overflow-x:auto}
    .paginacao nav > div:first-child{margin-bottom:6px}
    .paginacao .pagination{flex-wrap:wrap;justify-content:center;margin-bottom:0}

    @media (max-width: 575.98px){
        .painel{padding:12px}
        .page-sub{font-size:.68rem}
    }
</style>

<div class="container-fluid py-3 px-2 px-md-4" style="max-width:100%;">

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div class="min-w-0" style="flex:1 1 280px;">
            <div class="page-title"><i class="bi bi-box-arrow-up me-2" style="color:#198754;"></i>Saídas de carga</div>
            <div class="page-sub">Cada saída tem o seu cliente, a operação de onde sai o material e a lista de camiões registados. Só carrega quem estiver registado.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap w-100 w-md-auto" style="flex:0 1 auto;">
            <a href="{{ route('saidas.modelo') }}" class="btn btn-sm btn-outline-secondary fw-bold flex-fill" style="font-size:.72rem;border-radius:7px;">
                <i class="bi bi-file-earmark-excel me-1"></i>Modelo Excel
            </a>
            <a href="{{ route('saidas.exportar', request()->query()) }}" class="btn btn-sm btn-outline-success fw-bold flex-fill" style="font-size:.72rem;border-radius:7px;">
                <i class="bi bi-download me-1"></i>Exportar lista
            </a>
            <button type="button" class="btn btn-sm btn-primary fw-bold flex-fill" style="font-size:.72rem;border-radius:7px;" onclick="abrirNova()">
                <i class="bi bi-plus-lg me-1"></i>Nova saída
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2" style="font-size:.78rem;border-radius:8px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2" style="font-size:.78rem;border-radius:8px;">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2" style="font-size:.78rem;border-radius:8px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md"><div class="kpi"><div class="v">{{ $kpi['saidas'] }}</div><div class="l">Saídas</div></div></div>
        <div class="col-6 col-md"><div class="kpi"><div class="v" style="color:#198754;">{{ $kpi['abertas'] }}</div><div class="l">Abertas</div></div></div>
        <div class="col-6 col-md"><div class="kpi"><div class="v">{{ $kpi['camioes'] }}</div><div class="l">Camiões registados</div></div></div>
        <div class="col-6 col-md"><div class="kpi"><div class="v" style="color:#198754;">{{ $kpi['carregados'] }}</div><div class="l">Carregados</div></div></div>
        <div class="col-12 col-md"><div class="kpi"><div class="v" style="color:#d39e00;">{{ $kpi['pendentes'] }}</div><div class="l">Por carregar</div></div></div>
    </div>

    {{-- Resumo por produto (dos resultados filtrados) --}}
    @if($resumoProdutos->isNotEmpty())
        <div class="painel mb-3 py-2">
            <span class="dica fw-bold text-uppercase me-2" style="letter-spacing:.8px;">Carregado:</span>
            @foreach($resumoProdutos as $p)
                <span class="prod-chip">{{ $p['nome'] }}: {{ $fmt($p['total']) }} {{ $p['label'] }}</span>
            @endforeach
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" action="{{ route('saidas.index') }}" class="painel mb-3 filtros">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label>Pesquisar</label>
                <input type="text" name="q" value="{{ $f['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Código, cliente, matrícula, motorista…">
            </div>
            <div class="col-6 col-md-2">
                <label>Estado</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="aberta" @selected(($f['status'] ?? '') === 'aberta')>Aberta</option>
                    <option value="fechada" @selected(($f['status'] ?? '') === 'fechada')>Fechada</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label>Operação (navio)</label>
                <select name="operation_id" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($operations as $o)
                        <option value="{{ $o->id }}" @selected(($f['operation_id'] ?? '') == $o->id)>{{ $o->navio }} · {{ $o->empresa }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label>De</label>
                <input type="date" name="de" value="{{ $f['de'] ?? '' }}" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-2">
                <label>Até</label>
                <input type="date" name="ate" value="{{ $f['ate'] ?? '' }}" class="form-control form-control-sm">
            </div>
        </div>
        <div class="d-flex gap-2 justify-content-end mt-2">
            <a href="{{ route('saidas.index') }}" class="btn btn-light btn-sm px-3">Limpar</a>
            <button type="submit" class="btn btn-dark btn-sm fw-bold px-4"><i class="bi bi-funnel me-1"></i>Filtrar</button>
        </div>
    </form>

    {{-- ───────── Ecrãs pequenos: cartões ───────── --}}
    <div class="d-md-none">
        @forelse($saidas as $s)
            @php
                $pct = $s->camioes_count ? round($s->carregados_count / $s->camioes_count * 100) : 0;
                $bk  = $porSaida[$s->id] ?? collect();
            @endphp
            <div class="cartao">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="min-w-0">
                        <a href="{{ route('saidas.show', $s) }}" class="cod">{{ $s->codigo }}</a>
                        <div class="cliente">{{ $s->cliente }}</div>
                    </div>
                    <span class="estado-pill" style="background:{{ $stCor[$s->status] ?? '#94a3b8' }};">{{ $stTxt[$s->status] ?? $s->status }}</span>
                </div>

                <div class="dica mt-1">
                    {{ $s->data?->format('d/m/Y') }}
                    @if($s->operation) · <i class="bi bi-ship" style="color:#0d6efd;"></i> {{ $s->operation->navio }} @endif
                </div>

                <div class="mt-2" style="font-size:.72rem;">
                    <b>{{ $s->carregados_count }}</b>/{{ $s->camioes_count }} carregados
                    @if($s->pendentes_count)<span class="text-muted">· {{ $s->pendentes_count }} por carregar</span>@endif
                </div>
                <div class="barra mt-1" style="width:100%;"><div style="width:{{ $pct }}%;"></div></div>

                @if($bk->isNotEmpty())
                    <div class="mt-2">
                        @foreach($bk as $x)
                            @php $it = $itemMap[$x->operation_item_id] ?? null; @endphp
                            <span class="prod-chip">{{ $it->nome ?? '—' }}: {{ $fmt($x->total) }} {{ $it->unidade_label ?? '' }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('saidas.relatorio', $s) }}" class="btn btn-sm btn-outline-secondary flex-fill" style="font-size:.72rem;">
                        <i class="bi bi-printer me-1"></i>Relatório
                    </a>
                    <a href="{{ route('saidas.show', $s) }}" class="btn btn-sm btn-outline-primary fw-bold flex-fill" style="font-size:.72rem;">
                        Abrir <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="painel text-center text-muted py-4" style="font-size:.78rem;">
                Nenhuma saída encontrada. Use "Nova saída" para criar a primeira.
            </div>
        @endforelse
    </div>

    {{-- ───────── Ecrãs médios e grandes: tabela ───────── --}}
    <div class="painel d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-sm mb-0" id="tabSaidas">
                <thead>
                    <tr>
                        <th>Saída</th><th>Data</th><th>Cliente</th><th>Operação</th>
                        <th>Camiões</th><th>Carregado</th><th>Estado</th><th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($saidas as $s)
                    @php
                        $pct = $s->camioes_count ? round($s->carregados_count / $s->camioes_count * 100) : 0;
                        $bk  = $porSaida[$s->id] ?? collect();
                    @endphp
                    <tr>
                        <td class="text-nowrap"><a href="{{ route('saidas.show', $s) }}" class="cod">{{ $s->codigo }}</a></td>
                        <td class="text-nowrap">{{ $s->data?->format('d/m/Y') }}</td>
                        <td class="fw-semibold" style="word-break:break-word;">{{ $s->cliente }}</td>
                        <td>
                            @if($s->operation)
                                <i class="bi bi-ship me-1" style="color:#0d6efd;"></i>{{ $s->operation->navio }}
                            @else — @endif
                        </td>
                        <td style="min-width:130px;">
                            <div style="font-size:.7rem;">
                                <b>{{ $s->carregados_count }}</b>/{{ $s->camioes_count }} carregados
                                @if($s->pendentes_count)<span class="text-muted">· {{ $s->pendentes_count }} por carregar</span>@endif
                            </div>
                            <div class="barra mt-1"><div style="width:{{ $pct }}%;"></div></div>
                        </td>
                        <td>
                            @forelse($bk as $x)
                                @php $it = $itemMap[$x->operation_item_id] ?? null; @endphp
                                <span class="prod-chip">{{ $it->nome ?? '—' }}: {{ $fmt($x->total) }} {{ $it->unidade_label ?? '' }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>
                        <td><span class="estado-pill" style="background:{{ $stCor[$s->status] ?? '#94a3b8' }};">{{ $stTxt[$s->status] ?? $s->status }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('saidas.relatorio', $s) }}" class="btn btn-sm btn-outline-secondary py-0" style="font-size:.64rem;" title="Relatório">
                                <i class="bi bi-printer"></i>
                            </a>
                            <a href="{{ route('saidas.show', $s) }}" class="btn btn-sm btn-outline-primary py-0 fw-bold" style="font-size:.64rem;">
                                Abrir <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4" style="font-size:.78rem;">
                        Nenhuma saída encontrada. Use "Nova saída" para criar a primeira.
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3 paginacao">{{ $saidas->links() }}</div>
</div>

{{-- MODAL: nova saída --}}
<div class="modal fade" id="modalNova" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('saidas.store') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            @csrf
            <div class="modal-header bg-primary text-white border-0" style="padding:12px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:.88rem;"><i class="bi bi-box-arrow-up me-2"></i>Nova saída</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-2">
                    <label class="small fw-bold text-muted">Cliente / empresa</label>
                    <input type="text" name="cliente" class="form-control form-control-sm" value="{{ old('cliente') }}" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-12 col-sm-5">
                        <label class="small fw-bold text-muted">Data</label>
                        <input type="date" name="data" class="form-control form-control-sm" value="{{ old('data', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12 col-sm-7">
                        <label class="small fw-bold text-muted">Operação de origem (navio)</label>
                        <select name="operation_id" class="form-select form-select-sm" required>
                            <option value="">— escolher —</option>
                            @foreach($operationsAbertas as $o)
                                <option value="{{ $o->id }}" @selected(old('operation_id') == $o->id)>{{ $o->navio }} · {{ $o->empresa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-1">
                    <label class="small fw-bold text-muted">Observações</label>
                    <textarea name="observacoes" class="form-control form-control-sm" rows="2" placeholder="Opcional">{{ old('observacoes') }}</textarea>
                </div>
                <div class="dica mt-2">Depois de criar, descarregue o modelo Excel, preencha os camiões e faça o upload.</div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:0 20px 16px;">
                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold"><i class="bi bi-check-lg me-1"></i>Criar saída</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirNova() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalNova')).show();
}
@if($errors->any() && old('cliente') !== null)
document.addEventListener('DOMContentLoaded', abrirNova);
@endif
</script>

</x-app-layout>
EOF