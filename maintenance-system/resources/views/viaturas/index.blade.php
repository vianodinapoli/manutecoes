@extends('layouts.app')

@section('title', 'Gestão de Viaturas')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
    --navy:        #0f2640;
    --navy-mid:    #1a3c5e;
    --navy-light:  #2563a8;
    --navy-pale:   #e8f0fa;
    --navy-ghost:  #f4f8fe;
    --ink:         #0f172a;
    --ink-mid:     #334155;
    --ink-soft:    #64748b;
    --ink-pale:    #94a3b8;
    --line:        #e2e8f0;
    --surface:     #ffffff;
    --surface-2:   #f8fafc;
    --green:       #059669;
    --green-bg:    #ecfdf5;
    --green-border:#a7f3d0;
    --amber:       #d97706;
    --amber-bg:    #fffbeb;
    --amber-border:#fde68a;
    --red:         #dc2626;
    --red-bg:      #fef2f2;
    --red-border:  #fecaca;
    --radius-sm:   6px;
    --radius:      10px;
    --radius-lg:   16px;
    --shadow-sm:   0 1px 3px rgba(15,38,64,.08), 0 1px 2px rgba(15,38,64,.04);
    --shadow:      0 4px 16px rgba(15,38,64,.10), 0 1px 4px rgba(15,38,64,.06);
    --shadow-lg:   0 20px 48px rgba(15,38,64,.16), 0 4px 16px rgba(15,38,64,.08);
    --font:        'DM Sans', sans-serif;
    --font-mono:   'DM Mono', monospace;
    --transition:  .18s cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--font); font-size: 14px; color: var(--ink); background: var(--surface-2); line-height: 1.55; -webkit-font-smoothing: antialiased; }

.page-wrapper { max-width: 1380px; margin: 0 auto; padding: 32px 28px 64px; }

.page-top { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; gap: 20px; flex-wrap: wrap; }
.page-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--ink-soft); margin-bottom: 6px; }
.page-breadcrumb a { color: var(--navy-light); text-decoration: none; }
.page-breadcrumb a:hover { text-decoration: underline; }
.page-breadcrumb span { color: var(--ink-pale); }
.page-title { font-size: 26px; font-weight: 700; color: var(--navy); letter-spacing: -.4px; line-height: 1.2; }
.page-subtitle { font-size: 13px; color: var(--ink-soft); margin-top: 3px; }
.page-top-actions { display: flex; gap: 10px; align-items: center; }

.btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: var(--radius-sm); font-family: var(--font); font-size: 13.5px; font-weight: 600; cursor: pointer; transition: all var(--transition); border: none; white-space: nowrap; text-decoration: none; }
.btn-primary { background: var(--navy-mid); color: #fff; box-shadow: 0 2px 8px rgba(26,60,94,.30); }
.btn-primary:hover { background: var(--navy); box-shadow: 0 4px 14px rgba(15,38,64,.35); transform: translateY(-1px); }
.btn-secondary { background: var(--surface); color: var(--ink-mid); border: 1px solid var(--line); box-shadow: var(--shadow-sm); }
.btn-secondary:hover { background: var(--surface-2); border-color: var(--ink-pale); }
.btn-ghost { background: transparent; color: var(--ink-soft); padding: 7px 10px; }
.btn-ghost:hover { background: var(--surface-2); color: var(--ink); }
.btn-danger { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
.btn-danger:hover { background: #fee2e2; }
.btn-sm { padding: 5px 12px; font-size: 12.5px; }
.btn-icon { padding: 7px; border-radius: var(--radius-sm); }
.btn-print { background: #fff; color: var(--navy-mid); border: 1px solid var(--line); box-shadow: var(--shadow-sm); }
.btn-print:hover { background: var(--navy-pale); border-color: var(--navy-light); color: var(--navy); }

.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
.stat-card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 18px 20px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 14px; transition: box-shadow var(--transition); }
.stat-card:hover { box-shadow: var(--shadow); }
.stat-icon { width: 42px; height: 42px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.stat-icon.blue  { background: var(--navy-pale); }
.stat-icon.green { background: var(--green-bg); }
.stat-icon.amber { background: var(--amber-bg); }
.stat-icon.red   { background: var(--red-bg); }
.stat-label { font-size: 11.5px; color: var(--ink-soft); font-weight: 500; letter-spacing: .2px; }
.stat-value { font-size: 22px; font-weight: 700; color: var(--navy); line-height: 1.1; margin-top: 2px; }

.card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); overflow: hidden; }
.card-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--line); gap: 14px; flex-wrap: wrap; }
.card-title { font-size: 15px; font-weight: 700; color: var(--navy); }
.card-header-right { display: flex; gap: 8px; align-items: center; }

.search-wrap { position: relative; display: flex; align-items: center; }
.search-wrap svg { position: absolute; left: 10px; color: var(--ink-pale); pointer-events: none; }
.search-input { padding: 7px 12px 7px 34px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-family: var(--font); font-size: 13px; color: var(--ink); background: var(--surface-2); outline: none; width: 220px; transition: all var(--transition); }
.search-input:focus { border-color: var(--navy-light); background: var(--surface); box-shadow: 0 0 0 3px rgba(37,99,168,.12); width: 260px; }

/* ── TABELA JUSTIFICADA ── */
.table-scroll { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }

thead th {
    background: var(--navy-ghost);
    color: var(--ink-soft);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .7px;
    padding: 10px 14px;
    text-align: left;
    border-bottom: 1px solid var(--line);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Larguras fixas para colunas justificadas */
col.col-marca    { width: 13%; }
col.col-modelo   { width: 13%; }
col.col-mat      { width: 12%; }
col.col-chassi   { width: 18%; }
col.col-cor      { width: 9%; }
col.col-seguro   { width: 13%; }
col.col-ipo      { width: 13%; }
col.col-actions  { width: 9%; }

tbody tr { border-bottom: 1px solid var(--line); transition: background var(--transition); }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: var(--navy-ghost); }

tbody td { padding: 12px 14px; font-size: 13px; vertical-align: middle; color: var(--ink-mid); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
td.td-marca  { font-weight: 600; color: var(--navy); }
td.td-mat    { font-family: var(--font-mono); font-size: 12px; font-weight: 500; color: var(--navy-mid); letter-spacing: .5px; }
td.td-chassi { font-family: var(--font-mono); font-size: 11.5px; color: var(--ink-soft); letter-spacing: .3px; }
td.td-actions { text-align: right; white-space: nowrap; overflow: visible; }

.date-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.date-ok     { background: var(--green-bg); color: var(--green);  border: 1px solid var(--green-border); }
.date-warn   { background: var(--amber-bg); color: var(--amber);  border: 1px solid var(--amber-border); }
.date-expire { background: var(--red-bg);   color: var(--red);    border: 1px solid var(--red-border);   }
.date-dot    { width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: .7; flex-shrink: 0; }

.cor-wrap { display: flex; align-items: center; gap: 6px; }
.cor-dot  { width: 10px; height: 10px; border-radius: 50%; border: 1.5px solid rgba(0,0,0,.1); flex-shrink: 0; }

.action-group { display: inline-flex; gap: 3px; }

.empty-state { padding: 64px 24px; text-align: center; color: var(--ink-soft); }
.empty-state p { font-size: 14px; }

/* ── MODAIS ── */
.vt-overlay { display: none; position: fixed; inset: 0; background: rgba(10,20,40,.55); backdrop-filter: blur(3px); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
.vt-overlay.active { display: flex !important; animation: fadeIn .18s ease; }
@@keyframes fadeIn  { from { opacity: 0; } to { opacity: 1; } }
@@keyframes slideUp { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
.vt-modal { background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); width: 100%; max-height: 90vh; overflow-y: auto; animation: slideUp .22s cubic-bezier(.34,1.56,.64,1); position: relative; }
.vt-modal-sm { max-width: 480px; }
.vt-modal-md { max-width: 680px; }
.vt-modal-lg { max-width: 880px; }
.vt-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 22px 26px 18px; border-bottom: 1px solid var(--line); position: sticky; top: 0; background: var(--surface); z-index: 2; }
.vt-modal-header-left { display: flex; align-items: center; gap: 12px; }
.vt-modal-icon { width: 38px; height: 38px; border-radius: var(--radius-sm); background: var(--navy-pale); display: flex; align-items: center; justify-content: center; color: var(--navy-mid); font-size: 17px; }
.vt-modal-title { font-size: 16px; font-weight: 700; color: var(--navy); }
.vt-modal-subtitle { font-size: 12px; color: var(--ink-soft); margin-top: 1px; }
.vt-modal-close { width: 32px; height: 32px; border-radius: var(--radius-sm); border: 1px solid var(--line); background: var(--surface); cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--ink-soft); font-size: 18px; transition: all var(--transition); line-height: 1; }
.vt-modal-close:hover { background: var(--red-bg); color: var(--red); border-color: var(--red-border); }
.vt-modal-body   { padding: 24px 26px; }
.vt-modal-footer { padding: 16px 26px 22px; border-top: 1px solid var(--line); display: flex; justify-content: flex-end; gap: 10px; }

.form-grid   { display: grid; gap: 16px; }
.form-grid-2 { grid-template-columns: 1fr 1fr; }
.form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
.form-group  { display: flex; flex-direction: column; gap: 5px; }
.form-group.span-2 { grid-column: span 2; }
.form-group.span-3 { grid-column: span 3; }
label { font-size: 12px; font-weight: 600; color: var(--ink-mid); letter-spacing: .2px; }
label .required { color: var(--red); margin-left: 2px; }
input[type="text"], input[type="date"], input[type="email"], input[type="number"], select, textarea { padding: 9px 12px; border: 1px solid var(--line); border-radius: var(--radius-sm); font-family: var(--font); font-size: 13.5px; color: var(--ink); background: var(--surface); outline: none; transition: all var(--transition); width: 100%; -webkit-appearance: none; }
input:focus, select:focus, textarea:focus { border-color: var(--navy-light); box-shadow: 0 0 0 3px rgba(37,99,168,.12); }
textarea { resize: vertical; min-height: 80px; }
select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px; }
.form-hint { font-size: 11.5px; color: var(--ink-pale); margin-top: 2px; }
.form-section-label { font-size: 11px; font-weight: 700; color: var(--ink-soft); text-transform: uppercase; letter-spacing: .7px; display: flex; align-items: center; gap: 8px; margin: 20px 0 14px; }
.form-section-label::before, .form-section-label::after { content: ''; flex: 1; height: 1px; background: var(--line); }
.form-section-label:first-child { margin-top: 0; }

.meta-tags-wrap { display: flex; flex-wrap: wrap; gap: 6px; padding: 8px 10px; border: 1px solid var(--line); border-radius: var(--radius-sm); min-height: 42px; background: var(--surface); cursor: text; transition: border-color var(--transition), box-shadow var(--transition); }
.meta-tags-wrap:focus-within { border-color: var(--navy-light); box-shadow: 0 0 0 3px rgba(37,99,168,.12); }
.meta-tag { display: inline-flex; align-items: center; gap: 5px; background: var(--navy-pale); color: var(--navy-mid); padding: 3px 8px 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.meta-tag-remove { cursor: pointer; line-height: 1; color: var(--navy-light); font-size: 15px; opacity: .7; background: none; border: none; padding: 0; font-family: var(--font); }
.meta-tag-remove:hover { opacity: 1; }
.meta-tag-input { border: none; outline: none; font-family: var(--font); font-size: 13px; color: var(--ink); background: transparent; min-width: 100px; flex: 1; padding: 2px 4px; }

.dropzone { border: 2px dashed var(--line); border-radius: var(--radius); padding: 28px 20px; text-align: center; cursor: pointer; transition: all var(--transition); background: var(--surface-2); position: relative; }
.dropzone:hover, .dropzone.dragover { border-color: var(--navy-light); background: var(--navy-ghost); }
.dropzone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }
.dropzone-icon { font-size: 32px; margin-bottom: 8px; opacity: .5; }
.dropzone-text { font-size: 13px; color: var(--ink-soft); }
.dropzone-text strong { color: var(--navy-light); }
.dropzone-hint { font-size: 11.5px; color: var(--ink-pale); margin-top: 4px; }

.file-list { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
.file-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border: 1px solid var(--line); border-radius: var(--radius-sm); background: var(--surface); }
.file-icon { font-size: 20px; flex-shrink: 0; }
.file-info { flex: 1; min-width: 0; }
.file-name { font-size: 13px; font-weight: 500; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.file-size { font-size: 11.5px; color: var(--ink-pale); }
.file-remove { cursor: pointer; color: var(--ink-pale); font-size: 16px; background: none; border: none; padding: 3px; line-height: 1; }
.file-remove:hover { color: var(--red); }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: var(--line); border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; }
.info-cell { background: var(--surface); padding: 13px 16px; }
.info-cell.span-2 { grid-column: span 2; }
.info-cell-label { font-size: 10.5px; font-weight: 600; color: var(--ink-pale); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.info-cell-value { font-size: 13.5px; color: var(--ink); font-weight: 500; }
.info-cell-value.mono { font-family: var(--font-mono); font-size: 12.5px; }

.docs-preview { display: flex; flex-wrap: wrap; gap: 8px; }
.doc-chip { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border: 1px solid var(--line); border-radius: 20px; font-size: 12.5px; color: var(--navy-mid); background: var(--navy-ghost); cursor: pointer; text-decoration: none; transition: all var(--transition); }
.doc-chip:hover { background: var(--navy-pale); border-color: var(--navy-light); }

.toast-wrap { position: fixed; bottom: 24px; right: 24px; display: flex; flex-direction: column; gap: 8px; z-index: 2000; }
.toast { display: flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: var(--radius); background: var(--navy); color: #fff; font-size: 13.5px; font-weight: 500; box-shadow: var(--shadow-lg); animation: slideUp .22s ease; min-width: 260px; }
.toast.success { background: #065f46; }
.toast.error   { background: #991b1b; }

@@media (max-width: 900px) {
    .stats-row { grid-template-columns: 1fr 1fr; }
    .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
    .form-group.span-2, .form-group.span-3 { grid-column: span 1; }
    .info-grid { grid-template-columns: 1fr; }
    .info-cell.span-2 { grid-column: span 1; }
}
@@media (max-width: 600px) {
    .stats-row { grid-template-columns: 1fr; }
    .page-wrapper { padding: 20px 16px 48px; }
}

/* ══════════════════════════════════════
   IMPRESSÃO PDF
══════════════════════════════════════ */
@@media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

    body, html {
        background: #fff !important;
        font-family: 'DM Sans', sans-serif;
        font-size: 11px;
        color: #0f172a;
    }

    /* Esconder tudo menos o conteúdo de impressão */
    body > *:not(#printArea) { display: none !important; }
    #sidebar, nav, .page-top-actions, .card-header-right,
    .action-group, .vt-overlay, .toast-wrap { display: none !important; }

    #printArea {
        display: block !important;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: #fff;
        padding: 0;
    }

    @page {
        size: A4 landscape;
        margin: 14mm 12mm;
    }
}
</style>
@endpush

@section('content')

@php
    function dateStatus(string $date): string {
        $d = \Carbon\Carbon::parse($date);
        $days = now()->diffInDays($d, false);
        if ($days < 0)   return 'expire';
        if ($days <= 60) return 'warn';
        return 'ok';
    }
    function dateLabel(string $status): string {
        return match($status) { 'expire' => 'Expirado', 'warn' => 'A expirar', default => 'Válido' };
    }
    function corHex(string $cor): string {
        return match(strtolower(trim($cor))) {
            'branca', 'branco'       => '#f0f0f0',
            'preta', 'preto'         => '#1a1a1a',
            'vermelha', 'vermelho'   => '#dc2626',
            'azul'                   => '#2563a8',
            'cinza', 'cinzento'      => '#94a3b8',
            'verde'                  => '#059669',
            default                  => '#cbd5e1',
        };
    }
@endphp

<div class="page-wrapper">

    {{-- ── PAGE TOP ── --}}
    <div class="page-top">
        <div class="page-top-left">
            <div class="page-breadcrumb">
                <a href="{{ route('dashboard') }}">Início</a>
                <span>›</span>
                <span>Viaturas</span>
            </div>
            <h1 class="page-title">Gestão de Viaturas</h1>
            <p class="page-subtitle">Registo, documentação e controlo de frota</p>
        </div>
        <div class="page-top-actions">
            <button class="btn btn-print" onclick="imprimirRelatorio()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Imprimir / PDF
            </button>
            <button class="btn btn-secondary" onclick="exportar()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Exportar Excel
            </button>
            <button class="btn btn-primary" onclick="abrirModalNova()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Viatura
            </button>
        </div>
    </div>

    {{-- ── STATS ── --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon blue">🚛</div>
            <div>
                <div class="stat-label">Total de Viaturas</div>
                <div class="stat-value">{{ $viaturas->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div>
                <div class="stat-label">Documentação OK</div>
                <div class="stat-value">{{ $viaturas->filter(fn($v) => dateStatus($v->seguro) === 'ok' && dateStatus($v->ipo) === 'ok')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">⚠️</div>
            <div>
                <div class="stat-label">A Expirar (60 dias)</div>
                <div class="stat-value">{{ $viaturas->filter(fn($v) => dateStatus($v->seguro) === 'warn' || dateStatus($v->ipo) === 'warn')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">🚨</div>
            <div>
                <div class="stat-label">Documentos Expirados</div>
                <div class="stat-value">{{ $viaturas->filter(fn($v) => dateStatus($v->seguro) === 'expire' || dateStatus($v->ipo) === 'expire')->count() }}</div>
            </div>
        </div>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Frota Registada</span>
            <div class="card-header-right">
                <div class="search-wrap">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" class="search-input" id="searchInput" placeholder="Pesquisar viatura..." oninput="filtrarTabela(this.value)">
                </div>
            </div>
        </div>

        <div class="table-scroll">
            <table id="tabelaViaturas">
                <colgroup>
                    <col class="col-marca">
                    <col class="col-modelo">
                    <col class="col-mat">
                    <col class="col-chassi">
                    <col class="col-cor">
                    <col class="col-seguro">
                    <col class="col-ipo">
                    <col class="col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Matrícula</th>
                        <th>Nº Chassi</th>
                        <th>Cor</th>
                        <th>Seguro</th>
                        <th>IPO</th>
                        <th style="text-align:right">Acções</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($viaturas as $v)
                    @php
                        $segStatus = dateStatus($v->seguro);
                        $ipoStatus = dateStatus($v->ipo);
                    @endphp
                    <tr data-search="{{ strtolower($v->marca.' '.$v->modelo.' '.$v->matricula.' '.$v->numero_chassi) }}">
                        <td class="td-marca" title="{{ $v->marca }}">{{ $v->marca }}</td>
                        <td title="{{ $v->modelo }}">{{ $v->modelo }}</td>
                        <td class="td-mat" title="{{ $v->matricula }}">{{ $v->matricula }}</td>
                        <td class="td-chassi" title="{{ $v->numero_chassi }}">{{ $v->numero_chassi }}</td>
                        <td>
                            <div class="cor-wrap">
                                <span class="cor-dot" style="background:{{ corHex($v->cor) }};"></span>
                                {{ $v->cor }}
                            </div>
                        </td>
                        <td>
                            <span class="date-badge date-{{ $segStatus }}">
                                <span class="date-dot"></span>
                                {{ \Carbon\Carbon::parse($v->seguro)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="date-badge date-{{ $ipoStatus }}">
                                <span class="date-dot"></span>
                                {{ \Carbon\Carbon::parse($v->ipo)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="td-actions">
                            <div class="action-group">
                                <button class="btn btn-ghost btn-icon btn-sm" title="Visualizar"
                                    data-id="{{ $v->id }}" onclick="abrirModalVer(this.dataset.id)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <button class="btn btn-ghost btn-icon btn-sm" title="Editar"
                                    data-id="{{ $v->id }}" onclick="abrirModalEditar(this.dataset.id)" style="color:var(--navy-light)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button class="btn btn-ghost btn-icon btn-sm" title="Eliminar"
                                    data-id="{{ $v->id }}" data-mat="{{ $v->matricula }}" onclick="confirmarEliminar(this.dataset.id, this.dataset.mat)" style="color:var(--red)">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <p>Nenhuma viatura registada. Clique em <strong>Nova Viatura</strong> para começar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     ÁREA DE IMPRESSÃO (oculta no ecrã)
══════════════════════════════════════ --}}
<div id="printArea" style="display:none;">
<style>
    #printArea {
        font-family: 'DM Sans', 'Segoe UI', sans-serif;
        background: #fff;
        color: #0f172a;
        padding: 0;
    }

    /* Cabeçalho do relatório */
    .rpt-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 28px 32px 20px;
        border-bottom: 3px solid #0f2640;
        margin-bottom: 0;
    }
    .rpt-brand { display: flex; flex-direction: column; gap: 2px; }
    .rpt-brand-name {
        font-size: 26px;
        font-weight: 800;
        color: #0f2640;
        letter-spacing: -1px;
        line-height: 1;
    }
    .rpt-brand-sub {
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: #64748b;
        margin-top: 3px;
    }
    .rpt-meta { text-align: right; }
    .rpt-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f2640;
        margin-bottom: 4px;
    }
    .rpt-date {
        font-size: 10.5px;
        color: #64748b;
    }

    /* KPIs */
    .rpt-kpis {
        display: flex;
        gap: 0;
        background: #f4f8fe;
        border-bottom: 1px solid #e2e8f0;
    }
    .rpt-kpi {
        flex: 1;
        padding: 14px 20px;
        border-right: 1px solid #e2e8f0;
    }
    .rpt-kpi:last-child { border-right: none; }
    .rpt-kpi-label { font-size: 9px; font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; }
    .rpt-kpi-value { font-size: 20px; font-weight: 800; color: #0f2640; line-height: 1; }
    .rpt-kpi-value.green { color: #059669; }
    .rpt-kpi-value.amber { color: #d97706; }
    .rpt-kpi-value.red   { color: #dc2626; }

    /* Tabela */
    .rpt-table-wrap { padding: 0; }
    .rpt-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .rpt-table thead tr {
        background: #0f2640;
    }
    .rpt-table thead th {
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 10px 14px;
        text-align: left;
        border: none;
    }
    .rpt-table thead th:last-child { text-align: center; }

    .rpt-table tbody tr { border-bottom: 1px solid #f1f5f9; }
    .rpt-table tbody tr:nth-child(even) { background: #f8fafc; }
    .rpt-table tbody tr:last-child { border-bottom: none; }

    .rpt-table tbody td {
        padding: 9px 14px;
        font-size: 10.5px;
        color: #334155;
        vertical-align: middle;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .rpt-table td.td-marca  { font-weight: 700; color: #0f2640; }
    .rpt-table td.td-mat    { font-family: 'DM Mono', monospace; font-size: 10px; font-weight: 600; color: #1a3c5e; letter-spacing: .5px; }
    .rpt-table td.td-chassi { font-family: 'DM Mono', monospace; font-size: 9.5px; color: #64748b; }
    .rpt-table td.td-status { text-align: center; }

    /* Larguras das colunas no relatório */
    .rpt-table col.c-marca   { width: 14%; }
    .rpt-table col.c-modelo  { width: 14%; }
    .rpt-table col.c-mat     { width: 11%; }
    .rpt-table col.c-chassi  { width: 19%; }
    .rpt-table col.c-cor     { width: 9%; }
    .rpt-table col.c-seguro  { width: 14%; }
    .rpt-table col.c-ipo     { width: 14%; }
    .rpt-table col.c-estado  { width: 5%; }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .3px;
    }
    .badge-ok     { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
    .badge-warn   { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
    .badge-expire { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

    .badge-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

    /* Cor dot */
    .cor-dot-sm { display: inline-block; width: 8px; height: 8px; border-radius: 50%; border: 1px solid rgba(0,0,0,.15); margin-right: 5px; vertical-align: middle; }

    /* Rodapé */
    .rpt-footer {
        padding: 14px 32px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .rpt-footer-left  { font-size: 9px; color: #94a3b8; }
    .rpt-footer-right { font-size: 9px; color: #94a3b8; }

    /* Legenda */
    .rpt-legend {
        display: flex;
        gap: 16px;
        padding: 10px 32px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }
    .rpt-legend-item { display: flex; align-items: center; gap: 6px; font-size: 9px; color: #64748b; }
</style>

<div class="rpt-header">
    <div class="rpt-brand">
        <div class="rpt-brand-name">FEM</div>
        <div class="rpt-brand-sub">Fábrica de Explosivos de Moçambique</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Relatório de Gestão de Viaturas</div>
        <div class="rpt-date">Gerado em: <span id="rptDate"></span> &nbsp;·&nbsp; Total: {{ $viaturas->count() }} viaturas</div>
    </div>
</div>

<div class="rpt-kpis">
    <div class="rpt-kpi">
        <div class="rpt-kpi-label">Total de Viaturas</div>
        <div class="rpt-kpi-value">{{ $viaturas->count() }}</div>
    </div>
    <div class="rpt-kpi">
        <div class="rpt-kpi-label">Documentação OK</div>
        <div class="rpt-kpi-value green">{{ $viaturas->filter(fn($v) => dateStatus($v->seguro) === 'ok' && dateStatus($v->ipo) === 'ok')->count() }}</div>
    </div>
    <div class="rpt-kpi">
        <div class="rpt-kpi-label">A Expirar (60 dias)</div>
        <div class="rpt-kpi-value amber">{{ $viaturas->filter(fn($v) => dateStatus($v->seguro) === 'warn' || dateStatus($v->ipo) === 'warn')->count() }}</div>
    </div>
    <div class="rpt-kpi">
        <div class="rpt-kpi-label">Documentos Expirados</div>
        <div class="rpt-kpi-value red">{{ $viaturas->filter(fn($v) => dateStatus($v->seguro) === 'expire' || dateStatus($v->ipo) === 'expire')->count() }}</div>
    </div>
    <div class="rpt-kpi">
        <div class="rpt-kpi-label">Preparado por</div>
        <div style="font-size:12px;font-weight:700;color:#0f2640;margin-top:4px;">{{ auth()->user()->name }}</div>
    </div>
</div>

<div class="rpt-table-wrap">
    <table class="rpt-table">
        <colgroup>
            <col class="c-marca">
            <col class="c-modelo">
            <col class="c-mat">
            <col class="c-chassi">
            <col class="c-cor">
            <col class="c-seguro">
            <col class="c-ipo">
            <col class="c-estado">
        </colgroup>
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Matrícula</th>
                <th>Nº Chassi</th>
                <th>Cor</th>
                <th>Seguro até</th>
                <th>IPO até</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($viaturas as $v)
            @php
                $seg = dateStatus($v->seguro);
                $ipo = dateStatus($v->ipo);
                $overall = ($seg === 'expire' || $ipo === 'expire') ? 'expire'
                         : (($seg === 'warn'   || $ipo === 'warn')   ? 'warn' : 'ok');
                $overallLabel = match($overall) { 'expire' => '✗', 'warn' => '!', default => '✓' };
            @endphp
            <tr>
                <td class="td-marca">{{ $v->marca }}</td>
                <td>{{ $v->modelo }}</td>
                <td class="td-mat">{{ $v->matricula }}</td>
                <td class="td-chassi">{{ $v->numero_chassi ?? '—' }}</td>
                <td>
                    <span class="cor-dot-sm" style="background:{{ corHex($v->cor ?? '') }};"></span>
                    {{ $v->cor ?? '—' }}
                </td>
                <td>
                    <span class="badge badge-{{ $seg }}">
                        <span class="badge-dot"></span>
                        {{ \Carbon\Carbon::parse($v->seguro)->format('d/m/Y') }}
                    </span>
                </td>
                <td>
                    <span class="badge badge-{{ $ipo }}">
                        <span class="badge-dot"></span>
                        {{ \Carbon\Carbon::parse($v->ipo)->format('d/m/Y') }}
                    </span>
                </td>
                <td class="td-status">
                    <span class="badge badge-{{ $overall }}">{{ $overallLabel }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">Nenhuma viatura registada.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="rpt-legend">
    <div class="rpt-legend-item">
        <span class="badge badge-ok"><span class="badge-dot"></span> ✓ Válido</span>
        <span>— Documentação em dia</span>
    </div>
    <div class="rpt-legend-item">
        <span class="badge badge-warn">! A expirar</span>
        <span>— Expira em menos de 60 dias</span>
    </div>
    <div class="rpt-legend-item">
        <span class="badge badge-expire">✗ Expirado</span>
        <span>— Documento fora de validade</span>
    </div>
</div>

<div class="rpt-footer">
    <div class="rpt-footer-left">FEM — Sistema de Gestão Interna &nbsp;·&nbsp; Confidencial</div>
    <div class="rpt-footer-right">Documento gerado automaticamente pelo sistema</div>
</div>

</div>{{-- /printArea --}}


{{-- ═══════ MODAIS (inalterados) ═══════ --}}

<div class="vt-overlay" id="modalVer">
    <div class="vt-modal vt-modal-lg">
        <div class="vt-modal-header">
            <div class="vt-modal-header-left">
                <div class="vt-modal-icon">🚛</div>
                <div>
                    <div class="vt-modal-title" id="verTitle">Detalhe da Viatura</div>
                    <div class="vt-modal-subtitle" id="verSubtitle">—</div>
                </div>
            </div>
            <button class="vt-modal-close" onclick="fecharModal('modalVer')">×</button>
        </div>
        <div class="vt-modal-body">
            <div class="form-section-label">Identificação</div>
            <div class="info-grid">
                <div class="info-cell"><div class="info-cell-label">Marca</div><div class="info-cell-value" id="ver-marca">—</div></div>
                <div class="info-cell"><div class="info-cell-label">Modelo</div><div class="info-cell-value" id="ver-modelo">—</div></div>
                <div class="info-cell"><div class="info-cell-label">Matrícula</div><div class="info-cell-value mono" id="ver-matricula">—</div></div>
                <div class="info-cell"><div class="info-cell-label">Cor</div><div class="info-cell-value" id="ver-cor">—</div></div>
                <div class="info-cell span-2"><div class="info-cell-label">Nº Chassi</div><div class="info-cell-value mono" id="ver-chassi">—</div></div>
            </div>
            <div class="form-section-label">Documentos & Validades</div>
            <div class="info-grid">
                <div class="info-cell"><div class="info-cell-label">Seguro</div><div class="info-cell-value" id="ver-seguro">—</div></div>
                <div class="info-cell"><div class="info-cell-label">IPO</div><div class="info-cell-value" id="ver-ipo">—</div></div>
            </div>
            <div class="form-section-label">Observações</div>
            <div class="info-grid">
                <div class="info-cell span-2"><div class="info-cell-label">Notas</div><div class="info-cell-value" id="ver-obs" style="white-space:pre-wrap">—</div></div>
            </div>
            <div class="form-section-label">Metadata</div>
            <div id="ver-meta" style="display:flex;flex-wrap:wrap;gap:6px;padding:4px 0;"></div>
            <div class="form-section-label">Ficheiros Anexos</div>
            <div class="docs-preview" id="ver-docs"></div>
        </div>
        <div class="vt-modal-footer">
            <button class="btn btn-secondary" onclick="fecharModal('modalVer')">Fechar</button>
            <button class="btn btn-primary" id="verEditarBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar
            </button>
        </div>
    </div>
</div>

<div class="vt-overlay" id="modalForm">
    <div class="vt-modal vt-modal-lg">
        <div class="vt-modal-header">
            <div class="vt-modal-header-left">
                <div class="vt-modal-icon" id="formIcon">🆕</div>
                <div>
                    <div class="vt-modal-title" id="formTitle">Nova Viatura</div>
                    <div class="vt-modal-subtitle" id="formSubtitle">Preencha os dados abaixo</div>
                </div>
            </div>
            <button class="vt-modal-close" onclick="fecharModal('modalForm')">×</button>
        </div>
        <div class="vt-modal-body">
            <form id="formViatura" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="formId">

                <div class="form-section-label">Identificação do Veículo</div>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label>Marca <span class="required">*</span></label>
                        <input type="text" name="marca" id="fMarca" placeholder="ex: Henred" required>
                    </div>
                    <div class="form-group">
                        <label>Modelo <span class="required">*</span></label>
                        <input type="text" name="modelo" id="fModelo" placeholder="ex: Link">
                    </div>
                    <div class="form-group">
                        <label>Cor</label>
                        <input type="text" name="cor" id="fCor" placeholder="ex: Branca">
                    </div>
                    <div class="form-group">
                        <label>Matrícula <span class="required">*</span></label>
                        <input type="text" name="matricula" id="fMatricula" placeholder="ex: AA 660 MP" required style="font-family:var(--font-mono)">
                    </div>
                    <div class="form-group span-2">
                        <label>Nº Chassi</label>
                        <input type="text" name="numero_chassi" id="fChassi" placeholder="ex: AHBDSB2FFEB012432" style="font-family:var(--font-mono)">
                    </div>
                </div>

                <div class="form-section-label">Validades</div>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label>Data do Seguro <span class="required">*</span></label>
                        <input type="date" name="seguro" id="fSeguro" required>
                    </div>
                    <div class="form-group">
                        <label>Data do IPO <span class="required">*</span></label>
                        <input type="date" name="ipo" id="fIpo" required>
                    </div>
                </div>

                <div class="form-section-label">Observações</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Notas / Observações</label>
                        <textarea name="observacoes" id="fObs" placeholder="Informações adicionais..."></textarea>
                    </div>
                </div>

                <div class="form-section-label">Metadata</div>
                <div class="form-group">
                    <label>Tags / Etiquetas</label>
                    <div class="meta-tags-wrap" id="metaTagsWrap" onclick="document.getElementById('metaInput').focus()">
                        <input type="text" id="metaInput" class="meta-tag-input" placeholder="Escreva e prima Enter…" onkeydown="adicionarTag(event)">
                    </div>
                    <input type="hidden" name="metadata" id="metadataHidden">
                    <p class="form-hint">Prima Enter ou vírgula para adicionar uma etiqueta.</p>
                </div>

                <div class="form-section-label">Documentos Anexos</div>
                <div class="form-group">
                    <label>Adicionar Ficheiros</label>
                    <div class="dropzone" id="dropzone">
                        <input type="file" name="documentos[]" id="fileInput" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" onchange="previewFicheiros(this.files)">
                        <div class="dropzone-icon">📎</div>
                        <div class="dropzone-text"><strong>Clique para seleccionar</strong> ou arraste aqui</div>
                        <div class="dropzone-hint">PDF, Word, Excel, Imagens · Máx. 10 MB por ficheiro</div>
                    </div>
                    <div class="file-list" id="fileList"></div>
                    <div id="existingFiles" class="file-list" style="margin-top:6px"></div>
                </div>
            </form>
        </div>
        <div class="vt-modal-footer">
            <button class="btn btn-secondary" onclick="fecharModal('modalForm')">Cancelar</button>
            <button class="btn btn-primary" onclick="submeterFormulario()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span id="formBtnLabel">Guardar Viatura</span>
            </button>
        </div>
    </div>
</div>

<div class="vt-overlay" id="modalEliminar">
    <div class="vt-modal vt-modal-sm">
        <div class="vt-modal-header">
            <div class="vt-modal-header-left">
                <div class="vt-modal-icon" style="background:var(--red-bg);color:var(--red)">🗑️</div>
                <div>
                    <div class="vt-modal-title">Eliminar Viatura</div>
                    <div class="vt-modal-subtitle">Esta acção é irreversível</div>
                </div>
            </div>
            <button class="vt-modal-close" onclick="fecharModal('modalEliminar')">×</button>
        </div>
        <div class="vt-modal-body">
            <p style="color:var(--ink-mid);font-size:14px;line-height:1.6">
                Tem a certeza que pretende eliminar a viatura com matrícula
                <strong id="eliminarMat" style="color:var(--navy);font-family:var(--font-mono)">—</strong>?
                <br>Todos os documentos associados serão apagados permanentemente.
            </p>
        </div>
        <div class="vt-modal-footer">
            <button class="btn btn-secondary" onclick="fecharModal('modalEliminar')">Cancelar</button>
            <form id="formEliminar" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Sim, eliminar</button>
            </form>
        </div>
    </div>
</div>

<div class="toast-wrap" id="toastWrap"></div>

@endsection

@push('scripts')
<script>
/* ── Modais ── */
function abrirModal(id) { const el = document.getElementById(id); if (!el) return; el.style.display = 'flex'; el.classList.add('active'); document.body.style.overflow = 'hidden'; }
function fecharModal(id) { const el = document.getElementById(id); if (!el) return; el.style.display = 'none'; el.classList.remove('active'); document.body.style.overflow = ''; }
document.querySelectorAll('.vt-overlay').forEach(o => o.addEventListener('click', e => { if (e.target === o) fecharModal(o.id); }));
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('.vt-overlay.active').forEach(m => fecharModal(m.id)); });

function toast(msg, type = '') {
    const w = document.getElementById('toastWrap');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<span>${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span> ${msg}`;
    w.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

/* ── Dados ── */
const _viaturas = @json($viaturas);
function _getViatura(id) { return _viaturas.find(v => String(v.id) === String(id)); }

/* ── Pesquisa ── */
function filtrarTabela(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#tabelaViaturas tbody tr[data-search]').forEach(r => {
        r.style.display = r.dataset.search.includes(q) ? '' : 'none';
    });
}

/* ── Modal Ver ── */
let _viaturaAtual = null;
function abrirModalVer(id) {
    const v = _getViatura(id); if (!v) return; _viaturaAtual = v;
    document.getElementById('verTitle').textContent      = (v.marca||'') + ' ' + (v.modelo||'');
    document.getElementById('verSubtitle').textContent   = v.matricula || '—';
    document.getElementById('ver-marca').textContent     = v.marca         || '—';
    document.getElementById('ver-modelo').textContent    = v.modelo        || '—';
    document.getElementById('ver-matricula').textContent = v.matricula     || '—';
    document.getElementById('ver-cor').textContent       = v.cor           || '—';
    document.getElementById('ver-chassi').textContent    = v.numero_chassi || '—';
    document.getElementById('ver-seguro').innerHTML      = dateBadgeHtml(v.seguro);
    document.getElementById('ver-ipo').innerHTML         = dateBadgeHtml(v.ipo);
    document.getElementById('ver-obs').textContent       = v.observacoes   || '(Sem observações)';
    const metaEl = document.getElementById('ver-meta'); metaEl.innerHTML = '';
    let tags = [];
    try { tags = typeof v.metadata === 'string' ? JSON.parse(v.metadata || '[]') : (Array.isArray(v.metadata) ? v.metadata : []); } catch(e) { tags = []; }
    if (tags.length) { tags.forEach(t => { const s = document.createElement('span'); s.className = 'meta-tag'; s.textContent = t; metaEl.appendChild(s); }); }
    else { metaEl.innerHTML = '<span style="color:#94a3b8;font-size:13px">(sem etiquetas)</span>'; }
    const docsEl = document.getElementById('ver-docs'); docsEl.innerHTML = '';
    const docs = Array.isArray(v.documentos) ? v.documentos : [];
    if (docs.length) { docs.forEach(d => { const nome = d.nome||d.name||d.original_name||'Ficheiro'; const path = d.path||d.file_path||''; const a = document.createElement('a'); a.className = 'doc-chip'; a.href = '/storage/' + path; a.target = '_blank'; a.textContent = fileIcon(nome) + ' ' + nome; docsEl.appendChild(a); }); }
    else { docsEl.innerHTML = '<span style="color:#94a3b8;font-size:13px">(sem documentos)</span>'; }
    document.getElementById('verEditarBtn').onclick = () => { fecharModal('modalVer'); abrirModalEditar(v.id); };
    abrirModal('modalVer');
}

function dateBadgeHtml(dateStr) {
    if (!dateStr) return '<span style="color:#94a3b8">—</span>';
    let d;
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) { const [dd,mm,yyyy] = dateStr.split('/'); d = new Date(`${yyyy}-${mm}-${dd}`); }
    else { d = new Date(dateStr); }
    if (isNaN(d)) return '<span style="color:#94a3b8">' + dateStr + '</span>';
    const days = Math.floor((d - new Date()) / 86400000);
    const cls  = days < 0 ? 'date-expire' : days <= 60 ? 'date-warn' : 'date-ok';
    return '<span class="date-badge ' + cls + '"><span class="date-dot"></span>' + d.toLocaleDateString('pt-PT') + '</span>';
}
function fileIcon(name) { const ext = name.split('.').pop().toLowerCase(); if (['pdf'].includes(ext)) return '📄'; if (['doc','docx'].includes(ext)) return '📝'; if (['xls','xlsx'].includes(ext)) return '📊'; if (['jpg','jpeg','png'].includes(ext)) return '🖼️'; return '📎'; }

/* ── Modal Criar/Editar ── */
let _tagsActuais = [];
function abrirModalNova() {
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formId').value = '';
    document.getElementById('formViatura').action = '{{ route("viaturas.store") }}';
    ['fMarca','fModelo','fCor','fMatricula','fChassi','fSeguro','fIpo','fObs'].forEach(id => { document.getElementById(id).value = ''; });
    document.getElementById('formIcon').textContent = '🆕';
    document.getElementById('formTitle').textContent = 'Nova Viatura';
    document.getElementById('formSubtitle').textContent = 'Preencha os dados da nova viatura';
    document.getElementById('formBtnLabel').textContent = 'Guardar Viatura';
    document.getElementById('fileList').innerHTML = '';
    document.getElementById('existingFiles').innerHTML = '';
    _tagsActuais = []; renderizarTags(); abrirModal('modalForm');
}
function abrirModalEditar(id) {
    const v = _getViatura(id); if (!v) return;
    abrirModal('modalForm');
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formId').value = v.id;
    document.getElementById('formIcon').textContent = '✏️';
    document.getElementById('formTitle').textContent = 'Editar — ' + (v.matricula||'');
    document.getElementById('formSubtitle').textContent = (v.marca||'') + ' ' + (v.modelo||'');
    document.getElementById('formBtnLabel').textContent = 'Actualizar Viatura';
    document.getElementById('formViatura').action = '/viaturas/' + v.id;
    document.getElementById('fMarca').value = v.marca || '';
    document.getElementById('fModelo').value = v.modelo || '';
    document.getElementById('fCor').value = v.cor || '';
    document.getElementById('fMatricula').value = v.matricula || '';
    document.getElementById('fChassi').value = v.numero_chassi || '';
    document.getElementById('fObs').value = v.observacoes || '';
    document.getElementById('fSeguro').value = toInputDate(v.seguro);
    document.getElementById('fIpo').value = toInputDate(v.ipo);
    try { _tagsActuais = typeof v.metadata === 'string' ? JSON.parse(v.metadata||'[]') : (Array.isArray(v.metadata) ? v.metadata : []); } catch(e) { _tagsActuais = []; }
    renderizarTags();
    const ef = document.getElementById('existingFiles'); ef.innerHTML = '';
    const docs = Array.isArray(v.documentos) ? v.documentos : [];
    docs.forEach(d => { const nome = d.nome||d.name||d.original_name||'Ficheiro'; ef.innerHTML += '<div class="file-item"><span class="file-icon">'+fileIcon(nome)+'</span><div class="file-info"><div class="file-name">'+nome+'</div><div class="file-size">Ficheiro existente</div></div><button type="button" class="file-remove" onclick="eliminarDocExistente('+d.id+', this)">&times;</button></div>'; });
    document.getElementById('fileList').innerHTML = '';
}
function toInputDate(str) { if (!str) return ''; if (/^\d{4}-\d{2}-\d{2}/.test(str)) return str.substring(0,10); if (/^\d{2}\/\d{2}\/\d{4}$/.test(str)) { const [dd,mm,yyyy] = str.split('/'); return yyyy+'-'+mm+'-'+dd; } return str; }
function submeterFormulario() { document.getElementById('metadataHidden').value = JSON.stringify(_tagsActuais); document.getElementById('formViatura').submit(); }

/* ── Tags ── */
function adicionarTag(e) { if (e.key !== 'Enter' && e.key !== ',') return; e.preventDefault(); const val = e.target.value.trim().replace(/,$/, ''); if (val && !_tagsActuais.includes(val)) { _tagsActuais.push(val); renderizarTags(); } e.target.value = ''; }
function removerTag(idx) { _tagsActuais.splice(idx, 1); renderizarTags(); }
function renderizarTags() { const wrap = document.getElementById('metaTagsWrap'); const input = document.getElementById('metaInput'); wrap.innerHTML = ''; _tagsActuais.forEach((t,i) => { const span = document.createElement('span'); span.className = 'meta-tag'; span.innerHTML = `${t}<button type="button" class="meta-tag-remove" onclick="removerTag(${i})">×</button>`; wrap.appendChild(span); }); wrap.appendChild(input); input.focus(); }

/* ── Ficheiros ── */
function previewFicheiros(files) { const list = document.getElementById('fileList'); list.innerHTML = ''; Array.from(files).forEach(f => { const size = f.size > 1024*1024 ? (f.size/1024/1024).toFixed(1)+' MB' : Math.round(f.size/1024)+' KB'; list.innerHTML += `<div class="file-item"><span class="file-icon">${fileIcon(f.name)}</span><div class="file-info"><div class="file-name">${f.name}</div><div class="file-size">${size}</div></div></div>`; }); }
const dz = document.getElementById('dropzone');
if (dz) { dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); }); dz.addEventListener('dragleave', () => dz.classList.remove('dragover')); dz.addEventListener('drop', e => { e.preventDefault(); dz.classList.remove('dragover'); document.getElementById('fileInput').files = e.dataTransfer.files; previewFicheiros(e.dataTransfer.files); }); }

function eliminarDocExistente(docId, btn) { if (!confirm('Remover este documento?')) return; fetch(`/viaturas/documento/${docId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(r => r.json()).then(d => { if (d.success) { btn.closest('.file-item').remove(); toast('Documento removido', 'success'); } else toast('Erro ao remover documento', 'error'); }).catch(() => toast('Erro de ligação', 'error')); }

/* ── Eliminar viatura ── */
function confirmarEliminar(id, matricula) { document.getElementById('eliminarMat').textContent = matricula; document.getElementById('formEliminar').action = `/viaturas/${id}`; abrirModal('modalEliminar'); }

/* ── Exportar ── */
function exportar() { window.location.href = '{{ route("viaturas.export") }}'; }

/* ══════════════════════════════════════
   IMPRIMIR / PDF
══════════════════════════════════════ */
function imprimirRelatorio() {
    // Preencher data/hora actual
    const agora = new Date();
    const fmt = agora.toLocaleDateString('pt-PT', { day:'2-digit', month:'long', year:'numeric' })
              + ' às '
              + agora.toLocaleTimeString('pt-PT', { hour:'2-digit', minute:'2-digit' });
    document.getElementById('rptDate').textContent = fmt;

    // Mostrar área de impressão e ocultar o resto
    const printArea = document.getElementById('printArea');
    printArea.style.display = 'block';

    // Guardar e substituir o conteúdo do body
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printArea.outerHTML;

    window.print();

    // Restaurar após imprimir
    document.body.innerHTML = originalContents;
    window.location.reload();
}

/* ── Flash ── */
@if(session('success'))
    window.addEventListener('load', () => toast('{{ session("success") }}', 'success'));
@endif
@if(session('error'))
    window.addEventListener('load', () => toast('{{ session("error") }}', 'error'));
@endif
</script>
@endpush