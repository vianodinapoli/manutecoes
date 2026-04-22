@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
/* ── Table card ── */
.table-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
#tblRequisicoes thead tr{background:#f8f9fa}
#tblRequisicoes thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap}
#tblRequisicoes tbody td{padding:9px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
#tblRequisicoes tbody tr:hover{background:#fff5f5}
#tblRequisicoes tbody tr:last-child td{border-bottom:none}

/* ── KPI cards ── */
.kpi-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:transform .15s,box-shadow .15s;height:100%;position:relative;overflow:hidden}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.08)}
.kpi-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px}
.kpi-card.red::before{background:#c60a1a}
.kpi-card.amber::before{background:#d97706}
.kpi-card.green::before{background:#16a34a}
.kpi-card.blue::before{background:#0ea5e9}
.kpi-card.dark::before{background:#0f172a}
.kpi-label{font-size:.65rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}
.kpi-value{font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:2px}
.kpi-value-sm{font-size:1.05rem;font-weight:800;line-height:1;margin-bottom:2px;padding-top:4px}
.kpi-sub{font-size:.72rem;color:#adb5bd}
.kpi-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.08}

/* ── Status badges ── */
.badge-status{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:.68rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
.badge-emitida{background:#e0f2fe;color:#0369a1}
.badge-confirmada{background:#fef9c3;color:#a16207}
.badge-finalizada{background:#dcfce7;color:#15803d}
.badge-cancelado{background:#fee2e2;color:#dc2626}

/* ── Filter panel ── */
.filter-panel{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:20px 24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
.filter-title{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#adb5bd;margin-bottom:14px;display:flex;align-items:center;gap:8px}
.filter-group{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.filter-item{display:flex;flex-direction:column;gap:5px}
.filter-item label{font-size:.7rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:#6c757d}
.filter-item select,.filter-item input{border:1px solid #dee2e6;border-radius:8px;padding:7px 12px;font-size:.82rem;color:#343a40;background:#f8f9fa;outline:none;transition:border-color .2s,box-shadow .2s;min-width:150px}
.filter-item select:focus,.filter-item input:focus{border-color:#c60a1a;box-shadow:0 0 0 3px rgba(198,10,26,.08);background:#fff}
.btn-filter{padding:8px 18px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:6px;transition:all .2s}
.btn-filter-clear{background:#f1f5f9;color:#495057;border:1px solid #dee2e6}.btn-filter-clear:hover{background:#e9ecef}
.btn-filter-pdf{background:#c60a1a;color:#fff}.btn-filter-pdf:hover{background:#a00817}
.btn-filter-pdf:disabled{background:#adb5bd;cursor:not-allowed}

/* ── Extract bar ── */
#extractBar{display:none;margin-top:14px;padding:12px 16px;
    background:linear-gradient(135deg,#fff5f5,#fde8e8);
    border:1px solid #fca5a5;border-radius:10px;
    align-items:center;gap:12px;flex-wrap:wrap}
#extractBar.visible{display:flex}
#extractBar .ext-info{font-size:.78rem;color:#991b1b;font-weight:600;flex:1}
#extractBar .ext-info span{font-weight:800}

/* ── Action buttons ── */
.action-btn{width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid;transition:all .15s;text-decoration:none;cursor:pointer;background:transparent}
.action-btn:hover{opacity:.75}
.action-btn.btn-confirm-carga{width:auto;padding:0 8px;gap:4px;font-size:.7rem;font-weight:600}

/* ── Workflow steps indicator ── */
.workflow-steps{display:flex;align-items:center;gap:0;margin-bottom:20px}
.ws-step{flex:1;text-align:center;position:relative}
.ws-step:not(:last-child)::after{content:'';position:absolute;right:0;top:50%;transform:translateY(-50%);width:100%;height:2px;background:#e9ecef;z-index:0}
.ws-dot{width:32px;height:32px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;position:relative;z-index:1;border:2px solid #e9ecef;background:#fff;color:#adb5bd;transition:all .3s}
.ws-dot.active{border-color:#c60a1a;background:#c60a1a;color:#fff}
.ws-dot.done{border-color:#16a34a;background:#16a34a;color:#fff}
.ws-label{font-size:.65rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-top:5px;color:#adb5bd}
.ws-label.active{color:#c60a1a}
.ws-label.done{color:#16a34a}

/* ── Confirm carga modal ── */
.carga-header{background:linear-gradient(135deg,#0f172a,#1e293b);padding:28px;border-radius:16px 16px 0 0}
.carga-step{display:flex;align-items:flex-start;gap:14px;padding:16px;background:#f8fafc;border-radius:10px;border:1px solid #e9ecef;margin-bottom:12px}
.carga-step-num{width:28px;height:28px;border-radius:50%;background:#c60a1a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0}
.carga-step-body{flex:1}
.carga-step-title{font-size:.8rem;font-weight:700;color:#334155;margin-bottom:4px}
.carga-step-desc{font-size:.72rem;color:#94a3b8;line-height:1.5}
.carga-field label{font-size:.72rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#64748b;margin-bottom:5px;display:block}
.carga-field input{border:1px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:.9rem;font-weight:600;color:#0f172a;width:100%;outline:none;transition:border-color .2s,box-shadow .2s}
.carga-field input:focus{border-color:#c60a1a;box-shadow:0 0 0 3px rgba(198,10,26,.1)}
.carga-field .unit-badge{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:.72rem;font-weight:700;color:#94a3b8;pointer-events:none}
.btn-finalizar{background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:10px;padding:12px 24px;font-size:.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;box-shadow:0 4px 12px rgba(22,163,74,.3)}
.btn-finalizar:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(22,163,74,.4)}
.btn-finalizar:disabled{background:#94a3b8;box-shadow:none;cursor:not-allowed;transform:none}

/* ── Predefinições panel ── */
.predef-panel{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04);margin-top:20px}
.predef-header{background:#0f172a;padding:14px 20px;display:flex;align-items:center;justify-content:space-between}
.predef-header h6{color:#fff;margin:0;font-size:.82rem;font-weight:700;letter-spacing:.5px}
.predef-item{display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid #f1f3f5;cursor:pointer;transition:background .15s}
.predef-item:last-child{border-bottom:none}
.predef-item:hover{background:#fff5f5}
.predef-item-name{font-size:.8rem;font-weight:600;color:#1e293b;flex:1}
.predef-item-details{font-size:.72rem;color:#94a3b8}
.predef-item-add{width:24px;height:24px;border-radius:6px;background:#c60a1a;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.65rem;flex-shrink:0;transition:transform .15s}
.predef-item:hover .predef-item-add{transform:scale(1.1)}

/* ── Quick-select dropdown in items table ── */
.item-predef-select{border:1px solid #e2e8f0;border-radius:6px;padding:4px 8px;font-size:.72rem;color:#64748b;background:#f8fafc;cursor:pointer;max-width:160px}
.item-predef-select:focus{outline:none;border-color:#c60a1a}

/* ── Toast ── */
.toast-req{position:fixed;top:24px;right:24px;z-index:99999;background:#fff;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.12);border-left:4px solid #16a34a;min-width:300px;transform:translateX(120%);transition:transform .35s cubic-bezier(.34,1.56,.64,1)}
.toast-req.show{transform:translateX(0)}
.toast-req .t-icon{width:36px;height:36px;background:#f0fdf4;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:1rem;flex-shrink:0}
.toast-req.error{border-left-color:#dc2626}
.toast-req.error .t-icon{background:#fef2f2;color:#dc2626}
.toast-req .t-text{flex:1}
.toast-req .t-title{font-size:.82rem;font-weight:700;color:#1e293b;margin-bottom:2px}
.toast-req .t-sub{font-size:.74rem;color:#94a3b8}
.toast-req .t-close{background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1rem;padding:0;line-height:1}

/* ── Confirm overlay ── */
.confirm-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s}
.confirm-overlay.open{opacity:1;pointer-events:all}
.confirm-box{background:#fff;border-radius:20px;max-width:400px;width:calc(100% - 32px);box-shadow:0 24px 64px rgba(0,0,0,.18);transform:scale(.93) translateY(10px);transition:transform .25s cubic-bezier(.34,1.56,.64,1);overflow:hidden}
.confirm-overlay.open .confirm-box{transform:scale(1) translateY(0)}
.confirm-header{background:#fef2f2;padding:28px 28px 20px;text-align:center;border-bottom:1px solid #fecaca}
.confirm-icon{width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#dc2626;margin:0 auto 14px}
.confirm-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin-bottom:6px}
.confirm-sub{font-size:.82rem;color:#94a3b8;line-height:1.6}
.confirm-body{padding:20px 28px 24px}
.confirm-warning{display:flex;align-items:center;gap:8px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;font-size:.78rem;color:#92400e;margin-bottom:20px}
.confirm-actions{display:flex;gap:10px}
.confirm-actions button{flex:1;padding:11px;border-radius:10px;font-size:.82rem;font-weight:600;border:none;cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px}
.btn-cancel-confirm{background:#f1f5f9;color:#475569}.btn-cancel-confirm:hover{background:#e2e8f0}
.btn-delete-confirm{background:#dc2626;color:#fff;box-shadow:0 2px 8px rgba(220,38,38,.3)}.btn-delete-confirm:hover{background:#b91c1c}
.btn-delete-confirm:disabled{background:#f87171;cursor:not-allowed;box-shadow:none}

/* ── Gestão predefinições modal ── */
.predef-manage-item{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f1f3f5}
.predef-manage-item:last-child{border-bottom:none}

/* ── Print ── */
@media print {
    #sidebar,.sb-footer,.sb-brand,nav.sb-nav,.no-print,button,a.btn,
    .filter-panel,.kpi-card,.action-btn{display:none !important}
    *{box-sizing:border-box}
    body,.layout-wrap,main.content-area,.content-body{
        display:block !important;background:white !important;
        padding:0 !important;margin:0 !important;
        font-family:DejaVu Sans,Arial,sans-serif;font-size:11px;color:#222}
    .print-only{display:block !important}
    .card{box-shadow:none !important;border:none !important}
    .print-header{display:flex !important;justify-content:space-between;align-items:flex-start;
        border-bottom:3px solid #c60a1a;padding-bottom:14px;margin-bottom:20px}
    .ph-company-name{font-size:13px;font-weight:bold;color:#c60a1a;line-height:1.3}
    .ph-company-detail{font-size:9px;color:#555;margin-top:3px;line-height:1.6}
    .ph-doc-title{text-align:right}
    .ph-doc-title h2{font-size:17px;font-weight:bold;color:#c60a1a;margin:0}
    .ph-doc-title .ph-sub{font-size:10px;color:#555;margin-top:6px;line-height:1.6}
    table{width:100%;border-collapse:collapse;margin-bottom:16px}
    table thead tr{background:#c60a1a !important;color:white !important;
        -webkit-print-color-adjust:exact;print-color-adjust:exact}
    table thead th{padding:7px 10px;font-size:10px;text-transform:uppercase;text-align:left}
    table tbody td{padding:7px 10px;border-bottom:1px solid #e8edf2;font-size:10px}
    table tbody tr:nth-child(even){background:#fdf5f5 !important;
        -webkit-print-color-adjust:exact;print-color-adjust:exact}
    .print-totals{width:260px;margin-left:auto;margin-bottom:20px}
    .print-totals table{margin-bottom:0;width:100%}
    .print-totals .total-row{background:#c60a1a !important;color:white !important;
        -webkit-print-color-adjust:exact;print-color-adjust:exact}
    .print-totals .total-row td{padding:8px 10px;font-size:13px;font-weight:bold}
    .print-totals .total-row td:last-child{text-align:right}
    .print-footer{margin-top:28px;border-top:2px solid #c60a1a;padding-top:12px}
}
@media screen {
    .print-only { display:none !important; }
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-dolly text-danger me-2"></i>Requisições de Material
            </h4>
            <p class="text-muted small mb-0">Gestão de saídas e expedições de material</p>
        </div>
        <div class="d-flex gap-2">
            @can('requisicoes-material')
            <button class="btn btn-sm fw-bold px-3 text-white shadow-sm no-print" id="btnGerir"
                style="background:#0f172a;border:none;border-radius:8px;">
                <i class="fas fa-boxes me-1"></i> Predefinições
            </button>
            <button class="btn btn-sm fw-bold px-3 text-white shadow-sm no-print" id="btnNova"
                style="background:#c60a1a;border:none;border-radius:8px;">
                <i class="fas fa-plus me-1"></i> Nova Requisição
            </button>
            @endcan
        </div>
    </div>

    {{-- KPI CARDS --}}
    @php
        $total      = $requisicoes->count();
        $emitidas   = $requisicoes->where('status','EMITIDA')->count();
        $confirmadas= $requisicoes->where('status','CONFIRMADA')->count();
        $finalizadas= $requisicoes->where('status','FINALIZADA')->count();
        $valorTotal = $requisicoes->sum('total_final');
    @endphp
    <div class="row g-3 mb-4 no-print">
        <div class="col-6 col-md-2">
            <div class="kpi-card dark">
                <div class="kpi-label">Total</div>
                <div class="kpi-value" style="color:#0f172a;">{{ $total }}</div>
                <div class="kpi-sub">requisições</div>
                <i class="fas fa-dolly kpi-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card blue">
                <div class="kpi-label">Emitidas</div>
                <div class="kpi-value" style="color:#0369a1;">{{ $emitidas }}</div>
                <div class="kpi-sub">aguardam confirmação</div>
                <i class="fas fa-paper-plane kpi-icon" style="color:#0369a1;opacity:.08"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card amber">
                <div class="kpi-label">Confirmadas</div>
                <div class="kpi-value" style="color:#d97706;">{{ $confirmadas }}</div>
                <div class="kpi-sub">peso verificado</div>
                <i class="fas fa-weight-hanging kpi-icon" style="color:#d97706;opacity:.08"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card green">
                <div class="kpi-label">Finalizadas</div>
                <div class="kpi-value" style="color:#16a34a;">{{ $finalizadas }}</div>
                <div class="kpi-sub">concluídas</div>
                <i class="fas fa-check-circle kpi-icon" style="color:#16a34a;opacity:.08"></i>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="kpi-card red">
                <div class="kpi-label">Valor Total</div>
                <div class="kpi-value-sm" style="color:#c60a1a;">
                    {{ number_format($valorTotal, 2, ',', '.') }} MT
                </div>
                <div class="kpi-sub">soma geral das requisições</div>
                <i class="fas fa-coins kpi-icon" style="color:#c60a1a;opacity:.08"></i>
            </div>
        </div>
    </div>

    {{-- FLUXO VISUAL --}}
    <div class="filter-panel no-print" style="margin-bottom:16px;padding:16px 24px;">
        <div class="filter-title"><i class="fas fa-route"></i> Fluxo de uma Requisição</div>
        <div class="workflow-steps">
            <div class="ws-step">
                <div class="ws-dot done"><i class="fas fa-file-alt"></i></div>
                <div class="ws-label done">1. Emissão</div>
            </div>
            <div class="ws-step">
                <div class="ws-dot active"><i class="fas fa-weight-hanging"></i></div>
                <div class="ws-label active">2. Confirmação de Carga</div>
            </div>
            <div class="ws-step">
                <div class="ws-dot"><i class="fas fa-check-double"></i></div>
                <div class="ws-label">3. Finalizada</div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
   {{-- FILTROS --}}
<div class="filter-panel no-print">
    <div class="filter-title"><i class="fas fa-filter"></i> Filtros de Pesquisa</div>
    <div class="filter-group">

        {{-- Nº Requisição --}}
        <div class="filter-item">
            <label>Nº Requisição</label>
            <input type="text" id="filtroNumero" placeholder="Ex: 0012" style="min-width:110px;">
        </div>

        {{-- Data --}}
        <div class="filter-item">
            <label>Data Início</label>
            <input type="date" id="filtroDataInicio">
        </div>
        <div class="filter-item">
            <label>Data Fim</label>
            <input type="date" id="filtroDataFim">
        </div>

        {{-- Destino --}}
        <div class="filter-item">
            <label>Destino</label>
            <input type="text" id="filtroDestino" placeholder="Filtrar destino...">
        </div>

        {{-- Fornecedor --}}
        <div class="filter-item">
            <label>Fornecedor</label>
            <select id="filtroFornecedor" style="min-width:170px;">
                <option value="">Todos</option>
                @foreach($suppliers as $s)
                <option value="{{ strtolower($s->name) }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Motorista --}}
        <div class="filter-item">
            <label>Motorista</label>
            <input type="text" id="filtroMotorista" placeholder="Nome do motorista...">
        </div>

        {{-- Matrícula --}}
        <div class="filter-item">
            <label>Matrícula</label>
            <input type="text" id="filtroMatricula" placeholder="Ex: MBE-1234-M" style="min-width:130px;">
        </div>

        {{-- Responsável --}}
        <div class="filter-item">
            <label>Responsável</label>
            <input type="text" id="filtroResponsavel" placeholder="Responsável...">
        </div>

        {{-- Estado --}}
        <div class="filter-item">
            <label>Estado</label>
            <select id="filtroStatus">
                <option value="">Todos</option>
                <option value="EMITIDA">Emitida</option>
                <option value="CONFIRMADA">Confirmada</option>
                <option value="FINALIZADA">Finalizada</option>
                <option value="CANCELADO">Cancelado</option>
            </select>
        </div>

        <div class="d-flex gap-2 align-items-end">
            <button class="btn-filter btn-filter-clear" id="btnLimparFiltros">
                <i class="fas fa-times"></i> Limpar
            </button>
        </div>
    </div>

    <div id="extractBar">
        <div class="ext-info">
            <i class="fas fa-check-circle me-1" style="color:#c60a1a;"></i>
            Filtro activo: <span id="extractCount">0</span> requisição(ões) encontrada(s)
            <span id="extractLabel" style="color:#7f1d1d;"></span>
        </div>
        <button class="btn-filter btn-filter-pdf" onclick="gerarExtratoPDF()">
            <i class="fas fa-file-arrow-down me-1"></i> Exportar Extrato PDF
        </button>
    </div>
</div>

    {{-- PRINT ONLY: cabeçalho --}}
    <div class="print-only print-header" id="printHeader">
        <div>
            <div class="ph-company-name">Fábrica de Explosivos de Moçambique</div>
            <div class="ph-company-detail">
                Contribuinte Nº 400019029<br>
                Av. Samora Machel Nº — Parcela 10<br>
                Telef. +258 21 745 86/03 &nbsp;|&nbsp; FAX. +258 21 745 802
            </div>
        </div>
        <div class="ph-doc-title">
            <h2>REQUISIÇÕES DE MATERIAL</h2>
            <div class="ph-sub" id="printHeaderRight"></div>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="table-card">
        <div class="p-3">
            <table id="tblRequisicoes" class="table table-hover align-middle mb-0 w-100">
                <thead>
                    <tr>
                        <th>Nº / DATA</th>
                        <th>FORNECEDOR</th>
                        <th>DESTINO</th>
                        <th>MOTORISTA</th>
                        <th>MATRÍCULA</th>
                        <th>RESPONSÁVEL</th>
                        <th>ESTADO</th>
                        <th class="text-end">TOTAL (MT)</th>
                        <th class="text-center no-print">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requisicoes as $req)
                    <tr
                        data-status="{{ $req->status }}"
                        data-date="{{ $req->date->format('Y-m-d') }}"
                        data-date-fmt="{{ $req->date->format('d/m/Y') }}"
                        data-destino="{{ strtolower($req->destino) }}"
                        data-destino-fmt="{{ $req->destino }}"
                        data-req-id="{{ $req->id }}"
                        data-fornecedor="{{ $req->supplier->name ?? '—' }}"
                        data-motorista="{{ $req->motorista ?? '—' }}"
                        data-matricula="{{ $req->matricula ?? '—' }}"
                        data-responsavel="{{ $req->responsavel ?? '—' }}"
                        data-total="{{ number_format($req->total_final, 2, ',', '.') }}"
                        data-peso="{{ $req->peso_confirmado ?? '' }}"
                        data-valor-carga="{{ $req->valor_carga ?? '' }}">
                        <td>
                            <span class="fw-bold" style="font-size:.8rem;color:#c60a1a;">
                                #{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                            </span><br>
                            <span class="text-muted" style="font-size:.72rem;">{{ $req->date->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            @if($req->supplier)
                                <span class="fw-semibold" style="font-size:.78rem;">{{ $req->supplier->name }}</span>
                                @if($req->supplier->nuit)
                                    <div class="text-muted" style="font-size:.7rem;">NUIT: {{ $req->supplier->nuit }}</div>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem;">{{ $req->destino }}</td>
                        <td style="font-size:.82rem;">{{ $req->motorista ?? '—' }}</td>
                        <td style="font-size:.82rem;">{{ $req->matricula ?? '—' }}</td>
                        <td style="font-size:.82rem;">{{ $req->responsavel ?? '—' }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'EMITIDA'    => ['badge-emitida',    'fa-paper-plane',    'Emitida'],
                                    'CONFIRMADA' => ['badge-confirmada', 'fa-weight-hanging', 'Confirmada'],
                                    'FINALIZADA' => ['badge-finalizada', 'fa-check-double',   'Finalizada'],
                                    'CANCELADO'  => ['badge-cancelado',  'fa-ban',            'Cancelado'],
                                ];
                                [$cls, $ico, $lbl] = $statusMap[$req->status] ?? ['badge-emitida','fa-circle','—'];
                            @endphp
                            <span class="badge-status {{ $cls }}">
                                <i class="fas {{ $ico }}"></i> {{ $lbl }}
                            </span>
                        </td>
                        <td class="text-end fw-bold" style="font-size:.82rem;">
                            {{ number_format($req->total_final, 2, ',', '.') }} MT
                            @if($req->peso_confirmado)
                                <div class="text-muted fw-normal" style="font-size:.7rem;">
                                    <i class="fas fa-weight-hanging me-1"></i>{{ $req->peso_confirmado }} kg
                                </div>
                            @endif
                        </td>
                        <td class="text-center no-print" style="white-space:nowrap;">
                            <a href="{{ route('requisicoes-material.pdf', $req) }}" target="_blank"
                               class="action-btn text-danger border-danger border-opacity-25" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @can('requisicoes-material')
                            {{-- Botão Confirmar Carga — só aparece se EMITIDA --}}
                            @if($req->status === 'EMITIDA')
                            <button class="action-btn btn-confirm-carga text-warning border-warning border-opacity-50"
                                data-id="{{ $req->id }}"
                                data-num="#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}"
                                data-destino="{{ $req->destino }}"
                                title="Confirmar Carga">
                                <i class="fas fa-weight-hanging"></i>
                                <span>Confirmar</span>
                            </button>
                            @endif
                            {{-- Botão Editar — só EMITIDA pode ser editada --}}
                            @if($req->status === 'EMITIDA')
                            <button class="action-btn text-primary border-primary border-opacity-25 btn-edit"
                                data-id="{{ $req->id }}" title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            @endif
                            <button class="action-btn text-danger border-danger border-opacity-25 btn-delete"
                                data-id="{{ $req->id }}"
                                data-num="#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- PRINT ONLY: totais + rodapé --}}
    <div class="print-only print-totals" id="printTotals">
        <table>
            <tr class="total-row">
                <td>TOTAL GERAL</td>
                <td>{{ number_format($valorTotal, 2, ',', '.') }} MT</td>
            </tr>
        </table>
    </div>
    <div class="print-only print-footer" id="printFooter">
        <div style="font-size:8px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;margin-bottom:2px">Emitido por</div>
        <div style="font-size:11px;font-weight:bold;">{{ auth()->user()->name }}</div>
        <div style="font-size:10px;color:#555;">{{ auth()->user()->email }}</div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL: CRIAR / EDITAR
═══════════════════════════════════════ --}}
<div class="modal fade" id="modalRequisicao" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#0f172a;">
                <h5 class="modal-title text-white" id="modalTitle">Nova Requisição de Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formRequisicao" novalidate>
                    <input type="hidden" id="req_id">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Data <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="req_date" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small">Destino / Local de Entrega <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="req_destino"
                                placeholder="Ex: Armazém Central, Obra Nº 12, Beira..." required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Fornecedor</label>
                            <select class="form-select" id="req_supplier_id">
                                <option value="">— Sem fornecedor —</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" data-nuit="{{ $s->nuit }}">
                                    {{ $s->name }}@if($s->nuit) — NUIT: {{ $s->nuit }}@endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Motorista</label>
                            <input type="text" class="form-control" id="req_motorista" placeholder="Nome do motorista">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Matrícula</label>
                            <input type="text" class="form-control" id="req_matricula" placeholder="Ex: MBE-1234-M">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Responsável / Aprovador</label>
                            <input type="text" class="form-control" id="req_responsavel">
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold small">Observações</label>
                            <textarea class="form-control" id="req_observacoes" rows="2"
                                placeholder="Notas adicionais..."></textarea>
                        </div>
                        <div class="col-md-3 d-none" id="statusWrap">
                            <label class="form-label fw-semibold small">Estado</label>
                            <select class="form-select" id="req_status">
                                <option value="EMITIDA">EMITIDA</option>
                                <option value="CONFIRMADA">CONFIRMADA</option>
                                <option value="FINALIZADA">FINALIZADA</option>
                                <option value="CANCELADO">CANCELADO</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1 border-top"></div>
                        <span class="mx-3 text-muted small fw-bold text-uppercase" style="white-space:nowrap;">
                            Itens da Requisição
                        </span>
                        <div class="flex-grow-1 border-top"></div>
                    </div>

                    {{-- Predefinições rápidas --}}
                    <div id="predefPanel" class="mb-3" style="display:none;">
                        <div class="predef-panel">
                            <div class="predef-header">
                                <h6><i class="fas fa-layer-group me-2"></i>Materiais Predefinidos — clique para adicionar</h6>
                                <button type="button" class="btn-close btn-close-white btn-sm" id="btnClosePredef"></button>
                            </div>
                            <div id="predefList" style="max-height:180px;overflow-y:auto;"></div>
                        </div>
                    </div>

                    <div class="table-responsive mb-2">
                        <table class="table table-bordered table-sm mb-0" id="tblItems">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:35%">
                                        Descrição <span class="text-danger">*</span>
                                        <button type="button" class="btn btn-xs btn-outline-secondary ms-1" id="btnShowPredef"
                                            style="font-size:.65rem;padding:1px 6px;border-radius:4px;" title="Inserir predefinição">
                                            <i class="fas fa-layer-group"></i> Predefinições
                                        </button>
                                    </th>
                                    <th style="width:13%">Quantidade <span class="text-danger">*</span></th>
                                    <th style="width:10%">Unidade <span class="text-danger">*</span></th>
                                    <th style="width:17%">Preço Unit. (MT) <span class="text-danger">*</span></th>
                                    <th style="width:17%" class="text-end">Subtotal (MT)</th>
                                    <th style="width:8%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold pe-2">TOTAL GERAL</td>
                                    <td class="text-end fw-bold" style="color:#c60a1a;" id="totalGeral">0,00 MT</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="btnAddItem">
                        <i class="fas fa-plus-circle me-1"></i> Adicionar Item
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger px-4" id="btnSalvar">
                    <i class="fas fa-paper-plane me-1"></i> Emitir Requisição
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL: CONFIRMAR CARGA (Peso + Valor)
═══════════════════════════════════════ --}}
<div class="modal fade" id="modalConfirmarCarga" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="carga-header">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:44px;height:44px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-weight-hanging text-white" style="font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <h5 class="text-white mb-0" style="font-size:1rem;font-weight:700;">Confirmação de Carga</h5>
                        <div class="text-white-50" style="font-size:.78rem;" id="cargaModalSub">Requisição —</div>
                    </div>
                </div>
                {{-- mini workflow --}}
                <div class="d-flex align-items-center gap-2 mt-2">
                    <div style="flex:1;text-align:center;">
                        <div style="width:26px;height:26px;background:#16a34a;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;color:#fff;font-weight:700;"><i class="fas fa-check"></i></div>
                        <div style="font-size:.6rem;color:rgba(255,255,255,.6);margin-top:3px;text-transform:uppercase;letter-spacing:.5px;">Emitida</div>
                    </div>
                    <div style="flex:1;height:2px;background:rgba(255,255,255,.2);"></div>
                    <div style="flex:1;text-align:center;">
                        <div style="width:26px;height:26px;background:#c60a1a;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;color:#fff;font-weight:700;">2</div>
                        <div style="font-size:.6rem;color:rgba(255,255,255,.9);margin-top:3px;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Confirmar</div>
                    </div>
                    <div style="flex:1;height:2px;background:rgba(255,255,255,.2);"></div>
                    <div style="flex:1;text-align:center;">
                        <div style="width:26px;height:26px;background:rgba(255,255,255,.15);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;color:rgba(255,255,255,.5);font-weight:700;">3</div>
                        <div style="font-size:.6rem;color:rgba(255,255,255,.4);margin-top:3px;text-transform:uppercase;letter-spacing:.5px;">Finalizada</div>
                    </div>
                </div>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="carga_req_id">

                <div class="carga-step mb-3">
                    <div class="carga-step-num">1</div>
                    <div class="carga-step-body">
                        <div class="carga-step-title">Peso Real da Carga</div>
                        <div class="carga-step-desc">Introduza o peso total verificado após carregamento do veículo.</div>
                        <div class="carga-field mt-2" style="position:relative;">
                            <label>Peso (kg)</label>
                            <input type="number" id="carga_peso" step="0.001" min="0"
                                placeholder="Ex: 1250.500">
                            <span class="unit-badge">kg</span>
                        </div>
                    </div>
                </div>

                <div class="carga-step mb-3">
                    <div class="carga-step-num">2</div>
                    <div class="carga-step-body">
                        <div class="carga-step-title">Valor Real da Carga (MT)</div>
                        <div class="carga-step-desc">Valor final confirmado da carga expedida (pode diferir do total estimado dos itens).</div>
                        <div class="carga-field mt-2" style="position:relative;">
                            <label>Valor da Carga (MT)</label>
                            <input type="number" id="carga_valor" step="0.01" min="0"
                                placeholder="Ex: 125000.00">
                            <span class="unit-badge">MT</span>
                        </div>
                    </div>
                </div>

                <div class="carga-step" style="background:#f0fdf4;border-color:#bbf7d0;">
                    <div class="carga-step-num" style="background:#16a34a;">3</div>
                    <div class="carga-step-body">
                        <div class="carga-step-title" style="color:#15803d;">Finalizar Requisição</div>
                        <div class="carga-step-desc">Ao confirmar, o estado passa a <strong>FINALIZADA</strong> e a requisição fica encerrada. Esta acção não pode ser desfeita.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e9ecef;padding:16px 24px;">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-finalizar" id="btnFinalizar">
                    <i class="fas fa-check-double"></i> Confirmar e Finalizar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL: GERIR PREDEFINIÇÕES
═══════════════════════════════════════ --}}
<div class="modal fade" id="modalPredef" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#0f172a;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-layer-group me-2"></i>Gerir Materiais Predefinidos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Crie descrições de materiais frequentes para inserir rapidamente nas requisições.
                    As predefinições são partilhadas por todos os utilizadores.
                </p>
                <div class="row g-2 mb-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Descrição do Material</label>
                        <input type="text" class="form-control form-control-sm" id="predefNome" placeholder="Ex: Cimento Portland 50kg">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold">Unidade padrão</label>
                        <select class="form-select form-select-sm" id="predefUnidade">
                            <option value="un">un</option>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="mg">mg</option>
                            <option value="t">t</option>
                            <option value="m">m</option>
                            <option value="m²">m²</option>
                            <option value="m³">m³</option>
                            <option value="l">l</option>
                            <option value="ml">ml</option>
                            <option value="cx">cx</option>
                            <option value="pc">pc</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Preço Unit. padrão (MT)</label>
                        <input type="number" class="form-control form-control-sm" id="predefPreco" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-sm btn-danger w-100" id="btnSalvarPredef">
                            <i class="fas fa-plus me-1"></i> Adicionar Predefinição
                        </button>
                    </div>
                </div>
                <hr>
                <div id="predefManageList">
                    <div class="text-center text-muted py-3 small" id="predefEmptyMsg">
                        <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                        Ainda não há predefinições criadas.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     TOAST
═══════════════════════════════════════ --}}
<div class="toast-req" id="toastSuccess">
    <div class="t-icon"><i class="fas fa-check" id="toastIcon"></i></div>
    <div class="t-text">
        <div class="t-title" id="toastTitle">Operação concluída</div>
        <div class="t-sub" id="toastSub">Requisição guardada com sucesso.</div>
    </div>
    <button class="t-close" onclick="closeToast()"><i class="fas fa-times"></i></button>
</div>

{{-- ═══════════════════════════════════════
     CONFIRM DELETE OVERLAY
═══════════════════════════════════════ --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-header">
            <div class="confirm-icon"><i class="fas fa-trash"></i></div>
            <div class="confirm-title">Eliminar requisição?</div>
            <div class="confirm-sub">Tens a certeza que queres eliminar <strong id="confirmLabel"></strong>?</div>
        </div>
        <div class="confirm-body">
            <div class="confirm-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Esta acção é irreversível e não pode ser desfeita.
            </div>
            <div class="confirm-actions">
                <button class="btn-cancel-confirm" onclick="closeConfirm()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="btn-delete-confirm" id="confirmOkBtn">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
// ════════════════════════════════════════════
// DataTable
// ════════════════════════════════════════════
const table = $('#tblRequisicoes').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
    order: [[0, 'desc']],
    pageLength: 15,
    columnDefs: [{ orderable: false, targets: -1 }],
});

// ════════════════════════════════════════════
// Filtros — via DataTables ext.search (funciona com paginação)
// ════════════════════════════════════════════
$.fn.dataTable.ext.search.push(function (settings, _data, dataIndex) {
    if (settings.nTable.id !== 'tblRequisicoes') return true;

    const node = $(table.row(dataIndex).node());
    const di   = $('#filtroDataInicio').val();
    const df   = $('#filtroDataFim').val();
    const dest = $('#filtroDestino').val().toLowerCase().trim();
    const st   = $('#filtroStatus').val();
    const num  = $('#filtroNumero').val().trim().replace(/^#/, '').replace(/^0+/, '');
    const mot  = $('#filtroMotorista').val().toLowerCase().trim();
    const mat  = $('#filtroMatricula').val().toLowerCase().trim();
    const resp = $('#filtroResponsavel').val().toLowerCase().trim();
    const forn = $('#filtroFornecedor').val().toLowerCase().trim();

    if (di   && String(node.data('date'))        < di)                                       return false;
    if (df   && String(node.data('date'))        > df)                                       return false;
    if (st   && node.data('status')             !== st)                                      return false;
    if (num  && !String(node.data('req-id')).includes(num))                                  return false;
    if (dest && !String(node.data('destino')).toLowerCase().includes(dest))                  return false;
    if (forn && !String(node.data('fornecedor')).toLowerCase().includes(forn))               return false;
    if (mot  && !String(node.data('motorista')).toLowerCase().includes(mot))                 return false;
    if (mat  && !String(node.data('matricula')).toLowerCase().includes(mat))                 return false;
    if (resp && !String(node.data('responsavel')).toLowerCase().includes(resp))              return false;

    return true;
});

function aplicarFiltros() {
    table.draw();

    // contar linhas filtradas (todas as páginas)
    const count = table.rows({ filter: 'applied' }).count();

    const f = {
        di:   $('#filtroDataInicio').val(),
        df:   $('#filtroDataFim').val(),
        dest: $('#filtroDestino').val().trim(),
        st:   $('#filtroStatus').val(),
        num:  $('#filtroNumero').val().trim(),
        mot:  $('#filtroMotorista').val().trim(),
        mat:  $('#filtroMatricula').val().trim(),
        resp: $('#filtroResponsavel').val().trim(),
        forn: $('#filtroFornecedor').val(),
    };
    const algumActivo = Object.values(f).some(v => v !== '');

    $('#extractCount').text(count);
    const partes = [];
    if (f.num)  partes.push('Nº: ' + f.num);
    if (f.di)   partes.push('De '  + f.di.split('-').reverse().join('/'));
    if (f.df)   partes.push('até ' + f.df.split('-').reverse().join('/'));
    if (f.dest) partes.push('Destino: "' + f.dest + '"');
    if (f.forn) partes.push('Fornecedor: "' + f.forn + '"');
    if (f.mot)  partes.push('Motorista: "' + f.mot + '"');
    if (f.mat)  partes.push('Matrícula: "' + f.mat + '"');
    if (f.resp) partes.push('Responsável: "' + f.resp + '"');
    if (f.st)   partes.push('Estado: ' + f.st);
    $('#extractLabel').text(partes.length ? ' — ' + partes.join(' | ') : '');
    $('#extractBar').toggleClass('visible', algumActivo && count > 0);
}

$('#filtroDataInicio, #filtroDataFim, #filtroDestino, #filtroStatus, ' +
  '#filtroNumero, #filtroMotorista, #filtroMatricula, #filtroResponsavel, #filtroFornecedor')
    .on('input change', aplicarFiltros);

$('#btnLimparFiltros').on('click', function () {
    $('#filtroDataInicio, #filtroDataFim, #filtroDestino, ' +
      '#filtroNumero, #filtroMotorista, #filtroMatricula, #filtroResponsavel').val('');
    $('#filtroStatus, #filtroFornecedor').val('');
    table.draw();
    $('#extractBar').removeClass('visible');
});

// ════════════════════════════════════════════
// Gerar Extrato PDF
// ════════════════════════════════════════════
function gerarExtratoPDF() {
    // lê TODAS as linhas filtradas, não só a página actual
    const rows = [];
    table.rows({ filter: 'applied' }).every(function () {
        const r = $(this.node());
        rows.push({
            id:          r.data('req-id'),
            data:        r.data('date-fmt'),
            destino:     r.data('destino-fmt'),
            fornecedor:  r.data('fornecedor'),
            motorista:   r.data('motorista'),
            matricula:   r.data('matricula'),
            responsavel: r.data('responsavel'),
            status:      r.data('status'),
            total:       r.data('total'),
            peso:        r.data('peso') || '—',
        });
    });
    if (!rows.length) return;

    const di   = $('#filtroDataInicio').val();
    const df   = $('#filtroDataFim').val();
    const dest = $('#filtroDestino').val();
    const st   = $('#filtroStatus').val();
    const num  = $('#filtroNumero').val();
    const mot  = $('#filtroMotorista').val();
    const mat  = $('#filtroMatricula').val();
    const resp = $('#filtroResponsavel').val();
    const forn = $('#filtroFornecedor').val();

    const periodoStr = (di || df)
        ? (di ? di.split('-').reverse().join('/') : '—') + ' a ' + (df ? df.split('-').reverse().join('/') : '—')
        : 'Todo o período';

    const filtrosTexto = [
        num  ? 'Nº: ' + num           : null,
        dest ? 'Destino: "' + dest + '"' : null,
        forn ? 'Fornecedor: "' + forn + '"' : null,
        mot  ? 'Motorista: "' + mot + '"' : null,
        mat  ? 'Matrícula: "' + mat + '"' : null,
        resp ? 'Responsável: "' + resp + '"' : null,
        st   ? 'Estado: ' + st        : null,
        (di || df) ? 'Período: ' + periodoStr : null,
    ].filter(Boolean).join(' | ') || 'Todos os registos';

    function parseVal(str) {
        return parseFloat(String(str).replace(/\./g, '').replace(',', '.')) || 0;
    }
    const sumTotal = rows.reduce((a, r) => a + parseVal(r.total), 0);
    const fmt = n => n.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
    const dataHoje  = new Date().toLocaleDateString('pt-PT');
    const horaAgora = new Date().toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });

    const statusBadge = s => {
        const map = { EMITIDA: '#0369a1', CONFIRMADA: '#a16207', FINALIZADA: '#15803d', CANCELADO: '#dc2626' };
        return `<span style="background:${map[s]||'#64748b'};color:#fff;padding:2px 8px;border-radius:10px;font-size:9px;font-weight:700;">${s}</span>`;
    };

    const linhas = rows.map((r, i) => `
        <tr>
            <td class="center">${i + 1}</td>
            <td><strong style="color:#c60a1a;">#${String(r.id).padStart(4,'0')}</strong></td>
            <td>${r.data}</td>
            <td>${r.destino}</td>
            <td>${r.fornecedor}</td>
            <td>${r.motorista}</td>
            <td>${r.matricula}</td>
            <td>${r.responsavel}</td>
            <td class="center">${statusBadge(r.status)}</td>
            <td class="right">${r.peso} kg</td>
            <td class="right"><strong>${r.total} MT</strong></td>
        </tr>`).join('');

    const html = `<!DOCTYPE html>
<html lang="pt"><head><meta charset="UTF-8">
<title>Extrato de Requisições</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Arial,sans-serif;font-size:12px;color:#222;padding:30px}
.header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;border-bottom:3px solid #c60a1a;padding-bottom:16px}
.company-name{font-size:13px;font-weight:bold;color:#c60a1a}
.company-detail{font-size:9px;color:#555;margin-top:3px;line-height:1.6}
.doc-block h1{font-size:18px;font-weight:bold;color:#c60a1a;text-align:right}
.doc-block .sub{font-size:10px;color:#888;text-align:right;margin-top:4px}
.filter-summary{display:flex;gap:0;margin-bottom:20px;border:1px solid #e8edf2;border-radius:4px;overflow:hidden}
.fs-item{flex:1;padding:10px 14px;background:#fdf2f2;border-right:1px solid #e8edf2;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.fs-item:last-child{border-right:none}
.fs-label{font-size:8px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#888;margin-bottom:3px}
.fs-value{font-size:11px;font-weight:bold;color:#222}
table{width:100%;border-collapse:collapse;margin-bottom:16px}
thead tr{background:#c60a1a;-webkit-print-color-adjust:exact;print-color-adjust:exact}
thead th{padding:8px 10px;font-size:9px;font-weight:bold;letter-spacing:.8px;text-transform:uppercase;color:#fff;text-align:left}
thead th.right,td.right{text-align:right}
thead th.center,td.center{text-align:center}
tbody tr{border-bottom:1px solid #e8edf2}
tbody tr:nth-child(even){background:#fdf5f5;-webkit-print-color-adjust:exact;print-color-adjust:exact}
tbody td{padding:8px 10px;font-size:11px}
.totals-wrap{width:280px;margin-left:auto;margin-bottom:24px}
.totals-wrap tr.grand{background:#c60a1a;color:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.totals-wrap tr.grand td{padding:8px 10px;font-size:13px;font-weight:bold}
.page-footer{margin-top:32px;border-top:2px solid #c60a1a;padding-top:14px;display:flex;justify-content:space-between}
@media print{body{padding:20px}@page{margin:12mm;size:A4 landscape}}
</style></head><body>
<div class="header">
    <div>
        <div class="company-name">Fábrica de Explosivos de Moçambique</div>
        <div class="company-detail">Contribuinte Nº 400019029<br>Av. Samora Machel Nº — Parcela 10<br>Telef. +258 21 745 86/03 | FAX. +258 21 745 802</div>
    </div>
    <div class="doc-block">
        <h1>EXTRATO DE REQUISIÇÕES DE MATERIAL</h1>
        <div class="sub">Emitido em ${dataHoje} às ${horaAgora}</div>
    </div>
</div>
<div class="filter-summary">
    <div class="fs-item" style="flex:2;"><div class="fs-label">Filtros Aplicados</div><div class="fs-value">${filtrosTexto}</div></div>
    <div class="fs-item" style="flex:0 0 160px;"><div class="fs-label">Período</div><div class="fs-value">${periodoStr}</div></div>
    <div class="fs-item" style="flex:0 0 120px;"><div class="fs-label">Nº Requisições</div><div class="fs-value">${rows.length}</div></div>
</div>
<table>
    <thead><tr>
        <th class="center" style="width:3%">#</th>
        <th style="width:6%">Nº</th>
        <th style="width:7%">Data</th>
        <th style="width:14%">Destino</th>
        <th style="width:14%">Fornecedor</th>
        <th style="width:11%">Motorista</th>
        <th style="width:9%">Matrícula</th>
        <th style="width:11%">Responsável</th>
        <th class="center" style="width:10%">Estado</th>
        <th class="right" style="width:8%">Peso (kg)</th>
        <th class="right" style="width:11%">Total (MT)</th>
    </tr></thead>
    <tbody>${linhas}</tbody>
</table>
<div class="totals-wrap">
    <table><tr class="grand"><td>TOTAL GERAL</td><td class="right">${fmt(sumTotal)}</td></tr></table>
</div>
<div class="page-footer">
    <div>
        <div style="font-size:8px;text-transform:uppercase;color:#64748b;">Gerado por</div>
        <div style="font-size:10px;font-weight:bold;">{{ auth()->user()->name }}</div>
        <div style="font-size:9px;color:#64748b;">{{ auth()->user()->email }}</div>
    </div>
    <div style="font-size:9px;color:#94a3b8;text-align:right;">Documento gerado automaticamente.<br>Não requer assinatura.</div>
</div>
<script>window.onload=function(){window.print();}<\/script>
</body></html>`;

    const win = window.open('', '_blank', 'width=1200,height=800');
    win.document.write(html);
    win.document.close();
}

// ════════════════════════════════════════════
// Predefinições — localStorage
// ════════════════════════════════════════════
function loadPredef() {
    try { return JSON.parse(localStorage.getItem('req_predef') || '[]'); }
    catch (e) { return []; }
}
function savePredef(arr) { localStorage.setItem('req_predef', JSON.stringify(arr)); }

function renderPredefManage() {
    const arr  = loadPredef();
    const wrap = $('#predefManageList');
    $('#predefEmptyMsg').toggle(arr.length === 0);
    wrap.find('.predef-manage-item').remove();
    arr.forEach((p, i) => {
        wrap.append(`
            <div class="predef-manage-item">
                <div style="flex:1;">
                    <div style="font-size:.82rem;font-weight:600;color:#1e293b;">${p.nome}</div>
                    <div style="font-size:.72rem;color:#94a3b8;">${p.unidade} · ${Number(p.preco).toLocaleString('pt-PT',{minimumFractionDigits:2})} MT</div>
                </div>
                <button class="btn btn-xs btn-outline-danger btn-del-predef" data-idx="${i}"
                    style="font-size:.7rem;padding:3px 8px;border-radius:5px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`);
    });
}
function renderPredefPanel() {
    const arr  = loadPredef();
    const list = $('#predefList');
    list.empty();
    if (!arr.length) {
        list.html('<div class="text-center text-muted py-3 small"><i class="fas fa-inbox fa-2x mb-1 d-block opacity-25"></i>Sem predefinições.</div>');
        return;
    }
    arr.forEach((p, i) => {
        list.append(`
            <div class="predef-item" data-idx="${i}">
                <div class="predef-item-name">${p.nome}</div>
                <div class="predef-item-details">${p.unidade} · ${Number(p.preco).toLocaleString('pt-PT',{minimumFractionDigits:2})} MT</div>
                <div class="predef-item-add"><i class="fas fa-plus"></i></div>
            </div>`);
    });
}

$('#btnSalvarPredef').on('click', function () {
    const nome    = $('#predefNome').val().trim();
    const unidade = $('#predefUnidade').val();
    const preco   = $('#predefPreco').val();
    if (!nome) return alert('Introduz o nome do material.');
    const arr = loadPredef();
    arr.push({ nome, unidade, preco: preco || '0' });
    savePredef(arr);
    $('#predefNome').val(''); $('#predefPreco').val('');
    renderPredefManage(); renderPredefPanel();
    showToast('Predefinição adicionada.', false);
});

$(document).on('click', '.btn-del-predef', function () {
    const arr = loadPredef();
    arr.splice(parseInt($(this).data('idx')), 1);
    savePredef(arr);
    renderPredefManage(); renderPredefPanel();
});

$(document).on('click', '#predefList .predef-item', function () {
    const p = loadPredef()[parseInt($(this).data('idx'))];
    if (!p) return;
    $('#itemsBody').append(itemRow({ description: p.nome, unit: p.unidade, unit_price: p.preco }));
    recalcTotal();
    $('#predefPanel').hide();
});

$('#btnShowPredef').on('click', function () { renderPredefPanel(); $('#predefPanel').toggle(); });
$('#btnClosePredef').on('click', function () { $('#predefPanel').hide(); });
$('#btnGerir').on('click', function () { renderPredefManage(); new bootstrap.Modal('#modalPredef').show(); });

// ════════════════════════════════════════════
// Itens do modal
// ════════════════════════════════════════════
const UNITS = ['kg','g','mg','t','m','cm','mm','km','m²','m³','l','ml','un','cx','pc'];

function itemRow(item = {}) {
    const opts = UNITS.map(u =>
        `<option value="${u}" ${item.unit === u ? 'selected' : ''}>${u}</option>`
    ).join('');
    return `<tr>
        <td><input type="text" class="form-control form-control-sm item-desc"
            value="${item.description ?? ''}" placeholder="Descrição do material" required></td>
        <td><input type="number" class="form-control form-control-sm item-qty"
            step="0.001" min="0.001" value="${item.quantity ?? ''}" required></td>
        <td><select class="form-select form-select-sm item-unit">${opts}</select></td>
        <td><input type="number" class="form-control form-control-sm item-price"
            step="0.01" min="0" value="${item.unit_price ?? ''}" required></td>
        <td class="text-end align-middle fw-semibold item-subtotal">0,00</td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
}

function recalcTotal() {
    let total = 0;
    $('#itemsBody tr').each(function () {
        const qty   = parseFloat($(this).find('.item-qty').val())   || 0;
        const price = parseFloat($(this).find('.item-price').val()) || 0;
        const sub   = qty * price;
        $(this).find('.item-subtotal').text(sub.toLocaleString('pt-PT', { minimumFractionDigits: 2 }));
        total += sub;
    });
    $('#totalGeral').text(total.toLocaleString('pt-PT', { minimumFractionDigits: 2 }) + ' MT');
}

$(document).on('input', '.item-qty, .item-price', recalcTotal);
$(document).on('click', '.btn-remove-item', function () {
    if ($('#itemsBody tr').length > 1) {
        $(this).closest('tr').remove();
        recalcTotal();
    } else {
        alert('A requisição precisa de pelo menos um item.');
    }
});
$('#btnAddItem').on('click', function () { $('#itemsBody').append(itemRow()); $('#predefPanel').hide(); });

// ════════════════════════════════════════════
// Modal Nova Requisição
// ════════════════════════════════════════════
$('#btnNova').on('click', function () {
    $('#modalTitle').text('Nova Requisição de Material');
    $('#formRequisicao')[0].reset();
    $('#req_id').val('');
    $('#req_supplier_id').val('');
    $('#req_date').val(new Date().toISOString().split('T')[0]);
    $('#statusWrap').addClass('d-none');
    $('#predefPanel').hide();
    $('#itemsBody').html(itemRow());
    recalcTotal();
    $('#btnSalvar').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Emitir Requisição');
    new bootstrap.Modal('#modalRequisicao').show();
});

// ════════════════════════════════════════════
// Modal Editar
// ════════════════════════════════════════════
$(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    $.ajax({
        url: `/requisicoes-material/${id}`,
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        success(data) {
            $('#modalTitle').text('Editar Requisição #' + String(data.id).padStart(4, '0'));
            $('#req_id').val(data.id);
            $('#req_date').val(data.date);
            $('#req_destino').val(data.destino);
            $('#req_supplier_id').val(data.supplier_id ?? '');
            $('#req_motorista').val(data.motorista ?? '');
            $('#req_matricula').val(data.matricula ?? '');
            $('#req_responsavel').val(data.responsavel ?? '');
            $('#req_observacoes').val(data.observacoes ?? '');
            $('#req_status').val(data.status);
            $('#statusWrap').removeClass('d-none');
            $('#predefPanel').hide();
            $('#itemsBody').empty();
            (data.items ?? []).forEach(item => $('#itemsBody').append(itemRow(item)));
            recalcTotal();
            $('#btnSalvar').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar Alterações');
            new bootstrap.Modal('#modalRequisicao').show();
        },
        error(xhr) {
            alert('Erro ao carregar requisição:\n' + (xhr.responseJSON?.message ?? 'Erro desconhecido.'));
        },
    });
});

// ════════════════════════════════════════════
// Guardar / Emitir
// ════════════════════════════════════════════
$('#btnSalvar').on('click', function () {
    if (!$('#req_date').val() || !$('#req_destino').val().trim())
        return alert('Preenche os campos obrigatórios: Data e Destino.');

    const items = [];
    let valid = true;
    $('#itemsBody tr').each(function () {
        const desc  = $(this).find('.item-desc').val().trim();
        const qty   = $(this).find('.item-qty').val();
        const unit  = $(this).find('.item-unit').val();
        const price = $(this).find('.item-price').val();
        if (!desc || !qty || !price) { valid = false; return false; }
        items.push({ description: desc, quantity: qty, unit, unit_price: price });
    });
    if (!valid || !items.length) return alert('Preenche todos os campos dos itens.');

    const id = $('#req_id').val();
    const payload = {
        _token:      '{{ csrf_token() }}',
        date:        $('#req_date').val(),
        destino:     $('#req_destino').val().trim(),
        supplier_id: $('#req_supplier_id').val() || null,
        motorista:   $('#req_motorista').val(),
        matricula:   $('#req_matricula').val(),
        responsavel: $('#req_responsavel').val(),
        observacoes: $('#req_observacoes').val(),
        status:      $('#statusWrap').hasClass('d-none') ? 'EMITIDA' : ($('#req_status').val() || 'EMITIDA'),
        items,
    };
    if (id) payload._method = 'PUT';

    const url = id ? `/requisicoes-material/${id}` : '{{ route("requisicoes-material.store") }}';

    $('#btnSalvar').prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span> A guardar...');

    $.ajax({
        url, method: 'POST', data: payload,
        success() {
            bootstrap.Modal.getInstance('#modalRequisicao').hide();
            showToast(id ? 'Requisição actualizada.' : 'Requisição emitida com sucesso.', false);
            setTimeout(() => location.reload(), 1500);
        },
        error(xhr) {
            const errs = xhr.responseJSON?.errors;
            alert('Erro:\n' + (errs
                ? Object.values(errs).flat().join('\n')
                : xhr.responseJSON?.message ?? 'Erro desconhecido.'));
            $('#btnSalvar').prop('disabled', false)
                .html('<i class="fas fa-paper-plane me-1"></i> Emitir Requisição');
        },
    });
});

// ════════════════════════════════════════════
// Modal Confirmar Carga
// ════════════════════════════════════════════
$(document).on('click', '.btn-confirm-carga', function () {
    $('#carga_req_id').val($(this).data('id'));
    $('#cargaModalSub').text('Requisição ' + $(this).data('num') + ' — ' + $(this).data('destino'));
    $('#carga_peso, #carga_valor').val('');
    $('#btnFinalizar').prop('disabled', false)
        .html('<i class="fas fa-check-double"></i> Confirmar e Finalizar');
    new bootstrap.Modal('#modalConfirmarCarga').show();
});

$('#btnFinalizar').on('click', function () {
    const peso  = $('#carga_peso').val();
    const valor = $('#carga_valor').val();
    if (!peso || !valor) return alert('Preenche o peso e o valor da carga antes de finalizar.');

    $(this).prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span> A finalizar...');

    $.ajax({
        url:    `/requisicoes-material/${$('#carga_req_id').val()}/confirmar-carga`,
        method: 'POST',
        data:   { _token: '{{ csrf_token() }}', peso_confirmado: peso, valor_carga: valor },
        success() {
            bootstrap.Modal.getInstance('#modalConfirmarCarga').hide();
            showToast('Requisição finalizada com sucesso!', false);
            setTimeout(() => location.reload(), 1500);
        },
        error(xhr) {
            alert('Erro ao finalizar:\n' + (xhr.responseJSON?.message ?? 'Erro desconhecido.'));
            $('#btnFinalizar').prop('disabled', false)
                .html('<i class="fas fa-check-double"></i> Confirmar e Finalizar');
        },
    });
});

// ════════════════════════════════════════════
// Eliminar
// ════════════════════════════════════════════
let _deleteId = null;

$(document).on('click', '.btn-delete', function () {
    _deleteId = $(this).data('id');
    $('#confirmLabel').text($(this).data('num'));
    $('#confirmOkBtn').prop('disabled', false).html('<i class="fas fa-trash"></i> Eliminar');
    $('#confirmOverlay').addClass('open');
});

$('#confirmOkBtn').on('click', function () {
    $(this).prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span> A eliminar...');

    $.ajax({
        url:    `/requisicoes-material/${_deleteId}`,
        method: 'POST',
        data:   { _token: '{{ csrf_token() }}', _method: 'DELETE' },
        success() {
            closeConfirm();
            // remove a linha do DataTable sem recarregar a página
            table.row(`[data-req-id="${_deleteId}"]`).remove().draw();
            showToast('Requisição eliminada com sucesso.', false);
        },
        error() {
            closeConfirm();
            showToast('Erro ao eliminar a requisição.', true);
        },
    });
});

$('#confirmOverlay').on('click', function (e) { if (e.target === this) closeConfirm(); });
$(document).on('keydown', e => { if (e.key === 'Escape') closeConfirm(); });
function closeConfirm() { $('#confirmOverlay').removeClass('open'); }

// ════════════════════════════════════════════
// Toast
// ════════════════════════════════════════════
function showToast(msg, isError = false) {
    const toast = $('#toastSuccess');
    toast.toggleClass('error', isError);
    $('#toastIcon').attr('class', isError ? 'fas fa-times' : 'fas fa-check');
    $('#toastTitle').text(isError ? 'Ocorreu um erro' : 'Operação concluída');
    $('#toastSub').text(msg);
    toast.addClass('show');
    setTimeout(closeToast, 4000);
}
function closeToast() { $('#toastSuccess').removeClass('show'); }
</script>
@endpush