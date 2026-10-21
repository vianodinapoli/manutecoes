<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}
    .op-card{background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:16px 18px;height:100%;
             transition:box-shadow .15s,transform .15s;text-decoration:none;color:inherit;display:block}
    .op-card:hover{box-shadow:0 6px 18px rgba(15,23,42,.08);transform:translateY(-1px);color:inherit}
    .op-card.planned{border-style:dashed;background:#fbfcfe}
    .op-navio{font-size:.95rem;font-weight:800;color:#1e293b;line-height:1.2}
    .op-empresa{font-size:.72rem;color:#64748b}
    .op-ref{font-size:.68rem;color:#94a3b8}
    .op-ref b{color:#475569;font-weight:600}
    .op-bar{height:8px;border-radius:6px;background:#f1f3f5;overflow:hidden;margin:10px 0 4px}
    .op-bar>div{height:100%;border-radius:6px;background:linear-gradient(90deg,#1a56db,#0dcaf0)}
    .op-bar.done>div{background:#198754}
    .op-nums{display:flex;justify-content:space-between;font-size:.68rem;color:#64748b}
    .op-chip{display:inline-flex;align-items:center;gap:4px;border-radius:20px;padding:2px 8px;
             font-size:.62rem;font-weight:600;white-space:nowrap}
    .chip-transit{background:#e0f7fc;color:#0aa2c0}
    .chip-ok{background:#e6f4ec;color:#198754}
    .chip-plan{background:#fff3cd;color:#856404}
    .prod-chip{display:inline-block;background:#eef2ff;color:#3b4cca;border-radius:4px;
               font-size:.62rem;font-weight:600;padding:1px 7px;margin:2px 3px 0 0}
</style>

<div class="container-fluid py-3 px-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="page-title"><i class="bi bi-ship me-2" style="color:#0d6efd;"></i>Operações de Descarga</div>
            <div class="page-sub">Uma operação por navio — defina o plano antes de iniciar</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operations.archive') }}" class="btn btn-outline-secondary btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;">
                <i class="bi bi-archive me-1"></i>Arquivo
            </a>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm" style="border-radius:7px;font-size:.75rem;"
                    onclick="abrirOperacaoModal()">
                <i class="bi bi-plus-lg me-1"></i>Nova Operação
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2" style="font-size:.78rem;border-radius:8px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2" style="font-size:.78rem;border-radius:8px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="row g-3">
        @forelse($operations as $op)
        @php
            $recebido = round(((float) $op->discharges_sum_peso_liquido) / 1000, 3);
            $esperado = (float) $op->quantidade_esperada;
            $pct = $esperado > 0 ? min(100, round($recebido / $esperado * 100, 1)) : 0;
        @endphp
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('operations.show', $op) }}" class="op-card {{ $op->isPlanned() ? 'planned' : '' }}">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="op-navio">{{ $op->navio }}</div>
                        <div class="op-empresa">{{ $op->empresa }}</div>
                    </div>
                    @if($op->isPlanned())
                        <span class="op-chip chip-plan"><i class="bi bi-clipboard-check"></i>Planeada</span>
                    @elseif($op->pendentes_count > 0)
                        <span class="op-chip chip-transit"><i class="bi bi-truck"></i>{{ $op->pendentes_count }} por confirmar</span>
                    @elseif($op->discharges_count > 0)
                        <span class="op-chip chip-ok"><i class="bi bi-check-circle-fill"></i>Tudo confirmado</span>
                    @else
                        <span class="op-chip chip-transit"><i class="bi bi-play-circle"></i>Em curso</span>
                    @endif
                </div>

                <div class="op-ref mt-2">
                    BL: <b>{{ $op->bl ?: '—' }}</b> &nbsp;·&nbsp; Factura: <b>{{ $op->factura ?: '—' }}</b>
                    @if($op->data_chegada) &nbsp;·&nbsp; {{ $op->data_chegada->format('d/m/Y') }} @endif
                </div>

                <div class="mt-1">
                    @foreach($op->items as $it)
                        <span class="prod-chip">
                            {{ $it->nome }}:
                            {{ rtrim(rtrim(number_format($it->quantidade, 3, ',', '.'), '0'), ',') }} {{ $it->unidade_label }}
                            @if($it->unidade === 'saco') × {{ rtrim(rtrim(number_format($it->peso_unitario_kg, 3, ',', '.'), '0'), ',') }} kg @endif
                        </span>
                    @endforeach
                </div>

                <div class="op-bar {{ $pct >= 100 ? 'done' : '' }}"><div style="width:{{ $pct }}%"></div></div>
                <div class="op-nums">
                    <span><b>{{ number_format($recebido, 1, ',', '.') }} t</b> de {{ number_format($esperado, 1, ',', '.') }} t</span>
                    <span>{{ $pct }}% · {{ $op->discharges_count }} camiões</span>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center text-muted py-5" style="font-size:.85rem;">
                <i class="bi bi-ship" style="font-size:2rem;"></i>
                <div class="mt-2">Sem operações. Crie a primeira com “Nova Operação”.</div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@include('operations._modal')

</x-app-layout>
