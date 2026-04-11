<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ══ RESET / BASE ══ */
    *, *::before, *::after { box-sizing: border-box; }

    /* ══ KPI CARDS ══ */
    .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
    .kpi-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 14px 18px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .kpi-card.green { border-left: 4px solid #16a34a; }
    .kpi-card.red   { border-left: 4px solid #dc2626; }
    .kpi-card.blue  { border-left: 4px solid #1a56db; }
    .kpi-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .95rem; flex-shrink: 0; }
    .kpi-icon.green { background: #f0fdf4; color: #16a34a; }
    .kpi-icon.red   { background: #fef2f2; color: #dc2626; }
    .kpi-icon.blue  { background: #eff6ff; color: #1a56db; }
    .kpi-label { font-size: .58rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 1px; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1; }

    /* ══ PAGE HEADER ══ */
    .page-header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; gap: 12px; flex-wrap: wrap; }

    /* ══ FILTER CARD ══ */
    .filter-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .filter-grid { display: grid; grid-template-columns: repeat(5, 1fr) auto; gap: 10px; align-items: flex-end; }
    .filter-label { font-size: .65rem; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: #64748b; margin-bottom: 4px; display: block; }
    .filter-input, .filter-select { width: 100%; padding: 7px 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .8rem; color: #334155; background: #fff; transition: border-color .15s, box-shadow .15s; }
    .filter-input:focus, .filter-select:focus { outline: none; border-color: #64748b; box-shadow: 0 0 0 3px rgba(100,116,139,.1); }

    /* ══ TABLE CARD ══ */
    .table-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.04); overflow: hidden; }
    .table-card-head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .table-card-icon { width: 32px; height: 32px; background: #1e293b; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: .85rem; flex-shrink: 0; }

    /* ══ TABLE ══ */
    .mov-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .mov-table thead th { font-size: .6rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 9px 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mov-table tbody td { font-size: .8rem; color: #334155; padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; overflow: hidden; text-overflow: ellipsis; }
    .mov-table tbody tr:last-child td { border-bottom: none; }
    .mov-table tbody tr:hover { background: #f8fafc; }

    /* ══ BADGES ══ */
    .badge-entrada { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: .68rem; font-weight: 700; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .badge-saida   { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: .68rem; font-weight: 700; background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* ══ AVATAR ══ */
    .avatar { width: 28px; height: 28px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 700; color: #475569; flex-shrink: 0; }

    /* ══ BUTTONS ══ */
    .btn-primary-dark { background: #1e293b; color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-size: .8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background .15s; white-space: nowrap; }
    .btn-primary-dark:hover { background: #334155; }
    .btn-outline-sm { background: transparent; border: 1px solid #e2e8f0; border-radius: 6px; padding: 5px 8px; font-size: .75rem; color: #64748b; cursor: pointer; transition: all .15s; display: inline-flex; align-items: center; gap: 4px; }
    .btn-outline-sm:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .btn-danger-sm { background: transparent; border: 1px solid #fecaca; border-radius: 6px; padding: 5px 8px; font-size: .75rem; color: #dc2626; cursor: pointer; transition: all .15s; display: inline-flex; align-items: center; gap: 4px; }
    .btn-danger-sm:hover { background: #fef2f2; }
    .btn-filter { background: #1e293b; color: #fff; border: none; border-radius: 8px; padding: 7px 14px; font-size: .8rem; font-weight: 600; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; }
    .btn-filter-clear { background: #fff; color: #64748b; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 14px; font-size: .8rem; font-weight: 600; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
    .btn-filter-clear:hover { background: #f1f5f9; color: #334155; }
    .btn-print { background: #fff; color: #334155; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: .8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .15s; white-space: nowrap; text-decoration: none; }
    .btn-print:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }

    /* ══ STOCK INFO (modal) ══ */
    #stock-disponivel-wrap { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; font-size: .8rem; color: #334155; display: none; margin-top: 8px; }
    #stock-disponivel-wrap.show { display: flex; align-items: center; justify-content: space-between; }
    #stock-valor { font-weight: 800; font-size: 1rem; color: #1e293b; }

    /* ══ MODAL ══ */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.45); z-index: 1050; align-items: center; justify-content: center; padding: 16px; }
    .modal-overlay.show { display: flex; }
    .modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 520px; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: modalIn .2s ease; }
    @keyframes modalIn { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
    .modal-header { padding: 20px 24px 0; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    .modal-title { font-size: .95rem; font-weight: 800; color: #1e293b; }
    .modal-subtitle { font-size: .72rem; color: #94a3b8; margin-top: 2px; }
    .modal-close { background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.1rem; padding: 0; line-height: 1; flex-shrink: 0; }
    .modal-close:hover { color: #334155; }
    .modal-body { padding: 20px 24px; }
    .modal-footer { padding: 0 24px 20px; display: flex; justify-content: flex-end; gap: 8px; }
    .form-label { font-size: .68rem; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: #64748b; margin-bottom: 4px; display: block; }
    .form-control-custom { width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .82rem; color: #334155; transition: border-color .15s, box-shadow .15s; }
    .form-control-custom:focus { outline: none; border-color: #64748b; box-shadow: 0 0 0 3px rgba(100,116,139,.1); }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .tipo-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .tipo-btn { padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px; cursor: pointer; text-align: center; transition: all .15s; background: #fff; }
    .tipo-btn:hover { border-color: #cbd5e1; background: #f8fafc; }
    .tipo-btn.selected.entrada { border-color: #16a34a; background: #f0fdf4; }
    .tipo-btn.selected.saida   { border-color: #dc2626; background: #fef2f2; }
    .tipo-btn-icon  { font-size: 1.2rem; display: block; margin-bottom: 3px; }
    .tipo-btn-label { font-size: .75rem; font-weight: 700; color: #334155; }
    .tipo-btn.selected.entrada .tipo-btn-label { color: #166534; }
    .tipo-btn.selected.saida   .tipo-btn-label { color: #991b1b; }

    /* ══ CONFIRM MODAL ══ */
    .confirm-box { background: #fff; border-radius: 16px; width: 100%; max-width: 380px; box-shadow: 0 20px 60px rgba(0,0,0,.2); padding: 28px 24px 20px; text-align: center; animation: modalIn .2s ease; }
    .confirm-icon { width: 52px; height: 52px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #dc2626; margin: 0 auto 14px; }
    .confirm-title { font-size: .95rem; font-weight: 800; color: #1e293b; margin-bottom: 6px; }
    .confirm-desc  { font-size: .8rem; color: #64748b; margin-bottom: 20px; line-height: 1.5; }
    .confirm-actions { display: flex; gap: 8px; justify-content: center; }
    .btn-confirm-cancel { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 18px; font-size: .82rem; font-weight: 600; color: #64748b; cursor: pointer; }
    .btn-confirm-cancel:hover { background: #f1f5f9; }
    .btn-confirm-delete { background: #dc2626; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: .82rem; font-weight: 600; cursor: pointer; }
    .btn-confirm-delete:hover { background: #b91c1c; }

    /* ══ OBS TRUNCATE ══ */
    .obs-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; display: block; color: #94a3b8; font-size: .75rem; }

    /* ══ PAGINATION ══ */
    .pagination-wrap { padding: 12px 20px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: .75rem; color: #94a3b8; }
    .pagination-wrap .page-link { color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 9px; font-size: .75rem; text-decoration: none; }
    .pagination-wrap .page-item.active .page-link { background: #1e293b; color: #fff; border-color: #1e293b; }
    .pagination-wrap .page-item.disabled .page-link { opacity: .4; pointer-events: none; }

    /* ══ RESPONSIVE ══ */
    @media (max-width: 992px) { .filter-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) {
        .kpi-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .filter-grid { grid-template-columns: 1fr 1fr; }
        .mov-table th:nth-child(5), .mov-table td:nth-child(5) { display: none; }
        .page-header-row { flex-direction: column; align-items: flex-start; }
    }
    @media (max-width: 480px) {
        .kpi-grid { grid-template-columns: 1fr 1fr 1fr; gap: 6px; }
        .kpi-value { font-size: 1.1rem; }
        .filter-grid { grid-template-columns: 1fr; }
        .form-row-2 { grid-template-columns: 1fr; }
    }
</style>

{{-- ═══════════════════════════════════════════════
     CONTEÚDO PRINCIPAL
═══════════════════════════════════════════════ --}}
<div style="padding: 28px 24px 64px;">

    {{-- ALERTAS --}}
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius:10px;font-size:.82rem;">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="border-radius:10px;font-size:.82rem;">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger mb-3" style="border-radius:10px;font-size:.82rem;">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- CABEÇALHO --}}
    <div class="page-header-row">
        <div>
            <div style="font-size:.58rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:3px;">Armazém</div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.25rem;">Movimentos de Stock</h4>
            <p style="font-size:.75rem;color:#94a3b8;margin:0;">Entradas e saídas de materiais</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            {{-- ── BOTÃO PDF: passa os filtros activos como query string ── --}}
            <a class="btn-print"
               href="{{ route('movimentos.pdf', request()->query()) }}"
               target="_blank">
                <i class="bi bi-file-earmark-pdf-fill"></i> Exportar PDF
            </a>
            <button class="btn-primary-dark" onclick="abrirModalNovo()">
                <i class="bi bi-plus-lg"></i> Registar Movimento
            </button>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card green">
            <div class="kpi-icon green"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div>
                <div class="kpi-label">Entradas</div>
                <div class="kpi-value">{{ $totalEntradas }}</div>
            </div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-icon red"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <div>
                <div class="kpi-label">Saídas</div>
                <div class="kpi-value">{{ $totalSaidas }}</div>
            </div>
        </div>
        <div class="kpi-card blue">
            <div class="kpi-icon blue"><i class="bi bi-calendar-check-fill"></i></div>
            <div>
                <div class="kpi-label">Hoje</div>
                <div class="kpi-value">{{ $movimentosHoje }}</div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('movimentos.index') }}">
            <div class="filter-grid">
                <div>
                    <label class="filter-label">Produto</label>
                    <select name="stock_item_id" class="filter-select">
                        <option value="">Todos</option>
                        @foreach($stockItems as $p)
                        <option value="{{ $p->id }}" {{ request('stock_item_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nome }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="filter-label">Tipo</label>
                    <select name="tipo" class="filter-select">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="saida"   {{ request('tipo') === 'saida'   ? 'selected' : '' }}>Saída</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Responsável</label>
                    <input type="text" name="responsavel" class="filter-input" placeholder="Nome…" value="{{ request('responsavel') }}">
                </div>
                <div>
                    <label class="filter-label">De</label>
                    <input type="date" name="data_inicio" class="filter-input" value="{{ request('data_inicio') }}">
                </div>
                <div>
                    <label class="filter-label">Até</label>
                    <input type="date" name="data_fim" class="filter-input" value="{{ request('data_fim') }}">
                </div>
                <div style="display:flex;gap:6px;">
                    <button type="submit" class="btn-filter"><i class="bi bi-funnel-fill"></i> Filtrar</button>
                    @if(request()->hasAny(['stock_item_id','tipo','responsavel','data_inicio','data_fim']))
                    <a href="{{ route('movimentos.index') }}" class="btn-filter-clear"><i class="bi bi-x"></i> Limpar</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- TABELA --}}
    <div class="table-card">

        {{-- Cabeçalho do card --}}
        <div class="table-card-head">
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="table-card-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <div style="font-size:.82rem;font-weight:700;color:#334155;">Histórico de Movimentos</div>
                    <div style="font-size:.7rem;color:#94a3b8;">{{ $movimentos->total() }} registos encontrados</div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="table-responsive">
            <table class="mov-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Produto</th>
                        <th style="width:110px;">Tipo</th>
                        <th style="width:90px;">Qtd.</th>
                        <th style="width:140px;">Responsável</th>
                        <th style="width:200px;">Observações</th>
                        <th style="width:110px;">Data</th>
                        <th style="width:80px;" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimentos as $mov)
                    <tr>
                        <td style="color:#94a3b8;font-size:.72rem;">{{ $mov->id }}</td>
                        <td>
                            <div style="font-weight:600;font-size:.82rem;">{{ $mov->stockItem->nome ?? '—' }}</div>
                            <div style="font-size:.7rem;color:#94a3b8;">{{ $mov->stockItem->referencia ?? '' }}</div>
                        </td>
                        <td>
                            @if($mov->tipo === 'entrada')
                                <span class="badge-entrada"><i class="bi bi-arrow-down-circle-fill"></i> Entrada</span>
                            @else
                                <span class="badge-saida"><i class="bi bi-arrow-up-circle-fill"></i> Saída</span>
                            @endif
                        </td>
                        <td style="font-weight:700;font-size:.85rem;">
                            {{ number_format($mov->quantidade, 2, ',', '.') }}
                            @if($mov->stockItem && $mov->stockItem->metadata && isset($mov->stockItem->metadata['unidade']))
                                <span style="font-size:.7rem;color:#94a3b8;font-weight:400;">{{ $mov->stockItem->metadata['unidade'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div class="avatar">{{ strtoupper(substr($mov->responsavel ?? 'U', 0, 1)) }}</div>
                                <span style="font-size:.78rem;">{{ $mov->responsavel }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="obs-text" title="{{ $mov->observacoes }}">
                                {{ $mov->observacoes ?: '—' }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:.78rem;font-weight:600;">{{ $mov->created_at->format('d/m/Y') }}</div>
                            <div style="font-size:.68rem;color:#94a3b8;">{{ $mov->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end">
                            <div style="display:flex;justify-content:flex-end;gap:4px;">
                                <button class="btn-outline-sm"
                                    title="Editar"
                                    onclick="abrirModalEditar(
                                        {{ $mov->id }},
                                        '{{ addslashes($mov->responsavel) }}',
                                        '{{ addslashes($mov->observacoes ?? '') }}'
                                    )">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-danger-sm"
                                    title="Anular movimento"
                                    onclick="confirmarAnulacao(
                                        {{ $mov->id }},
                                        '{{ $mov->tipo === 'entrada' ? 'entrada' : 'saída' }}',
                                        '{{ addslashes($mov->stockItem->nome ?? '') }}',
                                        {{ $mov->quantidade }}
                                    )">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px 20px;color:#94a3b8;font-size:.85rem;">
                            <i class="bi bi-inbox" style="font-size:1.8rem;display:block;margin-bottom:8px;opacity:.4;"></i>
                            Nenhum movimento encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($movimentos->hasPages())
        <div class="pagination-wrap">
            <span>A mostrar {{ $movimentos->firstItem() }}–{{ $movimentos->lastItem() }} de {{ $movimentos->total() }}</span>
            <div>{{ $movimentos->links() }}</div>
        </div>
        @endif

    </div>{{-- /table-card --}}

</div>{{-- /page wrap --}}


{{-- ═══════════════════════════════════════════════
     MODAL — NOVO MOVIMENTO
═══════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalNovo">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><i class="bi bi-plus-circle me-2" style="color:#1a56db;"></i>Registar Movimento</div>
                <div class="modal-subtitle">Entrada adiciona ao stock · Saída dá baixa automática</div>
            </div>
            <button class="modal-close" onclick="fecharModal('modalNovo')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" action="{{ route('movimentos.store') }}" id="formNovo">
            @csrf
            <div class="modal-body">

                {{-- Tipo --}}
                <div style="margin-bottom:14px;">
                    <label class="form-label">Tipo de Movimento</label>
                    <div class="tipo-toggle">
                        <div class="tipo-btn entrada selected" id="btn-entrada" onclick="selecionarTipo('entrada')">
                            <span class="tipo-btn-icon">📥</span>
                            <span class="tipo-btn-label">Entrada</span>
                        </div>
                        <div class="tipo-btn saida" id="btn-saida" onclick="selecionarTipo('saida')">
                            <span class="tipo-btn-icon">📤</span>
                            <span class="tipo-btn-label">Saída</span>
                        </div>
                    </div>
                    <input type="hidden" name="tipo" id="input-tipo" value="entrada">
                </div>

                {{-- Produto --}}
                <div style="margin-bottom:14px;">
                    <label class="form-label" for="stock_item_id">Produto / Material</label>
                    <select name="stock_item_id" id="stock_item_id" class="form-control-custom"
                            onchange="carregarStock(this.value)" required>
                        <option value="">— Selecione um produto —</option>
                        @foreach($stockItems as $p)
                        <option value="{{ $p->id }}"
                                data-stock="{{ $p->quantidade }}"
                                data-unidade="{{ $p->metadata['unidade'] ?? '' }}"
                                {{ old('stock_item_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nome }}
                            @if($p->referencia) ({{ $p->referencia }})@endif
                        </option>
                        @endforeach
                    </select>

                    {{-- Stock disponível --}}
                    <div id="stock-disponivel-wrap">
                        <span>Stock disponível:</span>
                        <span>
                            <span id="stock-valor">—</span>
                            <span id="stock-unidade" style="font-size:.75rem;color:#94a3b8;margin-left:3px;"></span>
                        </span>
                    </div>
                </div>

                {{-- Quantidade + Responsável --}}
                <div class="form-row-2" style="margin-bottom:14px;">
                    <div>
                        <label class="form-label" for="quantidade">Quantidade</label>
                        <input type="number" name="quantidade" id="quantidade"
                               class="form-control-custom"
                               min="0.01" step="0.01" placeholder="0.00"
                               value="{{ old('quantidade') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="responsavel">Responsável</label>
                        <input type="text" name="responsavel" id="responsavel"
                               class="form-control-custom"
                               placeholder="Nome do operador…"
                               value="{{ old('responsavel') }}" required>
                    </div>
                </div>

                {{-- Observações --}}
                <div>
                    <label class="form-label" for="observacoes">
                        Observações
                        <span style="font-weight:400;text-transform:none;letter-spacing:0;">(opcional)</span>
                    </label>
                    <textarea name="observacoes" id="observacoes"
                              class="form-control-custom" rows="2"
                              placeholder="Ex: Entregue para reparação da máquina X…">{{ old('observacoes') }}</textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-confirm-cancel" onclick="fecharModal('modalNovo')">Cancelar</button>
                <button type="submit" class="btn-primary-dark">
                    <i class="bi bi-check-lg"></i>
                    <span id="btn-submit-label">Registar Entrada</span>
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════════
     MODAL — EDITAR MOVIMENTO
═══════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalEditar">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><i class="bi bi-pencil me-2" style="color:#f59e0b;"></i>Editar Movimento</div>
                <div class="modal-subtitle">Apenas responsável e observações são editáveis</div>
            </div>
            <button class="modal-close" onclick="fecharModal('modalEditar')"><i class="bi bi-x-lg"></i></button>
        </div>
        <form method="POST" id="formEditar">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div style="margin-bottom:14px;">
                    <label class="form-label" for="edit-responsavel">Responsável</label>
                    <input type="text" name="responsavel" id="edit-responsavel"
                           class="form-control-custom" required>
                </div>
                <div>
                    <label class="form-label" for="edit-observacoes">Observações</label>
                    <textarea name="observacoes" id="edit-observacoes"
                              class="form-control-custom" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-confirm-cancel" onclick="fecharModal('modalEditar')">Cancelar</button>
                <button type="submit" class="btn-primary-dark"><i class="bi bi-check-lg"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════════════
     MODAL — CONFIRMAR ANULAÇÃO
═══════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalConfirmar">
    <div style="display:flex;align-items:center;justify-content:center;width:100%;">
        <div class="confirm-box">
            <div class="confirm-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="confirm-title">Anular movimento?</div>
            <div class="confirm-desc" id="confirm-desc">Esta ação irá reverter o stock do produto.</div>
            <div class="confirm-actions">
                <button class="btn-confirm-cancel" onclick="fecharModal('modalConfirmar')">Cancelar</button>
                <form id="formAnular" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">Anular</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
/* ══ Abrir / fechar modais ════════════════════════ */
function abrirModalNovo() {
    document.getElementById('modalNovo').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function fecharModal(id) {
    document.getElementById(id).classList.remove('show');
    document.body.style.overflow = '';
}

/* Fechar ao clicar fora */
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) fecharModal(overlay.id);
    });
});

/* Fechar com Escape */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(function(m) {
            fecharModal(m.id);
        });
    }
});

/* ══ Tipo entrada / saída ═════════════════════════ */
function selecionarTipo(tipo) {
    document.getElementById('input-tipo').value = tipo;

    var btnE = document.getElementById('btn-entrada');
    var btnS = document.getElementById('btn-saida');

    btnE.classList.toggle('selected', tipo === 'entrada');
    btnE.classList.toggle('entrada',  tipo === 'entrada');
    btnS.classList.toggle('selected', tipo === 'saida');
    btnS.classList.toggle('saida',    tipo === 'saida');

    document.getElementById('btn-submit-label').textContent =
        tipo === 'entrada' ? 'Registar Entrada' : 'Registar Saída';

    /* Mostrar stock se produto já selecionado */
    var wrap = document.getElementById('stock-disponivel-wrap');
    if (wrap.dataset.loaded) wrap.classList.add('show');
}

/* ══ Carregar stock do produto ════════════════════ */
function carregarStock(produtoId) {
    var wrap      = document.getElementById('stock-disponivel-wrap');
    var valorEl   = document.getElementById('stock-valor');
    var unidadeEl = document.getElementById('stock-unidade');

    if (!produtoId) {
        wrap.classList.remove('show');
        wrap.dataset.loaded = '';
        return;
    }

    var option = document.querySelector('#stock_item_id option[value="' + produtoId + '"]');
    if (option) {
        var stock   = parseFloat(option.dataset.stock || 0);
        var unidade = option.dataset.unidade || '';

        valorEl.textContent   = stock.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        unidadeEl.textContent = unidade;
        wrap.classList.add('show');
        wrap.dataset.loaded = '1';

        valorEl.style.color = stock <= 0 ? '#dc2626'
                            : stock < 5  ? '#f59e0b'
                            : '#16a34a';
    }
}

/* ══ Abrir modal editar ════════════════════════════ */
function abrirModalEditar(id, responsavel, observacoes) {
    document.getElementById('formEditar').action = "{{ url('/movimentos') }}/" + id;
    document.getElementById('edit-responsavel').value = responsavel;
    document.getElementById('edit-observacoes').value = observacoes;
    document.getElementById('modalEditar').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/* ══ Confirmar anulação ════════════════════════════ */
function confirmarAnulacao(id, tipo, produto, quantidade) {
    document.getElementById('formAnular').action = "{{ url('/movimentos') }}/" + id;

    var qtd  = parseFloat(quantidade).toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    var acao = tipo === 'entrada' ? 'retirado do stock' : 'devolvido ao stock';

    document.getElementById('confirm-desc').innerHTML =
        'Vai anular <strong>' + tipo + '</strong> de <strong>' + qtd + '</strong> × <strong>' + produto + '</strong>.<br>' +
        'O stock será <strong>' + acao + '</strong> automaticamente.';

    document.getElementById('modalConfirmar').classList.add('show');
    document.body.style.overflow = 'hidden';
}

/* ══ Abrir modal se houver erros de validação ══════ */
@if($errors->any())
    abrirModalNovo();
    @if(old('tipo') === 'saida')
        selecionarTipo('saida');
    @endif
@endif

/* ══ Restaurar tipo se old() existir ═══════════════ */
@if(old('tipo') === 'saida')
    selecionarTipo('saida');
@endif

/* ══ Restaurar produto se old() existir ════════════ */
@if(old('stock_item_id'))
    carregarStock('{{ old('stock_item_id') }}');
@endif
</script>

</x-app-layout>