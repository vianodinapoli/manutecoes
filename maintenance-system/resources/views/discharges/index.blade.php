<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@php
    $readonly  = $operation->isArchived();
    $esperado  = (float) $operation->quantidade_esperada;
    $recebido  = $operation->recebido_ton;
    $falta     = $operation->falta_ton;
    $pct       = $operation->progresso;

    // Cores por produto (pela ordem do plano)
    $palette = [
        ['bg' => '#e8f0fe', 'bd' => '#c3d9fa', 'tx' => '#1a56db'],
        ['bg' => '#fde8e8', 'bd' => '#f8c4c4', 'tx' => '#dc3545'],
        ['bg' => '#e6f4ec', 'bd' => '#b7dfc7', 'tx' => '#198754'],
        ['bg' => '#fff3cd', 'bd' => '#ffe08a', 'tx' => '#b35c00'],
        ['bg' => '#f3e8ff', 'bd' => '#d9bcff', 'tx' => '#6f42c1'],
        ['bg' => '#e0f7fc', 'bd' => '#9fe3f0', 'tx' => '#0aa2c0'],
    ];
    $cor = [];
    foreach ($planItems as $idx => $pi) { $cor[$pi->id] = $palette[$idx % count($palette)]; }

    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $transpExtra = $operation->transportadorasExtra();

    $dtChegada = $operation->data_chegada ? \Carbon\Carbon::parse($operation->data_chegada) : null;
    $dtInicio  = $operation->started_at   ? \Carbon\Carbon::parse($operation->started_at)   : null;
    $dtArquivo = $operation->archived_at  ? \Carbon\Carbon::parse($operation->archived_at)  : null;
    $nomesProdutos = $planItems->pluck('nome')->filter()->implode(', ');
@endphp

<style>
   /* ── Base: peso leve em toda a página ────────────────── */
.op-page{font-weight:300;color:#334155;letter-spacing:.1px}
.op-page b,.op-page strong,.op-page .fw-bold,.op-page .fw-semibold{font-weight:400 !important}

/* ── Cabeçalho da página ─────────────────────────────── */
.page-header-bar{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px}
.page-title{font-size:clamp(.95rem,1.6vw,1.02rem);font-weight:500;color:#1e293b;margin:0}
.page-sub{font-size:.68rem;font-weight:300;color:#64748b;margin:2px 0 0}
.back-link{font-size:.7rem;color:#64748b;text-decoration:none}
.back-link:hover{color:#0d6efd}
.btn-op{border-radius:8px;font-weight:400;font-size:.7rem;padding:.3rem .65rem;min-height:30px;
        display:inline-flex;align-items:center;justify-content:center;gap:.35rem}
.estado-badge{font-size:.58rem;font-weight:400;vertical-align:middle}

/* ── Cabeçalho da operação ───────────────────────────── */
.op-head{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:12px 16px;margin-bottom:12px}
.op-ref{font-size:.68rem;color:#64748b;display:flex;flex-wrap:wrap;gap:3px 14px}
.op-ref b{color:#334155;font-weight:400}
.op-ref i{color:#94a3b8;margin-right:3px}
.op-bar{height:7px;border-radius:6px;background:#eef1f5;overflow:hidden;margin:10px 0 5px}
.op-bar>div{height:100%;border-radius:6px;background:linear-gradient(90deg,#1a56db,#0dcaf0);transition:width .4s}
.op-bar.done>div{background:#198754}
.op-nums{display:flex;justify-content:space-between;font-size:.68rem;color:#64748b;flex-wrap:wrap;gap:6px}
.meta-chip{display:inline-block;background:#f1f3f5;color:#475569;border-radius:6px;
           font-size:.62rem;padding:2px 7px;margin:3px 4px 0 0}

/* ── Transportadoras ─────────────────────────────────── */
.tp-label{font-size:.58rem;font-weight:500;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8;margin-right:6px}
.tp-chip{display:inline-flex;align-items:center;gap:4px;background:#eef2ff;color:#3b4cca;border:1px solid #d5dcff;
         border-radius:20px;font-size:.62rem;font-weight:400;padding:1px 8px;margin:3px 4px 0 0;white-space:nowrap}
.tp-chip.extra{background:#fd7e14;color:#fff;border-color:#fd7e14;box-shadow:0 0 0 2px rgba(253,126,20,.18)}

/* ── Plano vs realizado ──────────────────────────────── */
#planoTable th{font-size:.58rem;font-weight:500;letter-spacing:.6px;text-transform:uppercase;color:#868e96;
               border-bottom:1px solid #e9ecef;padding:5px 8px;white-space:nowrap}
#planoTable td{font-size:.7rem;font-weight:300;padding:5px 8px;vertical-align:middle;border-bottom:1px solid #f1f3f5}
#planoTable td.fw-bold{font-weight:400 !important}
.prod-dot{display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:6px}
.prod-tipo{font-size:.62rem;color:#94a3b8;font-weight:300;margin-left:4px}
.mini-bar{height:4px;border-radius:4px;background:#f1f3f5;overflow:hidden;min-width:70px}
.mini-bar>div{height:100%;border-radius:4px}

/* ── KPIs ────────────────────────────────────────────── */
.kpi-row{display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap}
.kpi{flex:1 1 120px;background:#fff;border:1px solid #e2e8f0;border-radius:10px;
     padding:7px 12px;position:relative;overflow:hidden}
.kpi::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:10px 0 0 10px}
.kpi.teal::before{background:#0dcaf0}.kpi.green::before{background:#198754}
.kpi.purple::before{background:#6f42c1}.kpi.gold::before{background:#fd7e14}
.kpi.amber::before{background:#ffc107}
.kpi-lbl{font-size:.56rem;font-weight:500;letter-spacing:.9px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
.kpi-val{font-size:1.05rem;font-weight:400;line-height:1.1;color:#1e293b}
.kpi-sub{font-size:.62rem;font-weight:300;color:#94a3b8;margin-top:1px}

/* ── Filtros ─────────────────────────────────────────── */
.filter-panel{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:10px 16px;margin-bottom:12px}
.filter-row{display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end}
.fi{display:flex;flex-direction:column;gap:3px}
.fi label{font-size:.58rem;font-weight:500;letter-spacing:.5px;text-transform:uppercase;color:#94a3b8}
.fi select,.fi input{border:1px solid #dee2e6;border-radius:7px;padding:3px 9px;min-height:28px;
                     font-size:.7rem;font-weight:300;color:#343a40;background:#f8f9fa;min-width:116px;outline:none}
.fi select:focus,.fi input:focus{border-color:#0d6efd;background:#fff}
.btn-f{padding:4px 12px;min-height:28px;border-radius:7px;font-size:.7rem;font-weight:400;
       cursor:pointer;border:1px solid #dee2e6;display:inline-flex;align-items:center;gap:5px}
.btn-fa{background:#0d6efd;color:#fff;border-color:#0d6efd}.btn-fa:hover{background:#0b5ed7}
.btn-fc{background:#f8f9fa;color:#495057}.btn-fc:hover{background:#e9ecef}
.btn-fr{background:#198754;color:#fff;border-color:#198754}.btn-fr:hover{background:#146c43}
.atr-chip{display:inline-block;font-size:.56rem;font-weight:500;letter-spacing:.4px;padding:1px 6px;border-radius:4px;margin:2px 0 0 4px}
.atr-chip.normal{background:#e8f0fe;color:#1a56db}.atr-chip.link{background:#fde8e8;color:#dc3545}
.active-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:8px}
.ftag{display:inline-flex;align-items:center;gap:5px;background:#e7f1ff;color:#0d6efd;
      border:1px solid #b6d0ff;border-radius:20px;padding:2px 9px;font-size:.64rem;font-weight:400}
.ftag .rm{cursor:pointer;opacity:.6;font-size:.56rem}.ftag .rm:hover{opacity:1}

/* ── Tabela ──────────────────────────────────────────── */
#dTable{font-size:.72rem;font-weight:300;color:#334155;letter-spacing:.1px}
#dTable thead tr{background:#f8f9fa}
#dTable thead th{font-size:.58rem;font-weight:500;letter-spacing:.6px;text-transform:uppercase;
                 color:#868e96;border-bottom:1px solid #e9ecef;padding:7px 9px;white-space:nowrap}
#dTable tbody td{padding:6px 9px;vertical-align:middle;border-bottom:1px solid #f1f3f5;
                 font-size:.72rem;font-weight:300;line-height:1.4}
#dTable tbody tr:hover{background:#fafbff}
#dTable tbody tr:last-child td{border-bottom:none}

.c-id{font-size:.72rem;font-weight:400;color:#64748b}
.c-sub{font-size:.64rem;font-weight:300;color:#94a3b8}
.c-guia{font-size:.72rem;font-weight:400;color:#1a56db;white-space:nowrap}

#dTable .fw-bold{font-weight:400 !important}
#dTable .motorista-name{font-size:.74rem;font-weight:400;color:#1e293b;line-height:1.3}
#dTable .mat-badge{display:inline-block;margin-top:2px;font-size:.56rem;padding:1px 5px;font-weight:400;letter-spacing:.6px}
#dTable .atr-chip{margin:2px 0 0 4px;font-weight:500}
#dTable .sp{font-size:.64rem;font-weight:400;padding:0 5px}
#dTable .sp-ref{font-weight:400}
#dTable .sp-n{font-size:.7rem;font-weight:500}
#dTable .sp-total,#dTable .div-badge{font-weight:400}
#dTable .sbadge{font-size:.62rem;font-weight:400;padding:2px 8px}
#dTable .btn-balanca{font-size:.64rem;font-weight:500;padding:2px 9px;min-height:24px}
#dTable .ab{width:24px;height:24px;font-size:.68rem}
#dTable .text-muted{font-weight:300}

#dTable td:nth-child(1),
#dTable td:nth-child(2),
#dTable td:nth-child(6),
#dTable td:nth-child(7),
#dTable td:nth-child(8),
#dTable td:nth-child(9){white-space:nowrap}

/* ── Alerta de tempo porto -> balança ────────────────── */
.sla-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px;vertical-align:middle;
         background:#0dcaf0;box-shadow:0 0 0 3px rgba(13,202,240,.22)}
.sla-dot.sla-warn{background:#f59e0b;box-shadow:0 0 0 3px rgba(245,158,11,.30)}
.sla-dot.sla-danger{background:#dc3545;box-shadow:0 0 0 3px rgba(220,53,69,.30);animation:sla-pulse 1.2s infinite}
@keyframes sla-pulse{0%,100%{opacity:1}50%{opacity:.35}}
#dTable tbody tr.sla-warn > td{background-color:#fffbeb}
#dTable tbody tr.sla-danger > td{background-color:#fef2f2}
#dTable tbody tr.sla-warn > td:first-child{border-left:4px solid #f59e0b}
#dTable tbody tr.sla-danger > td:first-child{border-left:4px solid #dc3545}
tr.sla-warn .sla-tempo{color:#b45309;font-weight:400}
tr.sla-danger .sla-tempo{color:#b91c1c;font-weight:400}

/* ── Cargas ──────────────────────────────────────────── */
.sp{display:inline-flex;align-items:center;gap:3px;border-radius:5px;
    padding:1px 6px;font-size:.64rem;font-weight:400;line-height:1.5;border:1px solid}
.sp-ref{font-size:.54rem;letter-spacing:.4px;text-transform:uppercase;opacity:.75}
.sp-n{font-size:.7rem;font-weight:500}
.sp-total{font-size:.6rem;color:#94a3b8;background:#f1f3f5;border-radius:4px;padding:0 5px}
.div-badge{display:inline-flex;align-items:center;gap:3px;background:#fff3cd;color:#856404;
           border:1px solid #ffc107;border-radius:4px;padding:0 6px;font-size:.58rem}

/* ── Estado ──────────────────────────────────────────── */
.sbadge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;
        border-radius:20px;font-size:.62rem;font-weight:400;white-space:nowrap}

/* ── Acções ──────────────────────────────────────────── */
.ab{width:26px;height:26px;border-radius:6px;display:inline-flex;align-items:center;
    justify-content:center;font-size:.72rem;border:1px solid;transition:all .12s;
    text-decoration:none;cursor:pointer;background:transparent}
.btn-balanca{font-size:.64rem;padding:3px 9px;min-height:26px;border-radius:20px;font-weight:500;
             background:#198754;color:#fff;border:none;white-space:nowrap;
             display:inline-flex;align-items:center;gap:4px}
.btn-balanca:hover{background:#146c43;color:#fff}
.btn-balanca.pesar{background:#fd7e14}
.btn-balanca.pesar:hover{background:#d96a0c}

.motorista-name{font-size:.74rem;font-weight:400;color:#1e293b;line-height:1.3}
.mat-badge{display:inline-block;background:#1e293b;color:#fff;border-radius:4px;
           font-size:.56rem;letter-spacing:.8px;padding:1px 6px;margin-top:2px;font-weight:400}

/* ── DataTables (Mostrar / Procurar / paginação mais pequenos e leves) ── */
div.dataTables_wrapper{font-size:.7rem;font-weight:300}
div.dataTables_wrapper div.dataTables_length,
div.dataTables_wrapper div.dataTables_filter{font-size:.7rem;color:#64748b}
div.dataTables_wrapper div.dataTables_filter input{border-radius:7px;border:1px solid #dee2e6;
    padding:2px 9px;font-size:.7rem;font-weight:300;min-height:26px}
div.dataTables_wrapper div.dataTables_length select{border-radius:7px;border:1px solid #dee2e6;
    padding:2px 22px 2px 8px;font-size:.7rem;font-weight:300;min-height:26px}
div.dataTables_wrapper div.dataTables_info{font-size:.66rem;color:#94a3b8;padding-top:8px}
div.dataTables_wrapper div.dataTables_paginate .pagination{margin:0;gap:2px}
div.dataTables_wrapper div.dataTables_paginate .page-link{font-size:.68rem;font-weight:300;padding:3px 10px;
    border-radius:6px !important;color:#475569}
div.dataTables_wrapper div.dataTables_paginate .page-item.active .page-link{color:#fff;font-weight:400}

.modal-loading{display:flex;align-items:center;justify-content:center;
               height:260px;flex-direction:column;gap:10px;color:#94a3b8}

/* ── Modais: mesmo peso leve ─────────────────────────── */
.modal{font-weight:300}
.modal b,.modal strong,.modal .fw-bold{font-weight:400 !important}
.modal .modal-title{font-weight:500 !important}

/* ── Progresso da pesagem ────────────────────────────── */
#pwProg{border:1px solid #e2e8f0;border-radius:10px;padding:9px 12px;background:#f8fafc}
#pwBarra{height:6px;background:#e2e8f0;border-radius:6px;overflow:hidden;margin:7px 0}
#pwFill{height:100%;width:0;border-radius:6px;transition:width .25s,background .25s}
.pw-chip{font-size:.66rem;font-weight:400;border-radius:20px;padding:2px 11px;border:1px solid #cbd5e1;
         display:inline-flex;align-items:center;gap:5px}
#pwTitulo{font-size:.8rem;font-weight:500}
#pwTexto{font-size:.68rem}

.lbl-b{font-size:.68rem;font-weight:400;color:#64748b}
.lbl-b small{font-weight:300;color:#94a3b8}

/* ── Modal de confirmação ────────────────────────────── */
.confirm-icon{width:50px;height:50px;border-radius:50%;display:flex;align-items:center;
              justify-content:center;font-size:1.35rem;margin:0 auto 12px}
.confirm-icon.perigo{background:#fee2e2;color:#dc3545}
.confirm-icon.aviso{background:#fef3c7;color:#b45309}
.confirm-icon.info{background:#e0ecff;color:#0d6efd}
#confirmarTitulo{font-size:.9rem;font-weight:500;color:#1e293b}
#confirmarTexto{color:#64748b;font-size:.74rem;margin-bottom:0}

/* ── Modais visíveis em qualquer ecrã, sem zoom ──────── */
.modal .modal-dialog{margin:.5rem auto;max-width:min(var(--bs-modal-width,500px),calc(100vw - 1rem))}
.modal .modal-content{max-height:calc(100vh - 1rem);max-height:calc(100dvh - 1rem);
                      display:flex;flex-direction:column;overflow:hidden}
.modal .modal-content>form{display:flex;flex-direction:column;min-height:0;max-height:inherit;overflow:hidden}
.modal .modal-header,.modal .modal-footer{flex-shrink:0}
.modal .modal-body{overflow-y:auto;min-height:0;-webkit-overflow-scrolling:touch}
@media (max-width:767.98px){
    .modal input,.modal select,.modal textarea,.fi select,.fi input{font-size:16px !important}
    .modal .modal-footer{flex-wrap:wrap}
    .modal .modal-footer .btn{flex:1 1 140px}
    .fi{flex:1 1 140px}
    .fi select,.fi input{width:100%}
}
@media (pointer:coarse){
    .modal .btn,.modal .form-control,.modal .form-select{min-height:40px}
    .ab{width:34px;height:34px}
    .btn-balanca{min-height:34px}
}
</style>

<div class="container-fluid py-3 px-3 px-md-4 op-page">
    {{-- CABEÇALHO --}}
    <div class="page-header-bar no-print">
        <div>
            <a href="{{ $readonly ? route('operations.archive') : route('operations.index') }}" class="back-link">
                <i class="bi bi-arrow-left me-1"></i>{{ $readonly ? 'Arquivo' : 'Operações' }}
            </a>
            <h1 class="page-title mt-1">
                <i class="bi bi-water me-2 text-primary"></i>{{ $operation->navio }}
                @if($readonly)
                    <span class="badge bg-secondary ms-2 estado-badge">ARQUIVADA</span>
                @else
                    <span class="badge ms-2 estado-badge" style="background:#e0f7fc;color:#0a8aa5;">EM CURSO</span>
                @endif
            </h1>
            <div class="page-sub">
                {{ $operation->empresa }}
                @if($nomesProdutos)<i class="bi bi-dot"></i>{{ $nomesProdutos }}@endif
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('operations.report', $operation) }}" target="_blank" class="btn btn-outline-secondary btn-op">
                <i class="bi bi-file-earmark-text"></i>Relatório
            </a>

            @if(!$readonly)
                <button type="button" class="btn btn-outline-primary btn-op"
                        onclick='abrirOperacaoModal(@json($operation->modalPayload()))'>
                    <i class="bi bi-pencil"></i>Editar Operação
                </button>

                <form action="{{ route('operations.archiveOperation', $operation) }}" method="POST" class="m-0"
                      data-confirm
                      data-confirm-tipo="aviso"
                      data-confirm-icone="bi-archive"
                      data-confirm-titulo="Arquivar esta operação?"
                      data-confirm-texto="A operação do navio {{ $operation->navio }} vai para o Arquivo e deixa de aceitar novas descargas. Pode reactivá-la quando quiser."
                      data-confirm-ok="Sim, arquivar">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-dark btn-op">
                        <i class="bi bi-archive"></i>Arquivar
                    </button>
                </form>

                <button type="button" class="btn btn-primary btn-op shadow-sm"
                        onclick="abrirModal('{{ route('discharges.create', ['operation_id' => $operation->id]) }}', 'Registar Nova Descarga', 'primary')">
                    <i class="bi bi-plus-lg"></i>Nova Descarga
                </button>
            @else
                <form action="{{ route('operations.restore', $operation) }}" method="POST" class="m-0"
                      data-confirm
                      data-confirm-tipo="info"
                      data-confirm-icone="bi-arrow-counterclockwise"
                      data-confirm-titulo="Reactivar esta operação?"
                      data-confirm-texto="A operação do navio {{ $operation->navio }} volta a ficar em curso e poderá registar novas descargas."
                      data-confirm-ok="Sim, reactivar">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-warning btn-op">
                        <i class="bi bi-arrow-counterclockwise"></i>Reactivar
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ERROS --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 no-print" role="alert" style="font-size:.74rem;border-radius:10px;">
            <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                <i class="bi bi-exclamation-triangle-fill"></i>Não foi possível concluir. Verifique:
            </div>
            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- TOAST --}}
    @if(session('success'))
    <div class="position-fixed top-0 end-0 p-3 no-print" style="z-index:9999;">
        <div id="successToast" class="toast show border-0"
             style="border-radius:10px;min-width:240px;max-width:92vw;overflow:hidden;
                    background:rgba(25,135,84,.95);backdrop-filter:blur(12px);
                    box-shadow:0 8px 24px rgba(25,135,84,.3);">
            <div class="d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-check-circle-fill text-white" style="font-size:1rem;"></i>
                <div style="font-size:.74rem;color:rgba(255,255,255,.95);">{{ session('success') }}
                    @if(session('ticket_url'))
                        <a href="{{ session('ticket_url') }}" target="_blank"
                           style="color:#fff;font-weight:700;text-decoration:underline;margin-left:6px;white-space:nowrap;">
                            <i class="bi bi-receipt"></i> Imprimir ticket
                        </a>
                    @endif
                </div>
                <button type="button" class="btn-close btn-close-white opacity-75 ms-auto"
                        data-bs-dismiss="toast" aria-label="Fechar" style="font-size:.55rem;"></button>
            </div>
            <div style="height:2px;background:rgba(255,255,255,.15);">
                <div id="toastProg" style="height:100%;width:100%;background:rgba(255,255,255,.5);transition:width {{ session('ticket_url') ? '12s' : '3.5s' }} linear;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- CABEÇALHO DA OPERAÇÃO + PLANO VS REALIZADO --}}
    <div class="op-head no-print">
        <div class="op-ref">
            <span><i class="bi bi-file-earmark-text"></i>BL: <b>{{ $operation->bl ?: '-' }}</b></span>
            <span><i class="bi bi-receipt"></i>Factura: <b>{{ $operation->factura ?: '-' }}</b></span>
            <span><i class="bi bi-building"></i>Empresa: <b>{{ $operation->empresa }}</b></span>
            @if($dtChegada)<span><i class="bi bi-calendar-event"></i>Chegada: <b>{{ $dtChegada->format('d/m/Y') }}</b></span>@endif
            @if($dtInicio)<span><i class="bi bi-play-circle"></i>Iniciada: <b>{{ $dtInicio->format('d/m/Y H:i') }}</b></span>@endif
            @if($readonly && $dtArquivo)<span><i class="bi bi-archive"></i>Arquivada em: <b>{{ $dtArquivo->format('d/m/Y') }}</b></span>@endif
        </div>

        @if(!empty($operation->metadata))
        <div>
            @foreach($operation->metadata as $k => $v)
                <span class="meta-chip"><b>{{ $k }}:</b> {{ is_scalar($v) ? $v : json_encode($v) }}</span>
            @endforeach
        </div>
        @endif

        @if(!empty($operation->transportadoras))
        <div class="mt-2">
            <span class="tp-label"><i class="bi bi-truck me-1"></i>Transportadoras</span>
            @foreach($operation->transportadoras as $t)
                <span class="tp-chip {{ !empty($t['extra']) ? 'extra' : '' }}"
                      @if(!empty($t['extra'])) title="Fora da lista prevista (acrescentada ao lançar um camião)" @endif>
                    @if(!empty($t['extra']))<i class="bi bi-exclamation-triangle-fill"></i>@endif{{ $t['nome'] ?? '' }}
                </span>
            @endforeach
        </div>
        @endif

        @include('operations._locais_resumo', ['operation' => $operation->loadMissing('locations')])

        @if($operation->observacoes)
            <div class="mt-2" style="font-size:.72rem;color:#64748b;"><i class="bi bi-chat-left-text me-1"></i>{{ $operation->observacoes }}</div>
        @endif

        {{-- Plano vs realizado por produto --}}
        <div class="table-responsive mt-3">
            <table class="table table-sm mb-0" id="planoTable">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th class="text-end">Plano</th>
                        <th class="text-end">No porto</th>
                        <th class="text-end">Confirmado</th>
                        <th class="text-end">Falta lançar</th>
                        <th style="width:150px">Lançado</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($planItems as $pi)
                @php
                    $reg  = (float) ($pi->registado ?? 0);
                    $conf = (float) ($pi->confirmado ?? 0);
                    $sal  = round((float) $pi->quantidade - $reg, 3);
                    $pp   = $pi->quantidade > 0 ? min(100, round($reg / $pi->quantidade * 100)) : 0;
                @endphp
                <tr>
                    <td class="fw-bold">
                        <span class="prod-dot" style="background:{{ $cor[$pi->id]['tx'] }};"></span>{{ $pi->nome }}
                        <span class="prod-tipo">
                            @if($pi->unidade === 'saco') sacos de {{ $fmt($pi->peso_unitario_kg) }} kg @else a granel @endif
                        </span>
                    </td>
                    <td class="text-end">{{ $fmt($pi->quantidade) }} {{ $pi->unidade_label }}</td>
                    <td class="text-end">{{ $fmt($reg) }}</td>
                    <td class="text-end" style="color:#198754;">{{ $fmt($conf) }}</td>
                    <td class="text-end fw-bold" style="color:{{ $sal > 0 ? '#dc3545' : '#198754' }};">{{ $fmt($sal) }}</td>
                    <td>
                        <div class="mini-bar"><div style="width:{{ $pp }}%;background:{{ $cor[$pi->id]['tx'] }};"></div></div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="op-bar {{ $pct >= 100 ? 'done' : '' }}" role="progressbar"
             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso da descarga">
            <div style="width:{{ min(100, $pct) }}%"></div>
        </div>
        <div class="op-nums">
            <span><b>{{ number_format($recebido, 1, ',', '.') }} t</b> pesadas na balança de {{ number_format($esperado, 1, ',', '.') }} t esperadas ({{ number_format($pct, 1, ',', '.') }}%)</span>
            <span style="color:{{ $falta > 0 ? '#dc3545' : '#198754' }};">
                <i class="bi {{ $falta > 0 ? 'bi-hourglass-split' : 'bi-check-circle-fill' }} me-1"></i>{{ $falta > 0 ? 'Faltam ' . number_format($falta, 1, ',', '.') . ' t' : ($falta < 0 ? 'Excesso de ' . number_format(abs($falta), 1, ',', '.') . ' t' : 'Quantidade completa') }}
            </span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row no-print">
        <div class="kpi gold">
            <div class="kpi-lbl">Carregado</div>
            <div class="kpi-val" style="color:#fd7e14;">{{ $discharges->where('status','loaded')->count() }}</div>
            <div class="kpi-sub">por sair do porto</div>
        </div>
        <div class="kpi teal">
            <div class="kpi-lbl">Em Trânsito</div>
            <div class="kpi-val" style="color:#0aa2c0;">{{ $discharges->where('status','in_transit')->count() }}</div>
            <div class="kpi-sub">camiões</div>
        </div>
        <div class="kpi amber">
            <div class="kpi-lbl">A Pesar</div>
            <div class="kpi-val" style="color:#d39e00;">{{ $discharges->where('status','weighing')->count() }}</div>
            <div class="kpi-sub">falta 1 peso</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Confirmados</div>
            <div class="kpi-val" style="color:#198754;">{{ $discharges->where('status','confirmed')->count() }}</div>
            <div class="kpi-sub">na balança</div>
        </div>
        <div class="kpi purple">
            <div class="kpi-lbl">Total</div>
            <div class="kpi-val" style="color:#6f42c1;">{{ $discharges->count() }}</div>
            <div class="kpi-sub">registos</div>
        </div>
        <div class="kpi gold">
            <div class="kpi-lbl">Peso Liq.</div>
            <div class="kpi-val" style="color:#fd7e14;font-size:1rem;padding-top:3px;">
                {{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido')/1000,1,',','.') }} t
            </div>
            <div class="kpi-sub">confirmado</div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel no-print">
        <div class="filter-row">
            <div class="fi"><label for="fDe">De</label><input type="date" id="fDe"></div>
            <div class="fi"><label for="fAte">Até</label><input type="date" id="fAte"></div>
            <div class="fi">
                <label for="fTrans">Transportadora</label>
                <select id="fTrans">
                    <option value="">Todas</option>
                    @foreach($discharges->pluck('transportadora')->unique()->sort() as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fi">
                <label for="fStatus">Estado</label>
                <select id="fStatus">
                    <option value="">Todos</option>
                    <option value="loaded">Carregado</option>
                    <option value="in_transit">Em Trânsito</option>
                    <option value="weighing">A pesar</option>
                    <option value="confirmed">Confirmado</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>
            <div class="fi">
                <label for="fCarga">Carga</label>
                <select id="fCarga">
                    <option value="">Todos</option>
                    @foreach($planItems as $pi)
                    <option value="{{ mb_strtolower($pi->nome) }}">Só {{ $pi->nome }}</option>
                    @endforeach
                    <option value="misto">Misto</option>
                </select>
            </div>
            <div class="fi">
                <label for="fDiv">Divergência</label>
                <select id="fDiv">
                    <option value="">Todos</option>
                    <option value="sim">Com divergência</option>
                    <option value="nao">Sem divergência</option>
                </select>
            </div>
            <div class="fi">
                <label for="fAtrel">Atrelado</label>
                <select id="fAtrel">
                    <option value="">Todos</option>
                    <option value="normal">Normal (até 24)</option>
                    <option value="link">Link (mais de 24)</option>
                    <option value="nd">Não definido</option>
                </select>
            </div>
            <div class="d-flex gap-1 align-items-end flex-wrap">
                <button type="button" class="btn-f btn-fa" onclick="applyFilters()"><i class="bi bi-search"></i>Filtrar</button>
                <button type="button" class="btn-f btn-fc" onclick="clearFilters()"><i class="bi bi-x-lg"></i>Limpar</button>
                <button type="button" class="btn-f btn-fr" onclick="gerarRelatorioCargas()"
                        title="Relatório de cargas por transportadora e tipo de atrelado, com os filtros aplicados">
                    <i class="bi bi-file-earmark-bar-graph"></i>Gerar relatório
                </button>
            </div>
        </div>
        <div class="active-tags" id="activeTags"></div>
    </div>

    {{-- TABELA --}}
    <div class="table-card no-print">
        <div class="p-2 pt-3 table-responsive">
            <table class="table table-hover align-middle mb-0" id="dTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:80px">Nº / Data</th>
                        <th style="width:90px">Guia</th>
                        <th>Motorista</th>
                        <th>Transportadora</th>
                        <th>Carga</th>
                        <th class="text-center" style="width:100px">Peso</th>
                        <th class="text-center" style="width:90px">Tempo</th>
                        <th class="text-center" style="width:110px">Estado</th>
                        <th class="text-center" style="width:150px">Acções</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($discharges as $d)
                @php
                    $dataD = \Carbon\Carbon::parse($d->data);
                    $stClass = match($d->status){
                        'confirmed'  => 'bg-success bg-opacity-10 text-success',
                        'in_transit' => 'bg-info bg-opacity-10 text-info',
                        'loaded'     => 'bg-secondary bg-opacity-10 text-secondary',
                        default      => 'bg-warning bg-opacity-10 text-warning',   // weighing (A pesar) e pending
                    };
                    $stIcon = match($d->status){
                        'confirmed'  => 'check-circle-fill',
                        'in_transit' => 'truck',
                        'loaded'     => 'box-seam',
                        'weighing'   => 'speedometer2',
                        default      => 'hourglass-split',
                    };
                    $div = $d->hasDivergence();
                    $produtos = $d->items->map(fn ($i) => mb_strtolower($i->operationItem->nome))->implode('|');
                    $itensBalanca = $d->items->map(fn ($i) => [
                        'id'         => $i->operation_item_id,
                        'nome'       => $i->operationItem->nome,
                        'unidade'    => $i->operationItem->unidade,
                        'quantidade' => (float) $i->quantidade,
                        'confirmada' => $i->quantidade_confirmada !== null ? (float) $i->quantidade_confirmada : null,
                    ])->values();
                    $labelBalanca = match(true){
                        $d->status === 'weighing' && $d->peso_bruto !== null => 'Pesar vazio',
                        $d->status === 'weighing'                           => 'Pesar cheio',
                        default                                             => 'Balança',
                    };

                    // Alerta porto -> balança: só camiões que ainda não chegaram à balança
                    $desde = null;
                    if (in_array($d->status, ['pending', 'in_transit'], true) && ($d->saiu_porto_em || $d->hora_saida_porto)) {
                        $desde = $d->saiu_porto_em
                            ? $d->saiu_porto_em->copy()
                            : \Carbon\Carbon::parse($dataD->format('Y-m-d') . ' ' . substr((string) $d->hora_saida_porto, 0, 5), 'Africa/Maputo');
                    }
                    $minSla = $desde ? max(0, (int) $desde->diffInMinutes(now(), false)) : null;
                    $sla    = $minSla === null ? '' : ($minSla >= 120 ? 'danger' : ($minSla >= 80 ? 'warn' : 'ok'));
                @endphp
                <tr class="{{ $sla ? 'sla-' . $sla : '' }}"
                    @if($desde) data-desde="{{ $desde->timestamp * 1000 }}" @endif
                    data-status="{{ $d->status }}"
                    data-transportadora="{{ strtolower($d->transportadora) }}"
                    data-data="{{ $dataD->format('Y-m-d') }}"
                    data-produtos="{{ $produtos }}"
                    data-divergencia="{{ $div ? 'sim' : 'nao' }}"
                    data-atrelado="{{ $d->tipo_atrelado ?: 'nd' }}">

                    {{-- Nº / Data --}}
                    <td data-order="{{ $d->id }}">
                        <span class="c-id">#{{ $d->id }}</span>
                        <div class="c-sub">{{ $dataD->format('d/m/Y') }}</div>
                    </td>

                    {{-- Guia --}}
                    <td><span class="c-guia">{{ $d->numero_guia }}</span></td>

                    {{-- Motorista --}}
                    <td>
                        <div class="motorista-name">{{ $d->motorista }}</div>
                        <span class="mat-badge">{{ $d->matricula }}</span>
                        @if($d->assinado ?? false)
                            <i class="bi bi-pen-fill" style="color:#198754;font-size:.64rem;margin-left:4px;" title="Assinado pelo motorista"></i>
                        @endif
                        @if($d->tipo_atrelado)
                            <span class="atr-chip {{ $d->tipo_atrelado }}"
                                  title="{{ $d->atreladoInconsistente() ? 'Não bate com o nº de sacos' : 'Atrelado ' . $d->tipo_atrelado }}">
                                {{ strtoupper($d->tipo_atrelado) }}@if($d->atreladoInconsistente())<i class="bi bi-exclamation-triangle-fill ms-1"></i>@endif
                            </span>
                        @endif
                    </td>

                    {{-- Transportadora --}}
                    <td>
                        <span style="font-size:.72rem;color:#64748b;">{{ $d->transportadora }}</span>
                        @if(in_array(mb_strtolower($d->transportadora), $transpExtra, true))
                            <div><span class="tp-chip extra" title="Transportadora fora da lista prevista da operação">
                                <i class="bi bi-exclamation-triangle-fill"></i>Fora da lista
                            </span></div>
                        @endif
                    </td>

                    {{-- Carga (um pill por produto) --}}
                    <td>
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            @foreach($d->items as $it)
                            @php $c = $cor[$it->operation_item_id] ?? $palette[0]; @endphp
                            <span class="sp" style="background:{{ $c['bg'] }};color:{{ $c['tx'] }};border-color:{{ $c['bd'] }};"
                                  title="{{ $it->operationItem->nome }}">
                                <span class="sp-ref">{{ mb_substr($it->operationItem->nome, 0, 3) }}</span>
                                <span class="sp-n">{{ $fmt($it->quantidade) }}</span>
                            </span>
                            @endforeach
                            @if($d->total_sacos > 0)
                            <span class="sp-total" title="Total de sacos">{{ $d->total_sacos }}</span>
                            @endif
                            @if($div)
                            <span class="div-badge" title="Divergência: sacos confirmados"><i class="bi bi-exclamation-triangle-fill" style="font-size:.56rem;"></i>{{ $d->sacos_confirmados }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- Peso --}}
                    <td class="text-center">
                        <div class="fw-bold" style="font-size:.73rem;">{{ number_format((float) $d->peso_total_estimado, 0, ',', '.') }} kg</div>
                        @if($d->status === 'weighing')
                            <div style="font-size:.66rem;color:#b35c00;">
                                {{ $d->peso_bruto !== null ? 'Cheio: ' . number_format((float) $d->peso_bruto, 0, ',', '.') : 'Vazio: ' . number_format((float) $d->tara, 0, ',', '.') }}
                            </div>
                        @elseif($d->peso_liquido)
                            <div style="font-size:.66rem;color:#198754;">Líq: {{ number_format((float) $d->peso_liquido, 0, ',', '.') }}</div>
                        @endif
                    </td>

                    {{-- Tempo (camiões em viagem: contador ao vivo com ponto de cor) --}}
                    <td class="text-center" style="white-space:nowrap;">
                        @if($desde)
                        <span class="sla-dot sla-{{ $sla }}"
                              title="Saiu do porto às {{ $desde->copy()->timezone('Africa/Maputo')->format('H:i') }}. Amarelo aos 80 min, vermelho aos 120 min."></span><span
                              class="sla-tempo" data-tempo style="font-size:.7rem;color:#64748b;">{{ $minSla }}m</span>
                        @elseif($d->tempo_transporte)
                        <span style="font-size:.7rem;color:#64748b;"><i class="bi bi-clock" style="font-size:.62rem;"></i> {{ $d->tempo_transporte }}m</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>

                    {{-- Estado --}}
                    <td class="text-center">
                        <span class="sbadge {{ $stClass }}">
                            <i class="bi bi-{{ $stIcon }}" style="font-size:.6rem;"></i>
                            {{ $d->status_label }}
                        </span>
                    </td>

                    {{-- Acções --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            @if(!$readonly && $d->status === 'loaded')
                            <form action="{{ route('discharges.saida-porto', $d->id) }}" method="POST" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-balanca" style="background:#0aa2c0;">
                                    <i class="bi bi-box-arrow-right"></i>Saiu do porto
                                </button>
                            </form>
                            @endif
                            @if(!$readonly && in_array($d->status, ['pending','in_transit','weighing']))
                            <button type="button" class="btn-balanca {{ $d->status === 'weighing' ? 'pesar' : '' }}"
                                    data-bs-toggle="modal" data-bs-target="#modalConfirm"
                                    data-id="{{ $d->id }}" data-guia="{{ $d->numero_guia }}"
                                    data-itens='@json($itensBalanca)'
                                    data-saida="{{ $d->hora_saida_porto }}"
                                    data-inicio="{{ $d->inicioTransito()?->toIso8601String() }}"
                                    data-atraso-min="{{ $d->atrasoMinutos() }}"
                                    data-atraso-fixo="{{ $d->chegou_balanca_em ? 1 : 0 }}"
                                    data-justificado="{{ $d->atrasoJustificado() ? 1 : 0 }}"
                                    data-hora="{{ $d->hora_chegada_balanca }}"
                                    data-bruto="{{ $d->peso_bruto }}"
                                    data-tara="{{ $d->tara }}"
                                    data-obs="{{ $d->observacoes }}">
                                <i class="bi bi-speedometer2"></i>{{ $labelBalanca }}
                            </button>
                            @endif
                            @if(in_array($d->status, ['weighing','confirmed']))
                            <a href="{{ route('discharges.ticket', $d->id) }}" target="_blank"
                               class="ab {{ $d->status === 'confirmed' ? 'text-success border-success' : 'text-warning border-warning' }} border-opacity-25"
                               title="{{ $d->status === 'confirmed' ? 'Ticket' : 'Ticket provisório' }}" aria-label="Ticket">
                                <i class="bi bi-receipt"></i>
                            </a>
                            @endif
                            <button type="button"
                                onclick="abrirModal('{{ route('discharges.show', $d->id) }}', 'Descarga #{{ $d->id }}', 'dark')"
                                class="ab text-primary border-primary border-opacity-25" title="Ver" aria-label="Ver descarga">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if(!$readonly)
                            <button type="button"
                                onclick="abrirModal('{{ route('discharges.edit', $d->id) }}', 'Editar #{{ $d->id }}', 'warning')"
                                class="ab text-warning border-warning border-opacity-25" title="Editar" aria-label="Editar descarga">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->user()?->hasRole('super-admin'))
                            <form action="{{ route('discharges.destroy', $d->id) }}" method="POST" class="m-0"
                                  data-confirm
                                  data-confirm-tipo="perigo"
                                  data-confirm-icone="bi-trash3"
                                  data-confirm-titulo="Eliminar este registo?"
                                  data-confirm-texto="A descarga #{{ $d->id }} (guia {{ $d->numero_guia }}, motorista {{ $d->motorista }}) será eliminada. Esta acção não pode ser desfeita."
                                  data-confirm-ok="Sim, eliminar">
                                @csrf @method('DELETE')
                                <button type="submit" class="ab text-danger border-danger border-opacity-25"
                                        title="Eliminar" aria-label="Eliminar descarga">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL UNIVERSAL --}}
<div class="modal fade" id="modalPrincipal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 text-white" id="modalHeader"
                 style="background:linear-gradient(135deg,#1a56db,#0dcaf0);padding:10px 18px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-text" id="modalIcon" style="font-size:.95rem;"></i>
                    <h5 class="modal-title fw-bold mb-0" style="font-size:.88rem;" id="modalTitulo">A carregar...</h5>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-0" style="min-height:240px;">
                <div id="modalLoading" class="modal-loading">
                    <div class="spinner-border text-primary" role="status" style="width:1.4rem;height:1.4rem;"></div>
                    <span style="font-size:.76rem;">A carregar...</span>
                </div>
                <div id="modalConteudo" style="display:none;padding:18px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BALANÇA --}}
<div class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="--bs-modal-width:520px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header bg-success text-white border-0" style="padding:10px 18px;">
                <h5 class="modal-title fw-bold" style="font-size:.88rem;">
                    <i class="bi bi-speedometer2 me-2"></i>Confirmação na Balança
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="formConfirm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body p-3 p-md-4">

                    {{-- Progresso da pesagem (2 passos: cheio e vazio, em qualquer ordem) --}}
                    <div id="pwProg" class="mb-3">
                        <div class="d-flex justify-content-between align-items-baseline flex-wrap gap-1">
                            <strong id="pwTitulo">Passo 0 de 2</strong>
                            <span id="pwTexto" class="text-muted"></span>
                        </div>
                        <div id="pwBarra"><div id="pwFill"></div></div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="pw-chip" id="pwChipB"></span>
                            <span class="pw-chip" id="pwChipT"></span>
                        </div>
                    </div>

                    {{-- Info da guia --}}
                    <div class="alert py-2 mb-3 border-0"
                         style="border-radius:8px;background:#e8f4fd;color:#1a56db;font-size:.74rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Guia <strong id="confirm_guia"></strong>. Confirme a quantidade de cada produto.
                    </div>

                    <div class="row g-2">

                        {{-- Justificação do atraso: só aparece com sinal vermelho --}}
                        <div class="col-12 d-none" id="blocoJustificacao">
                            <div class="p-2 rounded" style="background:#fdecea;border:1px solid #f5c2c0;">
                                <div class="fw-bold text-danger" style="font-size:.76rem;">
                                    <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                    Sinal vermelho: <span id="atrasoMinTexto"></span> min desde a saída do porto
                                    (limite <span id="atrasoLimiteTexto"></span> min)
                                </div>
                                <div class="text-muted mb-2" style="font-size:.7rem;">
                                    Justifique o atraso para confirmar a chegada à balança.
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach(\App\Models\Discharge::ATRASO_MOTIVOS as $chave => $rotulo)
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-motivo"
                                                data-motivo="{{ $chave }}" aria-pressed="false"
                                                onclick="escolherMotivo('{{ $chave }}')">{{ $rotulo }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="atraso_motivo" id="atraso_motivo">
                                <div id="motivoOutroWrap" class="d-none mt-2">
                                    <label class="form-label lbl-b" for="atraso_justificacao">Descreva o motivo</label>
                                    <textarea name="atraso_justificacao" id="atraso_justificacao"
                                              class="form-control form-control-sm" rows="2" maxlength="500"
                                              style="border-radius:7px;font-size:.76rem;"
                                              placeholder="Ex: avaria no caminho"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Hora chegada + tempo --}}
                        <div class="col-sm-6">
                            <label class="form-label lbl-b" for="hora_chegada_balanca">Hora de chegada</label>
                            <input type="time" name="hora_chegada_balanca" id="hora_chegada_balanca"
                                   class="form-control form-control-sm" style="border-radius:7px;font-size:.76rem;" readonly tabindex="-1" placeholder="Automático">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label lbl-b" for="transport_time_calc">Tempo de transporte</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="transport_time_calc" class="form-control bg-light"
                                       style="border-radius:7px 0 0 7px;font-size:.76rem;" readonly placeholder="Automático">
                                <span class="input-group-text" style="font-size:.72rem;">min</span>
                            </div>
                        </div>

                        {{-- Peso cheio (bruto) com botão de leitura --}}
                        <div class="col-12">
                            <label class="form-label lbl-b" for="peso_bruto">
                                Peso cheio / bruto (kg) <small>(camião a chegar com a carga)</small>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="peso_bruto" id="peso_bruto"
                                       class="form-control" style="border-radius:7px 0 0 7px;font-size:.76rem;"
                                       step="0.01" min="0" placeholder="0.00"
                                       oninput="calcNetWeight()">
                                <button type="button" id="btnLerBalanca"
                                        onclick="lerBalancaUI('peso_bruto')"
                                        class="btn btn-sm btn-outline-success fw-bold"
                                        style="border-radius:0 7px 7px 0;font-size:.72rem;white-space:nowrap;padding:0 12px;">
                                    <i class="bi bi-usb-symbol me-1"></i>Ler balança
                                </button>
                            </div>
                        </div>

                        {{-- Peso vazio (tara) com botão de leitura --}}
                        <div class="col-sm-6">
                            <label class="form-label lbl-b" for="tara">
                                Peso vazio / tara (kg) <small>(ao voltar)</small>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="tara" id="tara"
                                       class="form-control" style="border-radius:7px 0 0 7px;font-size:.76rem;"
                                       step="0.01" min="0" placeholder="0.00" oninput="calcNetWeight()">
                                <button type="button" id="btnLerTara"
                                        onclick="lerBalancaUI('tara')"
                                        class="btn btn-sm btn-outline-success fw-bold"
                                        style="border-radius:0 7px 7px 0;font-size:.72rem;white-space:nowrap;padding:0 10px;">
                                    <i class="bi bi-usb-symbol me-1"></i>Ler
                                </button>
                            </div>
                        </div>

                        {{-- Peso líquido calculado --}}
                        <div class="col-sm-6">
                            <label class="form-label lbl-b" for="net_weight_calc">Peso líquido (kg)</label>
                            <input type="text" id="net_weight_calc"
                                   class="form-control form-control-sm bg-light fw-bold text-success"
                                   style="border-radius:7px;font-size:.76rem;" readonly placeholder="Aguarda os 2 pesos">
                        </div>

                        {{-- Estado da leitura / validação --}}
                        <div class="col-12">
                            <div id="balanca-status" style="font-size:.7rem;min-height:16px;" aria-live="polite"></div>
                        </div>

                        {{-- Quantidades confirmadas, uma por produto (gerado por JS) --}}
                        <div class="col-12">
                            <label class="form-label mb-1 lbl-b">Quantidade confirmada por produto</label>
                            <div id="confirmItens" class="row g-2"></div>
                            <div id="divergence_alert" class="text-danger d-none" style="font-size:.7rem;margin-top:4px;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Divergência detectada!
                            </div>
                        </div>

                        {{-- Config porta (colapsável) --}}
                        <div class="col-12 mt-1">
                            <a class="text-muted" style="font-size:.7rem;cursor:pointer;text-decoration:none;"
                               data-bs-toggle="collapse" href="#configSerial">
                                <i class="bi bi-gear me-1"></i>Configurações da porta serial
                            </a>
                            <div class="collapse" id="configSerial">
                                <div class="row g-2 mt-1 p-2 rounded"
                                     style="background:#f8f9fa;border:1px solid #e9ecef;">
                                    <div class="col-6">
                                        <label for="cfg_baud" style="font-size:.66rem;color:#94a3b8;font-weight:600;">Baud rate</label>
                                        <select id="cfg_baud" class="form-select form-select-sm" style="border-radius:6px;font-size:.74rem;">
                                            <option value="1200">1200</option>
                                            <option value="2400">2400</option>
                                            <option value="4800">4800</option>
                                            <option value="9600" selected>9600</option>
                                            <option value="19200">19200</option>
                                            <option value="38400">38400</option>
                                            <option value="57600">57600</option>
                                            <option value="115200">115200</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="cfg_parity" style="font-size:.66rem;color:#94a3b8;font-weight:600;">Paridade</label>
                                        <select id="cfg_parity" class="form-select form-select-sm" style="border-radius:6px;font-size:.74rem;">
                                            <option value="none" selected>None</option>
                                            <option value="even">Even</option>
                                            <option value="odd">Odd</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2" style="font-size:.68rem;color:#94a3b8;">
                                            <i class="bi bi-info-circle"></i>
                                            Consulte o manual da balança se não souber os valores. O mais comum é 9600/8N1.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Observações --}}
                        <div class="col-12">
                            <label class="form-label lbl-b" for="obs_balanca">Observações</label>
                            <textarea name="observacoes" id="obs_balanca" class="form-control form-control-sm"
                                      style="border-radius:7px;font-size:.76rem;" rows="2"
                                      placeholder="Ex: saco rasgado, diferença de peso..."></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding:0 18px 14px;">
                    <button type="button" class="btn btn-light border btn-op px-3"
                            data-bs-dismiss="modal" onclick="Balanca.cancelar()">
                        <i class="bi bi-x-lg"></i>Cancelar
                    </button>
                    <button type="submit" id="btnConfirmar" class="btn btn-success btn-op px-4">
                        <i class="bi bi-check-circle"></i>Confirmar e guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DE CONFIRMAÇÃO (apagar, arquivar, reactivar) --}}
<div class="modal fade" id="confirmarModal" tabindex="-1" aria-labelledby="confirmarTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="--bs-modal-width:380px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="confirm-icon perigo" id="confirmarIconeBox"><i class="bi bi-trash3" id="confirmarIcone"></i></div>
                <h2 class="h5" id="confirmarTitulo">Tem a certeza?</h2>
                <p id="confirmarTexto"></p>
                <div class="d-flex flex-wrap gap-2 justify-content-center mt-4">
                    <button type="button" class="btn btn-light border btn-op flex-fill" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger btn-op flex-fill" id="confirmarOk">
                        <i class="bi bi-check-lg" id="confirmarOkIcone"></i><span>Confirmar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDITAR OPERAÇÃO --}}
@unless($readonly)
    @include('operations._modal')
@endunless

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dtTable;
let _itensBalanca = [];

// Alerta porto -> balança (minutos): limites em config/operacoes.php (iguais aos do servidor)
const SLA_AMARELO  = @json((int) config('operacoes.atraso_laranja_min', 60));
const SLA_VERMELHO = @json((int) config('operacoes.atraso_vermelho_min', 80));

// Tempo real: pergunta ao servidor a cada 2 s se algo mudou; recarga de segurança a cada 60 s
const URL_SINAL       = @json(route('operations.sinal'));
const SINAL_MS        = 2000;
const AUTO_REFRESH_MS = 60000;
let _autoOcupado = false;
let _ultimoSinal = null;

// Escapa texto antes de o pôr em HTML
function esc(s){
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

$(document).ready(function(){
    dtTable = $('#dTable').DataTable({
        language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order:[[0,'desc']],
        columnDefs:[{ orderable:false, targets:[7,8] }],
        pageLength:15,
    });

    // Contador ao vivo + cores (actualiza todas as linhas, mesmo as de outras páginas da tabela)
    atualizarSla();
    setInterval(atualizarSla, 20000);

    // Actualização automática (sem recarregar a página)
    setInterval(verificarSinal, SINAL_MS);
    setInterval(atualizarAuto, AUTO_REFRESH_MS);
    document.addEventListener('visibilitychange', function(){ if (!document.hidden) verificarSinal(); });

    @if(session('success'))
    const t = document.getElementById('successToast');
    new bootstrap.Toast(t,{delay:{{ session('ticket_url') ? 12000 : 3500 }}}).show();
    setTimeout(()=>{ document.getElementById('toastProg').style.width='0%'; },50);
    @endif

    // Abrir modal da balança: gera um campo por produto e pré-preenche o que já foi registado
    $(document).on('click','[data-bs-target="#modalConfirm"]',function(){
        const b=$(this);
        $('#confirm_guia').text(b.data('guia'));
        window._departureTime = b.data('saida');
        $('#formConfirm').attr('action',`/discharges/${b.data('id')}/confirm`);

        // valores já gravados (1ª pesagem), se existirem
        // (attr() e não data(): o jQuery converteria os números e perderia o formato)
        const horaGravada = (b.attr('data-hora') || '').substring(0,5);
        $('#hora_chegada_balanca').val(horaGravada);
        $('#peso_bruto').val(b.attr('data-bruto') || '');
        $('#tara').val(b.attr('data-tara') || '');
        $('#obs_balanca').val(b.attr('data-obs') || '');
        $('#net_weight_calc,#transport_time_calc').val('');
        $('#balanca-status').html('');
        $('#divergence_alert').addClass('d-none');

        prepararJustificacao(b);
        construirCamposBalanca(b.data('itens') || []);
        verificarDivergencia();
        calcTransportTime();
        calcNetWeight();   // também actualiza o progresso e o botão
    });

    $('#hora_chegada_balanca').on('change', calcTransportTime);

    // Validação no cliente: pelo menos 1 peso; se houver os dois, cheio > vazio
    document.getElementById('formConfirm').addEventListener('submit', function(e){
        // Sinal vermelho: sem motivo do atraso não confirma a chegada
        if (_exigeJustificacao) {
            const motivo = $('#atraso_motivo').val();
            if (!motivo) {
                e.preventDefault();
                setBalancaStatus('Escolha o motivo do atraso (sinal vermelho).', '#dc3545', 'bi-x-circle-fill');
                return;
            }
            if (motivo === 'outro' && $('#atraso_justificacao').val().trim().length < 5) {
                e.preventDefault();
                setBalancaStatus('Descreva o motivo do atraso (mínimo 5 caracteres).', '#dc3545', 'bi-x-circle-fill');
                $('#atraso_justificacao').trigger('focus');
                return;
            }
        }

        const sb = $('#peso_bruto').val().trim(), st = $('#tara').val().trim();
        if (sb === '' && st === '') {
            e.preventDefault();
            setBalancaStatus('Registe pelo menos um peso (cheio ou vazio).', '#dc3545', 'bi-x-circle-fill');
            return;
        }
        if (sb !== '' && st !== '' && parseFloat(sb) <= parseFloat(st)) {
            e.preventDefault();
            setBalancaStatus('O peso cheio tem de ser maior que o peso vazio.', '#dc3545', 'bi-x-circle-fill');
        }
    });

    document.getElementById('modalPrincipal').addEventListener('hidden.bs.modal',function(){
        document.getElementById('modalConteudo').innerHTML='';
        document.getElementById('modalConteudo').style.display='none';
        document.getElementById('modalLoading').style.display='flex';
    });
});

/* ── Todos os modais ficam centrados e com scroll interno ── */
document.addEventListener('show.bs.modal', function (e) {
    const dlg = e.target.querySelector('.modal-dialog');
    if (dlg) dlg.classList.add('modal-dialog-centered');
});

/* ── Confirmação amigável (formulários com data-confirm) ──
   Delegado ao documento: continua a funcionar depois de cada actualização da tabela. */
(function () {
    const modalEl = document.getElementById('confirmarModal');
    const okBtn   = document.getElementById('confirmarOk');
    const TIPOS = {
        perigo: { icone: 'bi-trash3',                btn: 'btn-danger'  },
        aviso:  { icone: 'bi-archive',               btn: 'btn-warning' },
        info:   { icone: 'bi-arrow-counterclockwise', btn: 'btn-primary' },
    };
    let pendente = null; // { acao, token, metodo }

    function submeter(p) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = p.acao;
        f.style.display = 'none';
        [['_token', p.token], ['_method', p.metodo]].forEach(([n, v]) => {
            if (!v) return;
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = n; i.value = v;
            f.appendChild(i);
        });
        document.body.appendChild(f);
        f.submit();
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
        e.preventDefault();

        pendente = {
            acao: form.action,
            token: (form.querySelector('input[name="_token"]') || {}).value,
            metodo: (form.querySelector('input[name="_method"]') || {}).value,
        };
        const tipo   = TIPOS[form.dataset.confirmTipo] ? form.dataset.confirmTipo : 'perigo';
        const icone  = form.dataset.confirmIcone || TIPOS[tipo].icone;
        const titulo = form.dataset.confirmTitulo || 'Tem a certeza?';
        const texto  = form.dataset.confirmTexto || 'Esta acção não pode ser desfeita.';
        const ok     = form.dataset.confirmOk || 'Confirmar';

        // Sem Bootstrap JS: usa a confirmação simples do navegador
        if (!(window.bootstrap && window.bootstrap.Modal)) {
            if (window.confirm(titulo + '\n\n' + texto)) submeter(pendente);
            pendente = null;
            return;
        }

        document.getElementById('confirmarTitulo').textContent = titulo;
        document.getElementById('confirmarTexto').textContent = texto;
        document.getElementById('confirmarIconeBox').className = 'confirm-icon ' + tipo;
        document.getElementById('confirmarIcone').className = 'bi ' + icone;
        okBtn.className = 'btn btn-op flex-fill ' + TIPOS[tipo].btn;
        document.getElementById('confirmarOkIcone').className = 'bi ' + icone;
        okBtn.querySelector('span').textContent = ok;
        okBtn.disabled = false;
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    okBtn.addEventListener('click', function () {
        if (!pendente) return;
        okBtn.disabled = true;
        okBtn.querySelector('span').textContent = 'A processar...';
        submeter(pendente);
    });

    modalEl.addEventListener('hidden.bs.modal', function () { pendente = null; });
})();

/* ── Alerta de tempo: camiões que saíram do porto e ainda não chegaram à balança ── */
function atualizarSla() {
    if (!dtTable) return;

    dtTable.rows().nodes().to$().filter('[data-desde]').each(function () {
        const tr  = this;
        const min = Math.max(0, Math.floor((Date.now() - Number(tr.dataset.desde)) / 60000));
        const nivel = min >= SLA_VERMELHO ? 'danger' : (min >= SLA_AMARELO ? 'warn' : 'ok');

        tr.classList.remove('sla-ok', 'sla-warn', 'sla-danger');
        tr.classList.add('sla-' + nivel);

        const dot = tr.querySelector('.sla-dot');
        if (dot) dot.className = 'sla-dot sla-' + nivel;

        const tempo = tr.querySelector('[data-tempo]');
        if (tempo) tempo.textContent = min + 'm';
    });
}

/* ── Actualização automática ──
   Vai buscar a própria página em segundo plano e actualiza o cabeçalho/plano, os KPIs e as linhas
   da tabela. Mantém a página, a ordenação, a pesquisa e os filtros do DataTables.
   Não corre com o separador escondido, com um modal aberto ou com o cursor num campo. */
function _autoPodeAtualizar() {
    if (document.visibilityState !== 'visible') return false;
    if (document.querySelector('.modal.show')) return false;
    const el = document.activeElement;
    if (el && ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName)) return false;
    return true;
}

// Devolve true se a página foi actualizada
async function atualizarAuto() {
    if (_autoOcupado || !dtTable || !_autoPodeAtualizar()) return false;
    _autoOcupado = true;

    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), 15000);

    try {
        const res = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            cache: 'no-store',
            signal: ctrl.signal,
        });
        if (res.status === 401 || res.status === 419 || (res.redirected && /login/i.test(res.url))) {
            window.location.reload();
            return false;
        }
        if (!res.ok) return false;

        const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
        const novoCorpo = doc.querySelector('#dTable tbody');
        if (!novoCorpo) return false;              // resposta inesperada
        if (!_autoPodeAtualizar()) return false;   // o utilizador começou a mexer entretanto

        // Cabeçalho da operação (plano vs realizado) e KPIs
        ['.op-head', '.kpi-row'].forEach(sel => {
            const atual = document.querySelector(sel), novo = doc.querySelector(sel);
            if (atual && novo) atual.innerHTML = novo.innerHTML;
        });

        // Lista de transportadoras do filtro (pode ter aparecido uma nova)
        const selAtual = document.getElementById('fTrans'), selNovo = doc.getElementById('fTrans');
        if (selAtual && selNovo) {
            const valor = selAtual.value;
            selAtual.innerHTML = selNovo.innerHTML;
            selAtual.value = valor;
        }

        // Linhas da tabela: substitui os dados e redesenha na mesma página
        const tmp = document.createElement('tbody');
        tmp.innerHTML = novoCorpo.innerHTML;
        dtTable.clear();
        dtTable.rows.add($(tmp).children('tr'));
        dtTable.draw(false);

        atualizarSla();
        return true;
    } catch (e) {
        return false;   // sem rede ou erro temporário: tenta no próximo ciclo
    } finally {
        clearTimeout(timer);
        _autoOcupado = false;
    }
}

// Pedido minúsculo: só recarrega a tabela quando o servidor diz que algo mudou
async function verificarSinal() {
    if (_autoOcupado || document.visibilityState !== 'visible') return;

    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), 10000);
    try {
        const res = await fetch(URL_SINAL, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin',
            cache: 'no-store',
            signal: ctrl.signal,
        });
        if (res.status === 401 || res.status === 419) { window.location.reload(); return; }
        if (!res.ok) return;

        const dados = await res.json();
        if (_ultimoSinal === null) { _ultimoSinal = dados.s; return; }
        if (dados.s !== _ultimoSinal) {
            const ok = await atualizarAuto();
            if (ok) _ultimoSinal = dados.s;   // se foi bloqueado (modal aberto), tenta no próximo ciclo
        }
    } catch (e) {
        // ignora: tenta no próximo ciclo
    } finally {
        clearTimeout(timer);
    }
}

/* ── Estado da leitura / validação da balança (texto seguro + ícone) ── */
/* ── Justificação do atraso (só com sinal vermelho) ── */
let _exigeJustificacao = false;

// Minutos desde a saída do porto. Se o camião já chegou, o valor está fixo (vem do servidor).
function minutosDeAtraso(b) {
    if (String(b.attr('data-atraso-fixo')) === '1') {
        const m = parseInt(b.attr('data-atraso-min'), 10);
        return Number.isNaN(m) ? null : m;
    }
    const inicio = b.attr('data-inicio');
    if (!inicio) return null;
    const t = new Date(inicio).getTime();
    if (Number.isNaN(t)) return null;
    return Math.max(0, Math.round((Date.now() - t) / 60000));
}

function prepararJustificacao(b) {
    const min = minutosDeAtraso(b);
    const jaJustificado = String(b.attr('data-justificado')) === '1';
    _exigeJustificacao = !jaJustificado && min !== null && min >= SLA_VERMELHO;

    $('#atraso_motivo').val('');
    $('#atraso_justificacao').val('');
    $('#motivoOutroWrap').addClass('d-none');
    $('#blocoJustificacao .btn-motivo').removeClass('active').attr('aria-pressed', 'false');
    $('#blocoJustificacao').toggleClass('d-none', !_exigeJustificacao);

    if (_exigeJustificacao) {
        $('#atrasoMinTexto').text(min);
        $('#atrasoLimiteTexto').text(SLA_VERMELHO);
    }
}

function escolherMotivo(motivo) {
    $('#atraso_motivo').val(motivo);
    $('#blocoJustificacao .btn-motivo').each(function () {
        const ligado = $(this).attr('data-motivo') === motivo;
        $(this).toggleClass('active', ligado).attr('aria-pressed', ligado ? 'true' : 'false');
    });
    $('#motivoOutroWrap').toggleClass('d-none', motivo !== 'outro');
    if (motivo === 'outro') $('#atraso_justificacao').trigger('focus');
    setBalancaStatus('', '#64748b');
}

function setBalancaStatus(msg, cor = '#64748b', icone = 'bi-info-circle') {
    const box = document.getElementById('balanca-status');
    box.innerHTML = '';
    const s = document.createElement('span');
    s.style.color = cor;
    const i = document.createElement('i');
    i.className = 'bi ' + icone + ' me-1';
    s.appendChild(i);
    s.appendChild(document.createTextNode(msg));
    box.appendChild(s);
}

/* ── Balança: campos por produto ── */
function construirCamposBalanca(itens) {
    _itensBalanca = itens;
    const box = document.getElementById('confirmItens');
    box.innerHTML = '';

    itens.forEach(it => {
        const col = document.createElement('div');
        col.className = 'col-sm-6';

        const lbl = document.createElement('label');
        lbl.style.cssText = 'font-size:.68rem;color:#64748b;font-weight:600;';
        lbl.textContent = `${it.nome} (registado: ${it.quantidade.toLocaleString('pt-PT')} ${it.unidade === 'tonelada' ? 't' : 'sacos'})`;

        const inp = document.createElement('input');
        inp.type = 'number';
        inp.name = `confirmados[${it.id}]`;
        inp.className = 'form-control form-control-sm confirm-qtd';
        inp.style.borderRadius = '7px';
        inp.style.fontSize = '.76rem';
        inp.min = 0;
        inp.step = it.unidade === 'tonelada' ? '0.001' : '1';
        // se já foi confirmada na 1ª pesagem, mostra o valor confirmado
        inp.value = (it.confirmada !== null && it.confirmada !== undefined) ? it.confirmada : it.quantidade;
        inp.required = true;
        inp.dataset.esperado = it.quantidade;
        inp.addEventListener('input', verificarDivergencia);

        col.appendChild(lbl);
        col.appendChild(inp);
        box.appendChild(col);
    });
}

function verificarDivergencia() {
    let div = false;
    document.querySelectorAll('#confirmItens .confirm-qtd').forEach(inp => {
        const diferente = Math.abs((parseFloat(inp.value) || 0) - parseFloat(inp.dataset.esperado)) > 0.0005;
        inp.classList.toggle('is-invalid', diferente);
        if (diferente) div = true;
    });
    $('#divergence_alert').toggleClass('d-none', !div);
}

/* ── Modal universal ── */
function abrirModal(url, titulo, cor) {
    const grads = {
        primary:'linear-gradient(135deg,#1a56db,#0dcaf0)',
        warning:'linear-gradient(135deg,#fd7e14,#ffc107)',
        dark:'linear-gradient(135deg,#1a1a2e,#0d3b2e)',
        success:'linear-gradient(135deg,#198754,#20c997)',
    };
    const icons = { primary:'plus-circle', warning:'pencil-square', dark:'file-text', success:'check-circle' };
    document.getElementById('modalHeader').style.background = grads[cor]||grads.primary;
    document.getElementById('modalIcon').className = `bi bi-${icons[cor]||'file-text'}`;
    document.getElementById('modalTitulo').textContent = titulo;
    document.getElementById('modalLoading').style.display='flex';
    document.getElementById('modalConteudo').style.display='none';
    document.getElementById('modalConteudo').innerHTML='';
    if (document.activeElement) document.activeElement.blur();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPrincipal')).show();

    // a URL de "create" já traz ?operation_id=..., por isso escolhemos ? ou &
    const sep = url.includes('?') ? '&' : '?';

    fetch(url+sep+'modal=1',{ headers:{'X-Requested-With':'XMLHttpRequest','Accept':'text/html'} })
    .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.text(); })
    .then(html=>{
        const doc=new DOMParser().parseFromString(html,'text/html');
        const div=document.getElementById('modalConteudo');
        div.innerHTML=doc.body?doc.body.innerHTML:html;
        div.style.display='block';
        document.getElementById('modalLoading').style.display='none';
        activarScriptsEForms(div);
    })
    .catch(err=>{
        const div = document.getElementById('modalConteudo');
        div.innerHTML = '<div class="alert alert-danger m-3 d-flex align-items-center gap-2" style="font-size:.76rem;"><i class="bi bi-exclamation-triangle-fill"></i><span></span></div>';
        div.querySelector('span').textContent = 'Não foi possível carregar (' + err.message + '). Tente novamente.';
        div.style.display='block';
        document.getElementById('modalLoading').style.display='none';
    });
}

function activarScriptsEForms(div) {
    div.querySelectorAll('script').forEach(s=>{ const n=document.createElement('script'); n.textContent=s.textContent; s.parentNode.replaceChild(n,s); });
    div.querySelectorAll('form').forEach(f=>{ f.addEventListener('submit',function(e){ e.preventDefault(); submeterFormModal(this); }); });
}

// Mostra erros de validação (422) dentro do modal, sem o fechar
function mostrarErrosModal(form, mensagens) {
    const antigo = form.querySelector('.erros-modal');
    if (antigo) antigo.remove();

    const box = document.createElement('div');
    box.className = 'alert alert-danger py-2 mb-3 erros-modal';
    box.style.fontSize = '.76rem';
    const ul = document.createElement('ul');
    ul.className = 'mb-0';
    mensagens.forEach(m => { const li = document.createElement('li'); li.textContent = m; ul.appendChild(li); });
    box.appendChild(ul);
    form.prepend(box);
    box.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function submeterFormModal(form) {
    const btn=form.querySelector('[type=submit]');
    const textoOriginal = btn ? btn.innerHTML : '';
    if(btn){ btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>A guardar...'; }

    fetch(form.action,{
        method:'POST',
        body:new FormData(form),
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}
    })
    .then(async r=>{
        if (r.status === 422) {
            const j = await r.json();
            const msgs = j.errors ? Object.values(j.errors).flat() : [j.message || 'Dados inválidos.'];
            mostrarErrosModal(form, msgs);
            if(btn){ btn.disabled=false; btn.innerHTML=textoOriginal; }
            return;
        }
        if (!r.ok) {
            let msg = 'Erro ' + r.status;
            try { const j = await r.json(); if (j.message) msg = j.message; } catch(e) {}
            mostrarErrosModal(form, [msg]);
            if(btn){ btn.disabled=false; btn.innerHTML=textoOriginal; }
            return;
        }
        const inst = bootstrap.Modal.getInstance(document.getElementById('modalPrincipal'));
        if (inst) inst.hide();
        window.location.reload();
    })
    .catch(()=>{
        mostrarErrosModal(form, ['Sem ligação ao servidor. Tente novamente.']);
        if(btn){ btn.disabled=false; btn.innerHTML=textoOriginal; }
    });
}

function calcTransportTime(){
    const dep=window._departureTime, arr=$('#hora_chegada_balanca').val();
    if(!dep||!arr) return;
    const [hD,mD]=String(dep).split(':').map(Number),[hA,mA]=arr.split(':').map(Number);
    const diff=(hA*60+mA)-(hD*60+mD);
    $('#transport_time_calc').val(diff>=0?diff:'-');
}

// Líquido só com os dois pesos; actualiza também o progresso e o botão
function calcNetWeight(){
    const sb = $('#peso_bruto').val().trim(), st = $('#tara').val().trim();
    if (sb !== '' && st !== '') {
        const net = parseFloat(sb) - parseFloat(st);
        $('#net_weight_calc').val(net > 0 ? net.toFixed(2) : '-');
    } else {
        $('#net_weight_calc').val('');
    }
    atualizarProgressoPesagem();
}

/* ── Progresso da pesagem: passo 0/1/2 de 2 ── */
function atualizarProgressoPesagem(){
    const b = $('#peso_bruto').val().trim() !== '';
    const t = $('#tara').val().trim() !== '';
    const n = (b ? 1 : 0) + (t ? 1 : 0);

    document.getElementById('pwTitulo').textContent = `Passo ${n} de 2`;

    const fill = document.getElementById('pwFill');
    fill.style.width = (n * 50) + '%';
    fill.style.background = n === 2 ? '#198754' : (n === 1 ? '#fd7e14' : '#cbd5e1');

    const texto = document.getElementById('pwTexto');
    if (n === 0)      texto.textContent = 'Registe pelo menos um peso (cheio ou vazio)';
    else if (n === 2) texto.textContent = 'Pesagem completa: ao guardar, a descarga fica confirmada';
    else if (b)       texto.textContent = 'Peso cheio registado. Falta pesar o camião vazio (ao voltar)';
    else              texto.textContent = 'Peso vazio registado. Falta pesar o camião cheio';

    const chip = (id, nome, ok) => {
        const el = document.getElementById(id);
        el.innerHTML = `<i class="bi ${ok ? 'bi-check-circle-fill' : 'bi-circle'}"></i>${nome}`;
        el.style.background  = ok ? '#e6f4ec' : '#fff';
        el.style.color       = ok ? '#198754' : '#64748b';
        el.style.borderColor = ok ? '#b7dfc7' : '#cbd5e1';
    };
    chip('pwChipB', 'Cheio (bruto)', b);
    chip('pwChipT', 'Vazio (tara)', t);

    // botão: confirma só quando houver os dois pesos
    const btn = document.getElementById('btnConfirmar');
    btn.disabled = (n === 0);
    btn.innerHTML = n === 2
        ? '<i class="bi bi-check-circle"></i>Confirmar descarga'
        : '<i class="bi bi-save"></i>Guardar peso (passo 1 de 2)';
}

/* ── Filtros ── */
const RELATORIO_CARGAS_URL = @json(route('operations.report.cargas', $operation));
let filtros = { de:'', ate:'', tr:'', st:'', tc:'', dv:'', at:'' };

function lerFiltros(){
    return {
        de:$('#fDe').val(), ate:$('#fAte').val(), tr:$('#fTrans').val(), st:$('#fStatus').val(),
        tc:$('#fCarga').val(), dv:$('#fDiv').val(), at:$('#fAtrel').val()
    };
}

// Filtro aplicado a TODAS as linhas da tabela (não só às da página visível)
$.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
    if (settings.nTable.id !== 'dTable') return true;
    const f = filtros;
    const r = $(settings.aoData[dataIndex].nTr);
    const prods = (r.attr('data-produtos') || '').split('|').filter(Boolean);

    if (f.de  && r.attr('data-data') < f.de)  return false;
    if (f.ate && r.attr('data-data') > f.ate) return false;
    if (f.tr  && !(r.attr('data-transportadora') || '').includes(f.tr.toLowerCase())) return false;
    if (f.st  && r.attr('data-status') !== f.st) return false;
    if (f.tc === 'misto' && prods.length < 2) return false;
    if (f.tc && f.tc !== 'misto' && !(prods.length === 1 && prods[0] === f.tc)) return false;
    if (f.dv  && r.attr('data-divergencia') !== f.dv) return false;
    if (f.at  && r.attr('data-atrelado') !== f.at) return false;
    return true;
});

function applyFilters(){
    filtros = lerFiltros();
    renderTags();
    dtTable.draw();
}
function clearFilters(){
    $('#fDe,#fAte').val('');
    $('#fTrans,#fStatus,#fCarga,#fDiv,#fAtrel').val('');
    filtros = { de:'', ate:'', tr:'', st:'', tc:'', dv:'', at:'' };
    $('#activeTags').html('');
    dtTable.draw();
}
function renderTags(){
    const f = filtros;
    const l = {
        de:`De: ${f.de}`, ate:`Até: ${f.ate}`, tr:`Transportadora: ${f.tr}`, st:`Estado: ${f.st}`,
        tc:`Carga: ${f.tc}`, dv:`Divergência: ${f.dv}`, at:`Atrelado: ${f.at === 'nd' ? 'não definido' : f.at}`
    };
    let html = '';
    for (const [k, v] of Object.entries(f)) {
        if (v) html += `<span class="ftag">${esc(l[k])} <i class="bi bi-x-lg rm" role="button" aria-label="Remover filtro" onclick="removeTag('${k}')"></i></span>`;
    }
    $('#activeTags').html(html);
}
function removeTag(k){
    const m = { de:'fDe', ate:'fAte', tr:'fTrans', st:'fStatus', tc:'fCarga', dv:'fDiv', at:'fAtrel' };
    $(`#${m[k]}`).val('');
    applyFilters();
}

// Relatório de cargas por transportadora / atrelado, com os filtros em uso
function gerarRelatorioCargas(){
    const f = lerFiltros();
    const p = new URLSearchParams();
    Object.entries(f).forEach(([k, v]) => { if (v) p.set(k, v); });
    const qs = p.toString();
    window.open(RELATORIO_CARGAS_URL + (qs ? '?' + qs : ''), '_blank');
}

/* ── Leitura da balança (porta serial): campo = 'peso_bruto' (cheio) ou 'tara' (vazio) ── */
async function lerBalancaUI(campo = 'peso_bruto') {
    const btn = document.getElementById(campo === 'tara' ? 'btnLerTara' : 'btnLerBalanca');
    const htmlOriginal = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>A ler...';

    try {
        const peso = await Balanca.lerPeso({
            baudRate : parseInt(document.getElementById('cfg_baud').value)   || 9600,
            parity   : document.getElementById('cfg_parity').value           || 'none',
            dataBits : 8,
            stopBits : 1,
            onStatus : msg => setBalancaStatus(msg, '#64748b', 'bi-hourglass-split'),
        });

        document.getElementById(campo).value = peso.toFixed(2);
        calcNetWeight();
        setBalancaStatus(`Peso ${campo === 'tara' ? 'vazio' : 'cheio'} lido: ${peso.toFixed(2)} kg`, '#198754', 'bi-check-circle-fill');

    } catch (err) {
        setBalancaStatus(err.message, '#dc3545', 'bi-x-circle-fill');
    } finally {
        btn.disabled = false;
        btn.innerHTML = htmlOriginal;
    }
}
</script>

</x-app-layout>