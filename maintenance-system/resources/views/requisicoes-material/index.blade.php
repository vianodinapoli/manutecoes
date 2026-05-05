@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
/* ── Design tokens FEM ── */
:root {
    --fem-dark:    #0f172a;
    --fem-dark-2:  #1e293b;
    --fem-red:     #c60a1a;
    --fem-red-dk:  #a00817;
    --fem-amber:   #d97706;
    --fem-green:   #16a34a;
    --fem-blue:    #0ea5e9;
    --fem-border:  #e9ecef;
    --fem-muted:   #adb5bd;
    --fem-shadow:  0 2px 8px rgba(0,0,0,.06);
    --fem-radius:  14px;
    --fem-radius-sm: 8px;
}

/* ── Table card ── */
.table-card { background:#fff; border-radius:var(--fem-radius); border:1px solid var(--fem-border); overflow:hidden; box-shadow:var(--fem-shadow); }
#tblRequisicoes thead tr { background:#f8f9fa; }
#tblRequisicoes thead th { font-size:.67rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#868e96; border-bottom:1px solid var(--fem-border); padding:8px 12px; white-space:nowrap; }
#tblRequisicoes tbody td { padding:9px 12px; vertical-align:middle; border-bottom:1px solid #f1f3f5; font-size:.82rem; }
#tblRequisicoes tbody tr:hover { background:#fff5f5; }
#tblRequisicoes tbody tr:last-child td { border-bottom:none; }

/* ── KPI cards ── */
.kpi-card { background:#fff; border-radius:var(--fem-radius); border:1px solid var(--fem-border); padding:16px 20px; box-shadow:var(--fem-shadow); transition:transform .15s,box-shadow .15s; height:100%; position:relative; overflow:hidden; }
.kpi-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08); }
.kpi-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:var(--fem-radius) 0 0 var(--fem-radius); }
.kpi-card.red::before   { background:var(--fem-red); }
.kpi-card.amber::before { background:var(--fem-amber); }
.kpi-card.green::before { background:var(--fem-green); }
.kpi-card.blue::before  { background:var(--fem-blue); }
.kpi-card.dark::before  { background:var(--fem-dark); }
.kpi-label     { font-size:.65rem; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:var(--fem-muted); margin-bottom:6px; }
.kpi-value     { font-size:1.7rem; font-weight:800; line-height:1; margin-bottom:2px; }
.kpi-value-sm  { font-size:1.05rem; font-weight:800; line-height:1; margin-bottom:2px; padding-top:4px; }
.kpi-sub       { font-size:.72rem; color:var(--fem-muted); }
.kpi-icon      { position:absolute; right:16px; top:50%; transform:translateY(-50%); font-size:2rem; opacity:.08; }

/* ── Status badges ── */
.badge-status    { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:.68rem; font-weight:700; letter-spacing:.5px; text-transform:uppercase; }
.badge-emitida   { background:#e0f2fe; color:#0369a1; }
.badge-confirmada{ background:#fef9c3; color:#a16207; }
.badge-finalizada{ background:#dcfce7; color:#15803d; }
.badge-cancelado { background:#fee2e2; color:#dc2626; }

/* ── Filter panel ── */
.filter-panel { background:#fff; border-radius:var(--fem-radius); border:1px solid var(--fem-border); padding:20px 24px; margin-bottom:24px; box-shadow:var(--fem-shadow); }
.filter-title  { font-size:.72rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--fem-muted); margin-bottom:14px; display:flex; align-items:center; gap:8px; }
.filter-group  { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; }
.filter-item   { display:flex; flex-direction:column; gap:5px; }
.filter-item label { font-size:.7rem; font-weight:600; letter-spacing:.8px; text-transform:uppercase; color:#6c757d; }
.filter-item select,
.filter-item input { border:1px solid #dee2e6; border-radius:var(--fem-radius-sm); padding:7px 12px; font-size:.82rem; color:#343a40; background:#f8f9fa; outline:none; transition:border-color .2s,box-shadow .2s; min-width:150px; }
.filter-item select:focus,
.filter-item input:focus { border-color:var(--fem-red); box-shadow:0 0 0 3px rgba(198,10,26,.08); background:#fff; }
.btn-filter       { padding:8px 18px; border-radius:var(--fem-radius-sm); font-size:.8rem; font-weight:600; cursor:pointer; border:none; display:inline-flex; align-items:center; gap:6px; transition:all .2s; }
.btn-filter-clear { background:#f1f5f9; color:#495057; border:1px solid #dee2e6; } .btn-filter-clear:hover { background:#e9ecef; }
.btn-filter-pdf   { background:var(--fem-red); color:#fff; } .btn-filter-pdf:hover { background:var(--fem-red-dk); }
.btn-filter-pdf:disabled { background:var(--fem-muted); cursor:not-allowed; }

/* ── Extract bar ── */
#extractBar { display:none; margin-top:14px; padding:12px 16px; background:linear-gradient(135deg,#fff5f5,#fde8e8); border:1px solid #fca5a5; border-radius:10px; align-items:center; gap:12px; flex-wrap:wrap; }
#extractBar.visible { display:flex; }
#extractBar .ext-info { font-size:.78rem; color:#991b1b; font-weight:600; flex:1; }
#extractBar .ext-info span { font-weight:800; }

/* ── Action buttons ── */
.action-btn { width:28px; height:28px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; border:1px solid; transition:all .15s; text-decoration:none; cursor:pointer; background:transparent; }
.action-btn:hover { opacity:.75; }
.action-btn.btn-confirm-carga,
.action-btn.btn-edit-carga { width:auto; padding:0 8px; gap:4px; font-size:.7rem; font-weight:600; }

/* ── Workflow steps indicator ── */
.workflow-steps { display:flex; align-items:center; gap:0; margin-bottom:20px; }
.ws-step { flex:1; text-align:center; position:relative; }
.ws-step:not(:last-child)::after { content:''; position:absolute; right:0; top:50%; transform:translateY(-50%); width:100%; height:2px; background:var(--fem-border); z-index:0; }
.ws-dot   { width:32px; height:32px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; position:relative; z-index:1; border:2px solid var(--fem-border); background:#fff; color:var(--fem-muted); transition:all .3s; }
.ws-dot.active { border-color:var(--fem-red);   background:var(--fem-red);   color:#fff; }
.ws-dot.done   { border-color:var(--fem-green); background:var(--fem-green); color:#fff; }
.ws-label      { font-size:.65rem; font-weight:600; letter-spacing:.5px; text-transform:uppercase; margin-top:5px; color:var(--fem-muted); }
.ws-label.active { color:var(--fem-red); }
.ws-label.done   { color:var(--fem-green); }

/* ════════════════════════════════════════
   MODAIS — Design system FEM unificado
════════════════════════════════════════ */
.fem-modal-header {
    background: linear-gradient(135deg, var(--fem-dark) 0%, var(--fem-dark-2) 100%);
    padding: 22px 28px;
    border-bottom: none;
    position: relative;
    overflow: hidden;
}
.fem-modal-header::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: var(--fem-red);
}
.fem-modal-header .modal-title {
    color: #fff;
    font-size: .95rem;
    font-weight: 700;
    letter-spacing: .3px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.fem-modal-header .modal-title .title-icon {
    width: 36px; height: 36px;
    background: rgba(198,10,26,.25);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    color: #ff8a8a;
    flex-shrink: 0;
}
.fem-modal-header .modal-subtitle {
    font-size: .75rem;
    color: rgba(255,255,255,.5);
    margin-top: 3px;
    padding-left: 46px;
}
.fem-modal-header .btn-close-fem {
    position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
    width: 30px; height: 30px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.7);
    font-size: .8rem;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
}
.fem-modal-header .btn-close-fem:hover { background:rgba(198,10,26,.4); color:#fff; border-color:var(--fem-red); }

.fem-modal-body { padding: 28px; }
.fem-modal-footer {
    padding: 16px 28px;
    border-top: 1px solid var(--fem-border);
    display: flex; justify-content: flex-end; gap: 10px;
    background: #f8fafc;
    border-radius: 0 0 var(--fem-radius) var(--fem-radius);
}

.fem-label {
    font-size: .7rem; font-weight: 700; letter-spacing: .8px;
    text-transform: uppercase; color: #64748b;
    margin-bottom: 5px; display: block;
}
.fem-input {
    border: 1px solid #e2e8f0; border-radius: var(--fem-radius-sm);
    padding: 9px 14px; font-size: .88rem; color: var(--fem-dark);
    width: 100%; outline: none;
    transition: border-color .2s, box-shadow .2s;
    background: #f8fafc;
}
.fem-input:focus { border-color:var(--fem-red); box-shadow:0 0 0 3px rgba(198,10,26,.09); background:#fff; }

.fem-section {
    background: #f8fafc;
    border: 1px solid var(--fem-border);
    border-radius: 10px;
    padding: 16px 18px;
    margin-bottom: 14px;
    position: relative;
}
.fem-section-title {
    font-size: .68rem; font-weight: 700; letter-spacing: 1.2px;
    text-transform: uppercase; color: var(--fem-muted);
    margin-bottom: 14px; display: flex; align-items: center; gap: 7px;
}
.fem-section-num {
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--fem-red); color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .65rem; font-weight: 700; flex-shrink: 0;
}
.fem-section-num.green  { background: var(--fem-green); }
.fem-section-num.amber  { background: var(--fem-amber); }

.fem-btn-primary {
    background: var(--fem-red); color: #fff;
    border: none; border-radius: var(--fem-radius-sm);
    padding: 10px 22px; font-size: .85rem; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
    transition: all .2s; box-shadow: 0 3px 10px rgba(198,10,26,.25);
}
.fem-btn-primary:hover:not(:disabled) { background:var(--fem-red-dk); box-shadow:0 5px 16px rgba(198,10,26,.35); transform:translateY(-1px); }
.fem-btn-primary:disabled { background:#94a3b8; box-shadow:none; cursor:not-allowed; transform:none; }

.fem-btn-success {
    background: linear-gradient(135deg, var(--fem-green), #15803d);
    color: #fff; border: none; border-radius: var(--fem-radius-sm);
    padding: 10px 22px; font-size: .85rem; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
    transition: all .2s; box-shadow: 0 3px 10px rgba(22,163,74,.3);
}
.fem-btn-success:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 6px 20px rgba(22,163,74,.4); }
.fem-btn-success:disabled { background:#94a3b8; box-shadow:none; cursor:not-allowed; transform:none; }

.fem-btn-amber {
    background: linear-gradient(135deg, var(--fem-amber), #b45309);
    color: #fff; border: none; border-radius: var(--fem-radius-sm);
    padding: 10px 22px; font-size: .85rem; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
    transition: all .2s; box-shadow: 0 3px 10px rgba(217,119,6,.3);
}
.fem-btn-amber:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 6px 20px rgba(217,119,6,.4); }
.fem-btn-amber:disabled { background:#94a3b8; box-shadow:none; cursor:not-allowed; transform:none; }

.fem-btn-secondary {
    background: #f1f5f9; color: #475569;
    border: 1px solid #e2e8f0; border-radius: var(--fem-radius-sm);
    padding: 10px 20px; font-size: .85rem; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
    transition: all .15s;
}
.fem-btn-secondary:hover { background:#e2e8f0; }

/* ── Mini-workflow ── */
.mini-workflow { display:flex; align-items:center; gap:0; padding:12px 0 0; }
.mw-step { flex:1; text-align:center; }
.mw-line  { flex:2; height:2px; background:rgba(255,255,255,.15); }
.mw-dot   { width:26px; height:26px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; color:#fff; }
.mw-dot.done   { background:var(--fem-green); }
.mw-dot.active { background:var(--fem-red); }
.mw-dot.next   { background:rgba(255,255,255,.15); color:rgba(255,255,255,.4); }
.mw-label { font-size:.6rem; text-transform:uppercase; letter-spacing:.5px; margin-top:3px; font-weight:600; }
.mw-label.done   { color:rgba(255,255,255,.6); }
.mw-label.active { color:rgba(255,255,255,.9); }
.mw-label.next   { color:rgba(255,255,255,.35); }

/* ── Input com badge de unidade ── */
.input-unit-wrap { position:relative; }
.input-unit-wrap .fem-input { padding-right:44px; }
.input-unit-badge {
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    font-size:.7rem; font-weight:700; color:#94a3b8; pointer-events:none;
    background:#f1f5f9; padding:2px 6px; border-radius:4px;
    border: 1px solid #e2e8f0;
}

/* ── Predefinições panel ── */
.predef-panel { background:#fff; border-radius:var(--fem-radius); border:1px solid var(--fem-border); overflow:hidden; box-shadow:var(--fem-shadow); margin-top:20px; }
.predef-header { background:var(--fem-dark); padding:12px 18px; display:flex; align-items:center; justify-content:space-between; }
.predef-header h6 { color:#fff; margin:0; font-size:.8rem; font-weight:700; letter-spacing:.4px; }
.predef-item { display:flex; align-items:center; gap:10px; padding:10px 14px; border-bottom:1px solid #f1f3f5; cursor:pointer; transition:background .15s; }
.predef-item:last-child { border-bottom:none; }
.predef-item:hover { background:#fff5f5; }
.predef-item-name    { font-size:.8rem; font-weight:600; color:#1e293b; flex:1; }
.predef-item-details { font-size:.72rem; color:#94a3b8; }
.predef-item-add { width:24px; height:24px; border-radius:6px; background:var(--fem-red); color:#fff; display:flex; align-items:center; justify-content:center; font-size:.65rem; flex-shrink:0; transition:transform .15s; }
.predef-item:hover .predef-item-add { transform:scale(1.1); }

/* ── Toast ── */
.toast-req { position:fixed; top:24px; right:24px; z-index:99999; background:#fff; border-radius:12px; padding:16px 20px; display:flex; align-items:center; gap:12px; box-shadow:0 8px 32px rgba(0,0,0,.12); border-left:4px solid var(--fem-green); min-width:300px; transform:translateX(120%); transition:transform .35s cubic-bezier(.34,1.56,.64,1); }
.toast-req.show  { transform:translateX(0); }
.toast-req .t-icon { width:36px; height:36px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--fem-green); font-size:1rem; flex-shrink:0; }
.toast-req.error { border-left-color:#dc2626; }
.toast-req.error .t-icon { background:#fef2f2; color:#dc2626; }
.toast-req .t-text  { flex:1; }
.toast-req .t-title { font-size:.82rem; font-weight:700; color:#1e293b; margin-bottom:2px; }
.toast-req .t-sub   { font-size:.74rem; color:#94a3b8; }
.toast-req .t-close { background:none; border:none; color:#94a3b8; cursor:pointer; font-size:1rem; padding:0; line-height:1; }

/* ── Confirm overlay ── */
.confirm-overlay { position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:9999; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity .25s; backdrop-filter:blur(2px); }
.confirm-overlay.open { opacity:1; pointer-events:all; }
.confirm-box { background:#fff; border-radius:20px; max-width:400px; width:calc(100% - 32px); box-shadow:0 24px 64px rgba(0,0,0,.18); transform:scale(.93) translateY(10px); transition:transform .25s cubic-bezier(.34,1.56,.64,1); overflow:hidden; }
.confirm-overlay.open .confirm-box { transform:scale(1) translateY(0); }
.confirm-header { background:#fef2f2; padding:28px 28px 20px; text-align:center; border-bottom:1px solid #fecaca; }
.confirm-icon   { width:56px; height:56px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; color:#dc2626; margin:0 auto 14px; }
.confirm-title  { font-size:1.05rem; font-weight:700; color:#1e293b; margin-bottom:6px; }
.confirm-sub    { font-size:.82rem; color:#94a3b8; line-height:1.6; }
.confirm-body   { padding:20px 28px 24px; }
.confirm-warning { display:flex; align-items:center; gap:8px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; font-size:.78rem; color:#92400e; margin-bottom:20px; }
.confirm-actions { display:flex; gap:10px; }
.confirm-actions button { flex:1; padding:11px; border-radius:10px; font-size:.82rem; font-weight:600; border:none; cursor:pointer; transition:all .15s; display:flex; align-items:center; justify-content:center; gap:6px; }
.btn-cancel-confirm { background:#f1f5f9; color:#475569; } .btn-cancel-confirm:hover { background:#e2e8f0; }
.btn-delete-confirm { background:#dc2626; color:#fff; box-shadow:0 2px 8px rgba(220,38,38,.3); } .btn-delete-confirm:hover { background:#b91c1c; }
.btn-delete-confirm:disabled { background:#f87171; cursor:not-allowed; box-shadow:none; }

/* ── Gestão predefinições modal ── */
.predef-manage-item { display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #f1f3f5; }
.predef-manage-item:last-child { border-bottom:none; }

/* ── Print ── */
@media print {
    #sidebar,.sb-footer,.sb-brand,nav.sb-nav,.no-print,button,a.btn,
    .filter-panel,.kpi-card,.action-btn { display:none !important; }
    *{ box-sizing:border-box; }
    body,.layout-wrap,main.content-area,.content-body { display:block !important; background:white !important; padding:0 !important; margin:0 !important; font-family:DejaVu Sans,Arial,sans-serif; font-size:11px; color:#222; }
    .print-only { display:block !important; }
    .card { box-shadow:none !important; border:none !important; }
    .print-header { display:flex !important; justify-content:space-between; align-items:flex-start; border-bottom:3px solid var(--fem-red); padding-bottom:14px; margin-bottom:20px; }
    .ph-company-name   { font-size:13px; font-weight:bold; color:var(--fem-red); line-height:1.3; }
    .ph-company-detail { font-size:9px; color:#555; margin-top:3px; line-height:1.6; }
    .ph-doc-title      { text-align:right; }
    .ph-doc-title h2   { font-size:17px; font-weight:bold; color:var(--fem-red); margin:0; }
    .ph-doc-title .ph-sub { font-size:10px; color:#555; margin-top:6px; line-height:1.6; }
    table { width:100%; border-collapse:collapse; margin-bottom:16px; }
    table thead tr { background:var(--fem-red) !important; color:white !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    table thead th { padding:7px 10px; font-size:10px; text-transform:uppercase; text-align:left; }
    table tbody td { padding:7px 10px; border-bottom:1px solid #e8edf2; font-size:10px; }
    table tbody tr:nth-child(even) { background:#fdf5f5 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .print-totals { width:260px; margin-left:auto; margin-bottom:20px; }
    .print-totals table { margin-bottom:0; width:100%; }
    .print-totals .total-row { background:var(--fem-red) !important; color:white !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .print-totals .total-row td { padding:8px 10px; font-size:13px; font-weight:bold; }
    .print-totals .total-row td:last-child { text-align:right; }
    .print-footer { margin-top:28px; border-top:2px solid var(--fem-red); padding-top:12px; }
}
@media screen { .print-only { display:none !important; } }
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
        $total       = $requisicoes->count();
        $emitidas    = $requisicoes->where('status','EMITIDA')->count();
        $confirmadas = $requisicoes->where('status','CONFIRMADA')->count();
        $finalizadas = $requisicoes->where('status','FINALIZADA')->count();
        $valorTotal  = $requisicoes->where('status','FINALIZADA')->sum('total_final');
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
                <div class="kpi-value-sm" id="kpiValorTotal" style="color:#c60a1a;">
                    {{ number_format($valorTotal, 2, ',', '.') }} MT
                </div>
                <div class="kpi-sub">total das requisições finalizadas</div>
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
    <div class="filter-panel no-print">
        <div class="filter-title"><i class="fas fa-filter"></i> Filtros de Pesquisa</div>
        <div class="filter-group">
            <div class="filter-item">
                <label>Nº Requisição</label>
                <input type="text" id="filtroNumero" placeholder="Ex: 0012" style="min-width:110px;">
            </div>
            <div class="filter-item">
                <label>Data Início</label>
                <input type="date" id="filtroDataInicio">
            </div>
            <div class="filter-item">
                <label>Data Fim</label>
                <input type="date" id="filtroDataFim">
            </div>
            <div class="filter-item">
                <label>Destino</label>
                <input type="text" id="filtroDestino" placeholder="Filtrar destino...">
            </div>
            <div class="filter-item">
                <label>Fornecedor</label>
                <select id="filtroFornecedor" style="min-width:170px;">
                    <option value="">Todos</option>
                    @foreach($suppliers as $s)
                    <option value="{{ strtolower($s->name) }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Motorista</label>
                <input type="text" id="filtroMotorista" placeholder="Nome do motorista...">
            </div>
            <div class="filter-item">
                <label>Matrícula</label>
                <input type="text" id="filtroMatricula" placeholder="Ex: MBE-1234-M" style="min-width:130px;">
            </div>
            <div class="filter-item">
                <label>Transportadora</label>
                <input type="text" id="filtroTransportadora" placeholder="Nome da transportadora...">
            </div>
            <div class="filter-item">
                <label>Responsável</label>
                <input type="text" id="filtroResponsavel" placeholder="Responsável...">
            </div>
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

    {{-- TABELA PRINCIPAL --}}
    <div class="table-card">
        <div class="p-3">
            <table id="tblRequisicoes" class="table table-hover align-middle mb-0 w-100">
                <thead>
                    <tr>
                        <th>Nº / DATA</th>
                        <th>FORNECEDOR</th>
                        <th>Nº GUIA</th>
                        <th>CARGA / DESTINO</th>
                        <th>MOTORISTA</th>
                        <th>MATRÍCULA</th>
                        <th>TRANSPORTADORA</th>
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
                        data-transportadora="{{ $req->transportadora ?? '—' }}"
                        data-responsavel="{{ $req->responsavel ?? '—' }}"
                        data-total="{{ number_format($req->total_final, 2, ',', '.') }}"
                        data-peso="{{ $req->peso_confirmado ?? '' }}"
                        data-valor-carga="{{ $req->valor_carga ?? '' }}"
                        data-guia="{{ $req->numero_guia ?? '—' }}"
                        data-local-descarga="{{ $req->local_descarga ?? '' }}">

                        {{-- 1. Nº / DATA --}}
                        <td>
                            <span class="fw-bold" style="font-size:.8rem;color:#c60a1a;">
                                #{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                            </span><br>
                            <span class="text-muted" style="font-size:.72rem;">{{ $req->date->format('d/m/Y') }}</span>
                        </td>

                        {{-- 2. FORNECEDOR --}}
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

                        {{-- 3. Nº GUIA --}}
                        <td style="font-size:.82rem;">
                            @if($req->numero_guia)
                                <span class="fw-semibold d-flex align-items-center gap-1" style="color:#1e293b;">
                                    <i class="fas fa-file-invoice text-muted" style="font-size:.7rem;"></i>
                                    {{ $req->numero_guia }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- 4. CARGA / DESTINO --}}
                        <td style="font-size:.82rem;">
                            <span class="fw-bold">
                                {{ $req->items->first()->description ?? '—' }}
                            </span>
                            <br>
                            <span class="text-muted" style="font-size:.72rem;">
                                <i class="fas fa-location-dot me-1"></i>{{ $req->destino }}
                            </span>
                            @if($req->local_descarga)
                                <div class="text-muted" style="font-size:.7rem;">
                                    <i class="fas fa-map-pin me-1" style="color:#d97706;"></i>{{ $req->local_descarga }}
                                </div>
                            @endif
                        </td>

                        {{-- 5. MOTORISTA --}}
                        <td style="font-size:.82rem;">{{ $req->motorista ?? '—' }}</td>

                        {{-- 6. MATRÍCULA --}}
                        <td style="font-size:.82rem;">{{ $req->matricula ?? '—' }}</td>

                        {{-- 7. TRANSPORTADORA --}}
                        <td style="font-size:.82rem;">{{ $req->transportadora ?? '—' }}</td>

                        {{-- 8. RESPONSÁVEL --}}
                        <td style="font-size:.82rem;">{{ $req->responsavel ?? '—' }}</td>

                        {{-- 9. ESTADO --}}
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

                        {{-- 10. TOTAL (MT) --}}
                        <td class="text-end fw-bold" style="font-size:.82rem;">
                            @if($req->status === 'FINALIZADA')
                                {{ number_format($req->valor_carga ?? $req->total_final, 2, ',', '.') }} MT
                                @if($req->peso_confirmado)
                                    <div class="text-muted fw-normal" style="font-size:.7rem;">
                                        <i class="fas fa-weight-hanging me-1"></i>{{ number_format($req->peso_confirmado, 2, ',', '.') }} kg
                                    </div>
                                @endif
                            @else
                                <span class="text-muted" style="font-size:.75rem;font-style:italic;">
                                    — Aguarda confirmação
                                </span>
                            @endif
                        </td>

                        {{-- 11. AÇÕES --}}
                        <td class="text-center no-print" style="white-space:nowrap;">
                            <a href="{{ route('requisicoes-material.pdf', $req) }}" target="_blank"
                               class="action-btn text-danger border-danger border-opacity-25" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @can('requisicoes-material')
                                {{-- DEPOIS --}}
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

{{-- Editar sempre visível --}}
<button class="action-btn text-primary border-primary border-opacity-25 btn-edit"
    data-id="{{ $req->id }}" title="Editar Requisição">
    <i class="fas fa-pencil-alt"></i>
</button>

@if($req->status === 'FINALIZADA')
    <button class="action-btn btn-edit-carga text-success border-success border-opacity-50"
        data-id="{{ $req->id }}"
        data-num="#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}"
        data-destino="{{ $req->destino }}"
        data-peso="{{ $req->peso_confirmado ?? '' }}"
        data-valor="{{ $req->valor_carga ?? '' }}"
        data-guia="{{ $req->numero_guia ?? '' }}"
        data-local="{{ $req->local_descarga ?? '' }}"
        title="Editar dados da carga">
        <i class="fas fa-pen-to-square"></i>
        <!-- <span>Editar</span> -->
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


{{-- ═══════════════════════════════════════════════════════
     MODAL: CRIAR / EDITAR REQUISIÇÃO
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRequisicao" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius:var(--fem-radius);overflow:hidden;">
            <div class="fem-modal-header">
                <div>
                  <h4 class="modal-title" id="reqModalTitle">
    <span class="title-icon"><i class="fas fa-dolly"></i></span>
    <span class="title-text">Nova Requisição de Material</span>
</h4>
                    <div class="fem-modal-subtitle">Preencha os dados e adicione os itens a expedir</div>
                </div>
                <button type="button" class="btn-close-fem" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="fem-modal-body">
                <form id="formRequisicao" novalidate>
                    <input type="hidden" id="req_id">
                    <div class="fem-section mb-3">
                        <div class="fem-section-title">
                            <span class="fem-section-num">1</span>
                            Dados da Expedição
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="fem-label">Data <span class="text-danger">*</span></label>
                                <input type="date" class="fem-input" id="req_date" required>
                            </div>
                            <div class="col-md-9">
                                <label class="fem-label">Destino / Local de Entrega <span class="text-danger">*</span></label>
                                <input type="text" class="fem-input" id="req_destino"
                                    placeholder="Ex: Armazém Central, Obra Nº 12, Beira..." required>
                            </div>
                            <div class="col-12">
                                <label class="fem-label">Fornecedor</label>
                                <select class="fem-input" id="req_supplier_id" style="cursor:pointer;">
                                    <option value="">— Sem fornecedor —</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" data-nuit="{{ $s->nuit }}">
                                        {{ $s->name }}@if($s->nuit) — NUIT: {{ $s->nuit }}@endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="fem-section mb-3">
                        <div class="fem-section-title">
                            <span class="fem-section-num">2</span>
                            Transporte e Responsabilidade
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="fem-label">Motorista</label>
                                <input type="text" class="fem-input" id="req_motorista" placeholder="Nome do motorista">
                            </div>
                            <div class="col-md-3">
                                <label class="fem-label">Matrícula</label>
                                <input type="text" class="fem-input" id="req_matricula" placeholder="Ex: MBE-1234-M">
                            </div>
                            <div class="col-md-3">
                                <label class="fem-label">Transportadora</label>
                                <input type="text" class="fem-input" id="req_transportadora" placeholder="Ex: Transportes Alfa">
                            </div>
                            <div class="col-md-3">
                                <label class="fem-label">Responsável / Aprovador</label>
                                <input type="text" class="fem-input" id="req_responsavel">
                            </div>
                            <div class="col-md-9">
                                <label class="fem-label">Observações</label>
                                <textarea class="fem-input" id="req_observacoes" rows="2"
                                    style="resize:vertical;" placeholder="Notas adicionais..."></textarea>
                            </div>
                            <div class="col-md-3 d-none" id="statusWrap">
                                <label class="fem-label">Estado</label>
                                <select class="fem-input" id="req_status" style="cursor:pointer;">
                                    <option value="EMITIDA">EMITIDA</option>
                                    <option value="CONFIRMADA">CONFIRMADA</option>
                                    <option value="FINALIZADA">FINALIZADA</option>
                                    <option value="CANCELADO">CANCELADO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="fem-section">
                        <div class="fem-section-title d-flex justify-content-between align-items-center w-100">
                            <span class="d-flex align-items-center gap-2">
                                <span class="fem-section-num">3</span>
                                Itens da Requisição
                            </span>
                            <button type="button" class="fem-btn-secondary" id="btnShowPredef"
                                style="padding:5px 12px;font-size:.72rem;border-radius:6px;">
                                <i class="fas fa-layer-group"></i> Predefinições
                            </button>
                        </div>
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
                                <thead style="background:#f8fafc;">
                                    <tr>
                                        <th style="width:55%;font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;border-color:#e2e8f0;">Descrição <span class="text-danger">*</span></th>
                                        <th style="width:18%;font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;border-color:#e2e8f0;">Quantidade <span class="text-danger">*</span></th>
                                        <th style="width:18%;font-size:.68rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;border-color:#e2e8f0;">Unidade <span class="text-danger">*</span></th>
                                        <th style="width:9%;border-color:#e2e8f0;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                            </table>
                        </div>
                        <button type="button" class="fem-btn-secondary" id="btnAddItem"
                            style="padding:7px 16px;font-size:.78rem;border-radius:7px;">
                            <i class="fas fa-plus-circle"></i> Adicionar Item
                        </button>
                    </div>
                </form>
            </div>
            <div class="fem-modal-footer">
                <button class="fem-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="fem-btn-primary" id="btnSalvar">
                    <i class="fas fa-paper-plane"></i> Emitir Requisição
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════
     MODAL: CONFIRMAR CARGA (Peso + Valor + Guia)
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalConfirmarCarga" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content border-0" style="border-radius:var(--fem-radius);overflow:hidden;">
            <div class="fem-modal-header" style="padding-bottom:20px;">
                <div>
                    <h5 class="modal-title">
                        <span class="title-icon"><i class="fas fa-weight-hanging"></i></span>
                        Confirmação de Carga
                    </h5>
                    <div class="fem-modal-subtitle" id="cargaModalSub">Requisição —</div>
                </div>
                <button type="button" class="btn-close-fem" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
                <div class="mini-workflow">
                    <div class="mw-step">
                        <div class="mw-dot done"><i class="fas fa-check" style="font-size:.6rem;"></i></div>
                        <div class="mw-label done">Emitida</div>
                    </div>
                    <div class="mw-line"></div>
                    <div class="mw-step">
                        <div class="mw-dot active" style="font-size:.7rem;font-weight:700;">2</div>
                        <div class="mw-label active">Confirmar</div>
                    </div>
                    <div class="mw-line"></div>
                    <div class="mw-step">
                        <div class="mw-dot next" style="font-size:.7rem;font-weight:700;">3</div>
                        <div class="mw-label next">Finalizada</div>
                    </div>
                </div>
            </div>
            <div class="fem-modal-body">
                <input type="hidden" id="carga_req_id">
                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">1</span>
                        Peso Real da Carga
                    </div>
                    <div class="input-unit-wrap">
                        <input type="number" class="fem-input" id="carga_peso"
                            step="0.001" min="0" placeholder="Ex: 1250.500">
                        <span class="input-unit-badge">kg</span>
                    </div>
                </div>
                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">2</span>
                        Valor Real da Carga (MT)
                    </div>
                    <div class="input-unit-wrap">
                        <input type="number" class="fem-input" id="carga_valor"
                            step="0.01" min="0" placeholder="Ex: 125000.00">
                        <span class="input-unit-badge">MT</span>
                    </div>
                </div>
                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">3</span>
                        Número da Guia de Remessa
                    </div>
                    <input type="text" class="fem-input" id="carga_guia"
                        placeholder="Ex: GR-2024/001">
                </div>
                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">4</span>
                        Local de Descarga
                    </div>
                    <p class="text-muted mb-2" style="font-size:.78rem;">
                        Local exacto onde a carga foi/será descarregada (armazém, obra, coordenadas, etc.).
                    </p>
                    <input type="text" class="fem-input" id="carga_local_descarga"
                        placeholder="Ex: Armazém 3 — Porta Norte, Obra Nº 12 — Manica">
                </div>
                <div class="fem-section" style="background:#f0fdf4;border-color:#bbf7d0;">
                    <div class="fem-section-title" style="color:#15803d;margin-bottom:0;">
                        <span class="fem-section-num green">
                            <i class="fas fa-check" style="font-size:.6rem;"></i>
                        </span>
                        Finalizar Requisição
                    </div>
                    <p class="mb-0 mt-2" style="font-size:.78rem;color:#166534;">
                        Ao confirmar, o estado passa a <strong>FINALIZADA</strong> e a requisição fica encerrada.
                        Os dados de carga poderão ser editados posteriormente se necessário.
                    </p>
                </div>
            </div>
            <div class="fem-modal-footer">
                <button class="fem-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="fem-btn-success" id="btnFinalizar">
                    <i class="fas fa-check-double"></i> Confirmar e Finalizar
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════
     MODAL: EDITAR DADOS DE CARGA (pós-finalização)
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditarCarga" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content border-0" style="border-radius:var(--fem-radius);overflow:hidden;">
            <div class="fem-modal-header" style="padding-bottom:16px;">
                <div>
                    <h5 class="modal-title">
                        <span class="title-icon" style="background:rgba(217,119,6,.25);color:#fbbf24;">
                            <i class="fas fa-pen-to-square"></i>
                        </span>
                        Editar Dados de Carga
                    </h5>
                    <div class="fem-modal-subtitle" id="editCargaModalSub">Requisição —</div>
                </div>
                <button type="button" class="btn-close-fem" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="fem-modal-body">
                <input type="hidden" id="edit_carga_req_id">

                {{-- Aviso de edição pós-finalização --}}
                <div class="d-flex align-items-start gap-2 mb-3 p-3"
                    style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:.78rem;color:#92400e;">
                    <i class="fas fa-triangle-exclamation mt-1" style="flex-shrink:0;"></i>
                    <span>Esta requisição já está <strong>FINALIZADA</strong>. Qualquer alteração aqui irá actualizar os dados da carga e regenerar o PDF automaticamente.</span>
                </div>

                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">1</span>
                        Peso Real da Carga
                    </div>
                    <div class="input-unit-wrap">
                        <input type="number" class="fem-input" id="edit_carga_peso"
                            step="0.001" min="0" placeholder="Ex: 1250.500">
                        <span class="input-unit-badge">kg</span>
                    </div>
                </div>

                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">2</span>
                        Valor Real da Carga (MT)
                    </div>
                    <div class="input-unit-wrap">
                        <input type="number" class="fem-input" id="edit_carga_valor"
                            step="0.01" min="0" placeholder="Ex: 125000.00">
                        <span class="input-unit-badge">MT</span>
                    </div>
                </div>

                <div class="fem-section mb-3">
                    <div class="fem-section-title">
                        <span class="fem-section-num">3</span>
                        Número da Guia de Remessa
                    </div>
                    <input type="text" class="fem-input" id="edit_carga_guia"
                        placeholder="Ex: GR-2024/001">
                </div>

                <div class="fem-section">
                    <div class="fem-section-title">
                        <span class="fem-section-num amber">4</span>
                        Local de Descarga
                    </div>
                    <p class="text-muted mb-2" style="font-size:.78rem;">
                        Local exacto onde a carga foi descarregada. Aparece no rodapé do PDF.
                    </p>
                    <input type="text" class="fem-input" id="edit_carga_local_descarga"
                        placeholder="Ex: Armazém 3 — Porta Norte, Obra Nº 12 — Manica">
                </div>
            </div>
            <div class="fem-modal-footer">
                <button class="fem-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button class="fem-btn-amber" id="btnGuardarEditCarga">
                    <i class="fas fa-floppy-disk"></i> Guardar Alterações
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════
     MODAL: GERIR PREDEFINIÇÕES
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalPredef" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius:var(--fem-radius);overflow:hidden;">
            <div class="fem-modal-header">
                <div>
                    <h5 class="modal-title">
                        <span class="title-icon"><i class="fas fa-layer-group"></i></span>
                        Gerir Materiais Predefinidos
                    </h5>
                    <div class="fem-modal-subtitle">Crie descrições frequentes para inserir rapidamente nas requisições</div>
                </div>
                <button type="button" class="btn-close-fem" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="fem-modal-body">
                <div class="fem-section mb-4">
                    <div class="fem-section-title">
                        <span class="fem-section-num">+</span>
                        Adicionar Nova Predefinição
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="fem-label">Descrição do Material</label>
                            <input type="text" class="fem-input" id="predefNome"
                                placeholder="Ex: Cimento Portland 50kg">
                        </div>
                        <div class="col-md-3">
                            <label class="fem-label">Unidade padrão</label>
                            <select class="fem-input" id="predefUnidade" style="cursor:pointer;">
                                <option value="un">un</option>
                                <option value="kg">kg</option>
                                <option value="g">g</option>
                                <option value="mg">mg</option>
                                <option value="t">ton</option>
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
                            <button class="fem-btn-primary w-100" id="btnSalvarPredef"
                                style="justify-content:center;">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="fem-section" style="padding-bottom:4px;">
                    <div class="fem-section-title">
                        <span class="fem-section-num" style="background:#64748b;">
                            <i class="fas fa-list" style="font-size:.55rem;"></i>
                        </span>
                        Predefinições Existentes
                    </div>
                    <div id="predefManageList">
                        <div class="text-center text-muted py-4 small" id="predefEmptyMsg">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                            Ainda não há predefinições criadas.
                        </div>
                    </div>
                </div>
            </div>
            <div class="fem-modal-footer">
                <button class="fem-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>


{{-- TOAST --}}
<div class="toast-req" id="toastSuccess">
    <div class="t-icon"><i class="fas fa-check" id="toastIcon"></i></div>
    <div class="t-text">
        <div class="t-title" id="toastTitle">Operação concluída</div>
        <div class="t-sub"   id="toastSub">Requisição guardada com sucesso.</div>
    </div>
    <button class="t-close" onclick="closeToast()"><i class="fas fa-times"></i></button>
</div>

{{-- CONFIRM DELETE OVERLAY --}}
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
// Helpers de Modal
// ════════════════════════════════════════════
function showModal(id) { bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show(); }
function hideModal(id) { bootstrap.Modal.getInstance(document.getElementById(id))?.hide(); }

// ════════════════════════════════════════════
// DataTable
// ════════════════════════════════════════════
const table = $('#tblRequisicoes').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
    order: [[0, 'desc']],
    pageLength: 15,
    columnDefs: [{ orderable: false, targets: -1 }],
});

aplicarFiltros();

// ════════════════════════════════════════════
// Filtros
// ════════════════════════════════════════════
$.fn.dataTable.ext.search.push(function (settings, _data, dataIndex) {
    if (settings.nTable.id !== 'tblRequisicoes') return true;

    const node  = $(table.row(dataIndex).node());
    const di    = $('#filtroDataInicio').val();
    const df    = $('#filtroDataFim').val();
    const dest  = $('#filtroDestino').val().toLowerCase().trim();
    const st    = $('#filtroStatus').val();
    const num   = $('#filtroNumero').val().trim().replace(/^#/, '').replace(/^0+/, '');
    const mot   = $('#filtroMotorista').val().toLowerCase().trim();
    const mat   = $('#filtroMatricula').val().toLowerCase().trim();
    const transp= $('#filtroTransportadora').val().toLowerCase().trim();
    const resp  = $('#filtroResponsavel').val().toLowerCase().trim();
    const forn  = $('#filtroFornecedor').val().toLowerCase().trim();

    if (di    && String(node.data('date'))                                    < di)   return false;
    if (df    && String(node.data('date'))                                    > df)   return false;
    if (st    && node.data('status')                                         !== st)  return false;
    if (num   && !String(node.data('req-id')).includes(num))                          return false;
    if (dest  && !String(node.data('destino')).toLowerCase().includes(dest))          return false;
    if (forn  && !String(node.data('fornecedor')).toLowerCase().includes(forn))       return false;
    if (mot   && !String(node.data('motorista')).toLowerCase().includes(mot))         return false;
    if (mat   && !String(node.data('matricula')).toLowerCase().includes(mat))         return false;
    if (transp&& !String(node.data('transportadora')).toLowerCase().includes(transp)) return false;
    if (resp  && !String(node.data('responsavel')).toLowerCase().includes(resp))      return false;

    return true;
});

function aplicarFiltros() {
    table.draw();
    const filteredRows = table.rows({ filter: 'applied' });
    const count = filteredRows.count();

    let somaFiltrada = 0;
    filteredRows.every(function () {
        const node = $(this.node());
        if (node.data('status') === 'FINALIZADA') {
            somaFiltrada += parseFloat(node.data('valor-carga')) || 0;
        }
    });

    $('#kpiValorTotal').text(
        somaFiltrada.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT'
    );

    const f = {
        di:     $('#filtroDataInicio').val(),
        df:     $('#filtroDataFim').val(),
        dest:   $('#filtroDestino').val().trim(),
        st:     $('#filtroStatus').val(),
        num:    $('#filtroNumero').val().trim(),
        mot:    $('#filtroMotorista').val().trim(),
        mat:    $('#filtroMatricula').val().trim(),
        transp: $('#filtroTransportadora').val().trim(),
        resp:   $('#filtroResponsavel').val().trim(),
        forn:   $('#filtroFornecedor').val(),
    };

    const algumActivo = Object.values(f).some(v => v !== '');
    $('#extractCount').text(count);

    const partes = [];
    if (f.num)    partes.push('Nº: ' + f.num);
    if (f.di)     partes.push('De ' + f.di.split('-').reverse().join('/'));
    if (f.df)     partes.push('até ' + f.df.split('-').reverse().join('/'));
    if (f.dest)   partes.push('Destino: "' + f.dest + '"');
    if (f.forn)   partes.push('Fornecedor: "' + f.forn + '"');
    if (f.mot)    partes.push('Motorista: "' + f.mot + '"');
    if (f.mat)    partes.push('Matrícula: "' + f.mat + '"');
    if (f.transp) partes.push('Transportadora: "' + f.transp + '"');
    if (f.resp)   partes.push('Responsável: "' + f.resp + '"');
    if (f.st)     partes.push('Estado: ' + f.st);

    $('#extractLabel').text(partes.length ? ' — ' + partes.join(' | ') : '');
    $('#extractBar').toggleClass('visible', algumActivo && count > 0);
}

$('#filtroDataInicio, #filtroDataFim, #filtroDestino, #filtroStatus, ' +
  '#filtroNumero, #filtroMotorista, #filtroMatricula, #filtroTransportadora, ' +
  '#filtroResponsavel, #filtroFornecedor')
    .on('input change', aplicarFiltros);

$('#btnLimparFiltros').on('click', function () {
    $('#filtroDataInicio, #filtroDataFim, #filtroDestino, ' +
      '#filtroNumero, #filtroMotorista, #filtroMatricula, ' +
      '#filtroTransportadora, #filtroResponsavel').val('');
    $('#filtroStatus, #filtroFornecedor').val('');
    table.draw();
    $('#extractBar').removeClass('visible');
});

// ════════════════════════════════════════════
// Gerar Extrato PDF
// ════════════════════════════════════════════
function gerarExtratoPDF() {
    const rows = [];
    table.rows({ filter: 'applied' }).every(function () {
        const r = $(this.node());
        const descFirstItem = r.find('td:eq(3) .fw-bold').text().trim();
        rows.push({
            id:              r.data('req-id'),
            data:            r.data('date-fmt'),
            destino:         r.data('destino-fmt'),
            descricao:       descFirstItem || '—',
            fornecedor:      r.data('fornecedor'),
            guia:            r.data('guia') || '—',
            motorista:       r.data('motorista'),
            matricula:       r.data('matricula'),
            transportadora:  r.data('transportadora') || '—',
            responsavel:     r.data('responsavel'),
            status:          r.data('status'),
            valorCarga:      r.data('valor-carga') || '',
            peso:            r.data('peso') || '—',
            localDescarga:   r.data('local-descarga') || '—',
        });
    });

    if (!rows.length) return;

    const di    = $('#filtroDataInicio').val();
    const df    = $('#filtroDataFim').val();
    const dest  = $('#filtroDestino').val();
    const st    = $('#filtroStatus').val();
    const num   = $('#filtroNumero').val();
    const mot   = $('#filtroMotorista').val();
    const mat   = $('#filtroMatricula').val();
    const transp= $('#filtroTransportadora').val();
    const resp  = $('#filtroResponsavel').val();
    const forn  = $('#filtroFornecedor').val();

    const periodoStr = (di || df)
        ? (di ? di.split('-').reverse().join('/') : '—') + ' a ' + (df ? df.split('-').reverse().join('/') : '—')
        : 'Todo o período';

    const filtrosTexto = [
        num    ? 'Nº: '              + num    : null,
        dest   ? 'Destino: "'        + dest   + '"' : null,
        forn   ? 'Fornecedor: "'     + forn   + '"' : null,
        mot    ? 'Motorista: "'      + mot    + '"' : null,
        mat    ? 'Matrícula: "'      + mat    + '"' : null,
        transp ? 'Transportadora: "' + transp + '"' : null,
        resp   ? 'Responsável: "'    + resp   + '"' : null,
        st     ? 'Estado: '          + st     : null,
        (di || df) ? 'Período: ' + periodoStr : null,
    ].filter(Boolean).join(' | ') || 'Todos os registos';

    function parseVal(str) {
        return parseFloat(String(str).replace(/\./g, '').replace(',', '.')) || 0;
    }

    const sumTotal = rows
        .filter(r => r.status === 'FINALIZADA' && r.valorCarga !== '')
        .reduce((a, r) => a + parseVal(r.valorCarga), 0);

    const fmt       = n => n.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
    const dataHoje  = new Date().toLocaleDateString('pt-PT');
    const horaAgora = new Date().toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });

    const statusBadge = s => {
        const map = { EMITIDA: '#0369a1', CONFIRMADA: '#a16207', FINALIZADA: '#15803d', CANCELADO: '#dc2626' };
        return `<span style="background:${map[s]||'#64748b'};color:#fff;padding:2px 8px;border-radius:10px;font-size:9px;font-weight:700;">${s}</span>`;
    };

    const linhas = rows.map((r, i) => {
        const valorCell = (r.status === 'FINALIZADA' && r.valorCarga !== '')
            ? `<strong>${parseVal(r.valorCarga).toLocaleString('pt-PT',{minimumFractionDigits:2,maximumFractionDigits:2})} MT</strong>`
            : `<span style="color:#94a3b8;font-style:italic;">—</span>`;

        return `<tr>
            <td class="center">${i + 1}</td>
            <td><strong style="color:#c60a1a;">#${String(r.id).padStart(4,'0')}</strong></td>
            <td>${r.data}</td>
            <td>
                <div style="font-weight:bold;color:#1e293b;">${r.descricao}</div>
                <div style="font-size:9px;color:#64748b;margin-top:2px;">⬡ ${r.destino}</div>
                ${r.localDescarga !== '—' ? `<div style="font-size:9px;color:#d97706;margin-top:1px;">↓ ${r.localDescarga}</div>` : ''}
            </td>
            <td>${r.fornecedor}</td>
            <td style="color:#1e293b;font-weight:600;">${r.guia}</td>
            <td>${r.motorista}</td>
            <td>${r.matricula}</td>
            <td>${r.transportadora}</td>
            <td>${r.responsavel}</td>
            <td class="center">${statusBadge(r.status)}</td>
            <td class="right">${r.peso !== '—' ? r.peso + ' kg' : '—'}</td>
            <td class="right">${valorCell}</td>
        </tr>`;
    }).join('');

    const mostrarTotal = rows.some(r => r.status === 'FINALIZADA' && r.valorCarga !== '');

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
thead th{padding:7px 9px;font-size:9px;font-weight:bold;letter-spacing:.7px;text-transform:uppercase;color:#fff;text-align:left}
thead th.right,td.right{text-align:right}
thead th.center,td.center{text-align:center}
tbody tr{border-bottom:1px solid #e8edf2}
tbody tr:nth-child(even){background:#fdf5f5;-webkit-print-color-adjust:exact;print-color-adjust:exact}
tbody td{padding:7px 9px;font-size:10px;vertical-align:middle}
.totals-wrap{width:300px;margin-left:auto;margin-bottom:24px}
.totals-wrap tr.grand{background:#c60a1a;color:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.totals-wrap tr.grand td{padding:8px 10px;font-size:13px;font-weight:bold}
.totals-wrap td.right{text-align:right}
.page-footer{margin-top:32px;border-top:2px solid #c60a1a;padding-top:14px;display:flex;justify-content:space-between}
@media print{body{padding:20px}@page{margin:10mm;size:A4 landscape}}
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
        <th style="width:4%">Nº</th>
        <th style="width:6%">Data</th>
        <th style="width:13%">Carga / Destino</th>
        <th style="width:10%">Fornecedor</th>
        <th style="width:7%">Nº Guia</th>
        <th style="width:8%">Motorista</th>
        <th style="width:7%">Matrícula</th>
        <th style="width:9%">Transportadora</th>
        <th style="width:8%">Responsável</th>
        <th class="center" style="width:8%">Estado</th>
        <th class="right" style="width:6%">Peso (kg)</th>
        <th class="right" style="width:11%">Total (MT)</th>
    </tr></thead>
    <tbody>${linhas}</tbody>
</table>
${mostrarTotal ? `
<div class="totals-wrap">
    <table>
        <tr><td style="font-size:9px;color:#64748b;padding:6px 10px;">* Soma apenas das requisições finalizadas</td></tr>
        <tr class="grand"><td>TOTAL GERAL</td><td class="right">${fmt(sumTotal)}</td></tr>
    </table>
</div>` : ''}
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
                    <div style="font-size:.72rem;color:#94a3b8;">${p.unidade}</div>
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
                <div class="predef-item-details">${p.unidade}</div>
                <div class="predef-item-add"><i class="fas fa-plus"></i></div>
            </div>`);
    });
}

$('#btnSalvarPredef').on('click', function () {
    const nome    = $('#predefNome').val().trim();
    const unidade = $('#predefUnidade').val();
    if (!nome) return alert('Introduz o nome do material.');
    const arr = loadPredef();
    arr.push({ nome, unidade });
    savePredef(arr);
    $('#predefNome').val('');
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
    $('#itemsBody').append(itemRow({ description: p.nome, unit: p.unidade }));
    $('#predefPanel').hide();
});

$('#btnShowPredef').on('click', function () { renderPredefPanel(); $('#predefPanel').toggle(); });
$('#btnClosePredef').on('click', function () { $('#predefPanel').hide(); });
$('#btnGerir').on('click', function () { renderPredefManage(); showModal('modalPredef'); });

// ════════════════════════════════════════════
// Itens do modal
// ════════════════════════════════════════════
const UNITS = ['kg','g','mg','tons','m','cm','mm','km','m²','m³','l','ml','un','cx','pc'];

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
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>`;
}

$(document).on('click', '.btn-remove-item', function () {
    if ($('#itemsBody tr').length > 1) {
        $(this).closest('tr').remove();
    } else {
        alert('A requisição precisa de pelo menos um item.');
    }
});
$('#btnAddItem').on('click', function () { $('#itemsBody').append(itemRow()); $('#predefPanel').hide(); });

// ════════════════════════════════════════════
// Modal Nova Requisição
// ════════════════════════════════════════════
$('#btnNova').on('click', function () {
   $('#reqModalTitle .title-text').text('Nova Requisição de Material');
    $('#formRequisicao')[0].reset();
    $('#req_id').val('');
    $('#req_supplier_id').val('');
    $('#req_date').val(new Date().toISOString().split('T')[0]);
    $('#statusWrap').addClass('d-none');
    $('#predefPanel').hide();
    $('#itemsBody').html(itemRow());
    $('#btnSalvar').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Emitir Requisição');
    showModal('modalRequisicao');
});

// ════════════════════════════════════════════
// Modal Editar Requisição
// ════════════════════════════════════════════
$(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    $.ajax({
        url: `/requisicoes-material/${id}`,
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        success(data) {
            $('#reqModalTitle .title-text').text('Editar Requisição #' + String(data.id).padStart(4, '0'));
            $('#req_id').val(data.id);
            $('#req_date').val(data.date);
            $('#req_destino').val(data.destino);
            $('#req_supplier_id').val(data.supplier_id ?? '');
            $('#req_motorista').val(data.motorista ?? '');
            $('#req_matricula').val(data.matricula ?? '');
            $('#req_transportadora').val(data.transportadora ?? '');
            $('#req_responsavel').val(data.responsavel ?? '');
            $('#req_observacoes').val(data.observacoes ?? '');
            $('#req_status').val(data.status);
            $('#statusWrap').removeClass('d-none');
            $('#predefPanel').hide();
            $('#itemsBody').empty();
            (data.items ?? []).forEach(item => $('#itemsBody').append(itemRow(item)));
            $('#btnSalvar').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Alterações');
            showModal('modalRequisicao');
        },
        error(xhr) {
            const msg = xhr.responseJSON?.message ?? xhr.responseJSON?.error ?? `HTTP ${xhr.status}`;
            alert('Erro ao carregar requisição:\n' + msg);
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
        const desc = $(this).find('.item-desc').val().trim();
        const qty  = $(this).find('.item-qty').val();
        const unit = $(this).find('.item-unit').val();
        if (!desc || !qty) { valid = false; return false; }
        items.push({ description: desc, quantity: qty, unit: unit, unit_price: 0 });
    });
    if (!valid || !items.length) return alert('Preenche a descrição e a quantidade de todos os itens.');

    const id = $('#req_id').val();
    const payload = {
        _token:          '{{ csrf_token() }}',
        date:            $('#req_date').val(),
        destino:         $('#req_destino').val().trim(),
        supplier_id:     $('#req_supplier_id').val() || null,
        motorista:       $('#req_motorista').val(),
        matricula:       $('#req_matricula').val(),
        transportadora:  $('#req_transportadora').val(),
        responsavel:     $('#req_responsavel').val(),
        observacoes:     $('#req_observacoes').val(),
        status:          $('#statusWrap').hasClass('d-none') ? 'EMITIDA' : ($('#req_status').val() || 'EMITIDA'),
        items:           items,
    };
    if (id) payload._method = 'PUT';

    const url = id ? `/requisicoes-material/${id}` : '{{ route("requisicoes-material.store") }}';

    $('#btnSalvar').prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span> A guardar...');

    $.ajax({
        url, method: 'POST', data: payload,
        success() {
            hideModal('modalRequisicao');
            showToast(id ? 'Requisição actualizada.' : 'Requisição emitida com sucesso.', false);
            setTimeout(() => location.reload(), 1500);
        },
        error(xhr) {
            const errs = xhr.responseJSON?.errors;
            alert('Erro:\n' + (errs
                ? Object.values(errs).flat().join('\n')
                : xhr.responseJSON?.message ?? `HTTP ${xhr.status}`));
            $('#btnSalvar').prop('disabled', false)
                .html('<i class="fas fa-paper-plane"></i> Emitir Requisição');
        },
    });
});

// ════════════════════════════════════════════
// Modal Confirmar Carga (nova finalização)
// ════════════════════════════════════════════
$(document).on('click', '.btn-confirm-carga', function () {
    $('#carga_req_id').val($(this).data('id'));
    $('#cargaModalSub').text('Requisição ' + $(this).data('num') + ' — ' + $(this).data('destino'));
    $('#carga_peso, #carga_valor, #carga_guia, #carga_local_descarga').val('');
    $('#btnFinalizar').prop('disabled', false)
        .html('<i class="fas fa-check-double"></i> Confirmar e Finalizar');
    showModal('modalConfirmarCarga');
});

$('#btnFinalizar').on('click', function () {
    const peso  = $('#carga_peso').val();
    const valor = $('#carga_valor').val();
    const guia  = $('#carga_guia').val();

    if (!peso || !valor || !guia)
        return alert('Preenche o peso, o valor e o número da guia antes de finalizar.');

    $(this).prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span> A finalizar...');

    $.ajax({
        url: `/requisicoes-material/${$('#carga_req_id').val()}/confirmar-carga`,
        method: 'POST',
        data: {
            _token:           '{{ csrf_token() }}',
            peso_confirmado:  peso,
            valor_carga:      valor,
            numero_guia:      guia,
            local_descarga:   $('#carga_local_descarga').val(),
        },
        success() {
            hideModal('modalConfirmarCarga');
            showToast('Requisição finalizada com sucesso!', false);
            setTimeout(() => location.reload(), 1500);
        },
        error(xhr) {
            alert('Erro ao finalizar:\n' + (xhr.responseJSON?.message ?? `HTTP ${xhr.status}`));
            $('#btnFinalizar').prop('disabled', false)
                .html('<i class="fas fa-check-double"></i> Confirmar e Finalizar');
        },
    });
});

// ════════════════════════════════════════════
// Modal Editar Dados de Carga (pós-finalização)
// ════════════════════════════════════════════
$(document).on('click', '.btn-edit-carga', function () {
    const btn = $(this);
    $('#edit_carga_req_id').val(btn.data('id'));
    $('#editCargaModalSub').text('Requisição ' + btn.data('num') + ' — ' + btn.data('destino'));
    $('#edit_carga_peso').val(btn.data('peso'));
    $('#edit_carga_valor').val(btn.data('valor'));
    $('#edit_carga_guia').val(btn.data('guia'));
    $('#edit_carga_local_descarga').val(btn.data('local'));
    $('#btnGuardarEditCarga').prop('disabled', false)
        .html('<i class="fas fa-floppy-disk"></i> Guardar Alterações');
    showModal('modalEditarCarga');
});

$('#btnGuardarEditCarga').on('click', function () {
    const peso  = $('#edit_carga_peso').val();
    const valor = $('#edit_carga_valor').val();
    const guia  = $('#edit_carga_guia').val();

    if (!peso || !valor || !guia)
        return alert('Preenche o peso, o valor e o número da guia.');

    $(this).prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span> A guardar...');

    $.ajax({
        url: `/requisicoes-material/${$('#edit_carga_req_id').val()}/editar-carga`,
        method: 'POST',
        data: {
            _token:           '{{ csrf_token() }}',
            _method:          'PATCH',
            peso_confirmado:  peso,
            valor_carga:      valor,
            numero_guia:      guia,
            local_descarga:   $('#edit_carga_local_descarga').val(),
        },
        success() {
            hideModal('modalEditarCarga');
            showToast('Dados de carga actualizados. O PDF foi regenerado.', false);
            setTimeout(() => location.reload(), 1500);
        },
        error(xhr) {
            alert('Erro ao guardar:\n' + (xhr.responseJSON?.message ?? `HTTP ${xhr.status}`));
            $('#btnGuardarEditCarga').prop('disabled', false)
                .html('<i class="fas fa-floppy-disk"></i> Guardar Alterações');
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
            table.row(`tr[data-req-id="${_deleteId}"]`).remove().draw();
            showToast('Requisição eliminada com sucesso.', false);
        },
        error(xhr) {
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