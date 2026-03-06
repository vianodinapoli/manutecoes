<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* ── KPI Cards ── */
    .kpi-card { background:#fff; border-radius:14px; border:1px solid #e9ecef; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:transform 0.15s,box-shadow 0.15s; height:100%; position:relative; overflow:hidden; }
    .kpi-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,0.08); }
    .kpi-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:14px 0 0 14px; }
    .kpi-card.green::before  { background:#198754; }
    .kpi-card.blue::before   { background:#1a56db; }
    .kpi-card.red::before    { background:#dc3545; }
    .kpi-card.teal::before   { background:#0dcaf0; }
    .kpi-card.gold::before   { background:#fd7e14; }
    .kpi-card.purple::before { background:#6f42c1; }
    .kpi-label { font-size:0.65rem; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#adb5bd; margin-bottom:6px; }
    .kpi-value { font-size:1.7rem; font-weight:800; line-height:1; margin-bottom:2px; }
    .kpi-sub   { font-size:0.72rem; color:#adb5bd; }
    .kpi-icon  { position:absolute; right:16px; top:50%; transform:translateY(-50%); font-size:2rem; opacity:0.08; }

    /* ── Painel / Cards genéricos ── */
    .panel-card { background:#fff; border-radius:14px; border:1px solid #e9ecef; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    .panel-title { font-size:0.72rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#adb5bd; display:flex; align-items:center; gap:8px; }

    /* ── Tanque visor ── */
    .tank-visor { height:38px; background:#f1f3f5; border-radius:8px; border:1px solid #e9ecef; overflow:hidden; position:relative; }
    .tank-level { height:100%; transition:width 1s ease-in-out; background:linear-gradient(90deg,#198754,#20c997); }
    .tank-level.low { background:linear-gradient(90deg,#dc3545,#ff6b6b); }
    .tank-text { position:absolute; width:100%; text-align:center; top:50%; transform:translateY(-50%); font-weight:700; color:#212529; font-size:0.72rem; }

    /* ── Filtro ── */
    .filter-panel { background:#fff; border-radius:14px; border:1px solid #e9ecef; padding:18px 22px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    .filter-group { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; }
    .filter-item { display:flex; flex-direction:column; gap:4px; }
    .filter-item label { font-size:0.68rem; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; color:#6c757d; }
    .filter-item select, .filter-item input { border:1px solid #dee2e6; border-radius:8px; padding:7px 11px; font-size:0.82rem; color:#343a40; background:#f8f9fa; outline:none; transition:border-color 0.2s,box-shadow 0.2s; min-width:120px; }
    .filter-item select:focus, .filter-item input:focus { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,0.1); background:#fff; }
    .btn-filter { padding:8px 18px; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; border:none; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s; }
    .btn-filter-apply { background:#0d6efd; color:#fff; }
    .btn-filter-apply:hover { background:#0b5ed7; }
    .btn-filter-clear { background:#f1f3f5; color:#495057; border:1px solid #dee2e6; }
    .btn-filter-clear:hover { background:#e9ecef; }

    /* ── Tabela ── */
    .table-card { background:#fff; border-radius:14px; border:1px solid #e9ecef; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    #fuelTable thead tr { background:#f8f9fa; }
    #fuelTable thead th { font-size:0.67rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#868e96; border-bottom:1px solid #e9ecef; padding:8px 12px; white-space:nowrap; }
    #fuelTable tbody td { padding:7px 12px; vertical-align:middle; border-bottom:1px solid #f1f3f5; font-size:0.82rem; }
    #fuelTable tbody tr:hover { background:#f8fff8; }
    #fuelTable tbody tr:last-child td { border-bottom:none; }

    /* ── Badges tipo ── */
    .tipo-badge { display:inline-flex; align-items:center; gap:4px; border-radius:6px; padding:2px 8px; font-size:0.68rem; font-weight:700; white-space:nowrap; }
    .tipo-badge.entrada { background:#e6f4ea; color:#198754; border:1px solid #a3d9b1; }
    .tipo-badge.saida   { background:#fce8e6; color:#dc3545; border:1px solid #f8b8b8; }

    /* ── Action Buttons ── */
    .action-btn { width:28px; height:28px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:0.72rem; border:1px solid; transition:all 0.15s; text-decoration:none; cursor:pointer; background:transparent; }

    /* ── Form card ── */
    .form-label-sm { font-size:0.72rem; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; color:#6c757d; margin-bottom:4px; }
    .form-control, .form-select { border-radius:8px; border:1px solid #dee2e6; font-size:0.85rem; background:#f8f9fa; }
    .form-control:focus, .form-select:focus { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,0.1); background:#fff; }

    /* DataTables overrides */
    div.dataTables_wrapper div.dataTables_filter input { border-radius:8px; border:1px solid #dee2e6; padding:6px 12px; font-size:0.82rem; }
    div.dataTables_wrapper div.dataTables_length select { border-radius:8px; border:1px solid #dee2e6; padding:4px 8px; font-size:0.82rem; }
    .dt-buttons { display:flex; gap:6px; }
    .dt-button { border-radius:8px !important; font-size:0.78rem !important; font-weight:600 !important; padding:5px 14px !important; border:1px solid #dee2e6 !important; }

    /* ── Print ── */
    .print-header { display:none; }
    @media print {
        .no-print, .d-print-none { display:none !important; }
        .print-header { display:block !important; border-bottom:2px solid #000; margin-bottom:20px; }
        body * { visibility:hidden; }
        #printArea, #printArea * { visibility:visible; }
        #printArea { position:absolute; left:0; top:0; width:100%; padding:20px; }
        .table td, .table th { font-size:9pt !important; }
    }
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-fuel-pump-fill text-success me-2"></i>Gestão de Combustível</h4>
            <p class="text-muted small mb-0">Controlo de abastecimentos e stock de tanques</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-dark btn-sm fw-bold px-3" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Imprimir
            </button>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                <i class="bi bi-plus-lg me-1"></i> Atestar Tanque
            </button>
        </div>
    </div>

    {{-- KPI CARDS — stock por tanque --}}
    <div class="row g-3 mb-3 no-print">
        @foreach($tanques as $i => $tanque)
        @php
            $colors = ['green','blue','teal','gold','purple','red'];
            $color  = $colors[$i % count($colors)];
            $icons  = ['fuel-pump-fill','droplet-fill','water','archive-fill','database-fill','fuel-pump'];
            $icon   = $icons[$i % count($icons)];
        @endphp
        <div class="col-6 col-md-2">
            <div class="kpi-card {{ $color }}">
                <div class="kpi-label">{{ $tanque->nome }}</div>
                <div class="kpi-value" style="font-size:1.3rem;color:{{ $tanque->percentagem < 15 ? '#dc3545' : '#198754' }};">
                    {{ number_format($tanque->percentagem, 0) }}%
                </div>
                <div class="kpi-sub">{{ number_format($tanque->stock_atual, 0, ',', '.') }}L disponíveis</div>
                <i class="bi bi-{{ $icon }} kpi-icon"></i>
                {{-- mini barra --}}
                <div style="position:absolute;bottom:0;left:4px;right:0;height:3px;background:#f1f3f5;border-radius:0 0 14px 0;">
                    <div style="height:100%;width:{{ $tanque->percentagem }}%;background:{{ $tanque->percentagem < 15 ? '#dc3545' : '#198754' }};border-radius:inherit;transition:width 1s;"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- PAINEL PRINCIPAL: Form + Tanques lado a lado --}}
    <div class="row g-3 mb-3 no-print">

        {{-- Formulário de Abastecimento --}}
        <div class="col-lg-7">
            <div class="panel-card p-4 h-100">
                <div class="panel-title mb-3"><i class="bi bi-fuel-pump text-success"></i> Registar Abastecimento</div>
                <form action="{{ route('fuel.store') }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label-sm">Tanque de Origem</label>
                            <select name="tank_id" class="form-select" required>
                                <option value="" disabled selected>Escolha o tanque...</option>
                                @foreach($tanques as $tanque)
                                    <option value="{{ $tanque->id }}">{{ $tanque->nome }} ({{ number_format($tanque->stock_atual,0) }}L)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-sm">Data</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-sm">Viatura</label>
                            <input type="text" name="plate" class="form-control" placeholder="Matrícula" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Contador Inicial</label>
                            <input type="number" name="start_counter" id="ci" class="form-control" oninput="calc()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Contador Final</label>
                            <input type="number" name="end_counter" id="cf" class="form-control" oninput="calc()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm" style="color:#198754;">Total (L)</label>
                            <input type="number" id="qty" name="quantity" class="form-control fw-bold" style="border-color:#a3d9b1;color:#198754;background:#f0fdf4;" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Empresa</label>
                            <input type="text" name="company" list="empresas-list" class="form-control" placeholder="Selecione ou digite...">
                            <datalist id="empresas-list">
                                <option value="Tanque da Fem">
                                <option value="Bymoze">
                                <option value="Nitro">
                                <option value="Bomba Móvel">
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Operador</label>
                            <input type="text" name="operator" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sm">Motorista</label>
                            <input type="text" name="driver" class="form-control">
                        </div>
                        <div class="col-12 text-end pt-1">
                            <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm" style="border-radius:8px;">
                                <i class="bi bi-check-circle me-1"></i> Confirmar Saída
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Níveis de Stock --}}
        <div class="col-lg-5">
            <div class="panel-card p-3 h-100">
                <div class="panel-title mb-3"><i class="bi bi-bar-chart-fill" style="color:#0dcaf0;"></i> Níveis de Stock</div>
                <div style="overflow-y:auto;max-height:260px;">
                    @foreach($tanques as $tanque)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold" style="font-size:0.78rem;">{{ $tanque->nome }}</span>
                            <span style="font-size:0.72rem;font-weight:700;color:{{ $tanque->percentagem < 15 ? '#dc3545' : '#198754' }};">
                                {{ number_format($tanque->percentagem,1) }}%
                            </span>
                        </div>
                        <div class="tank-visor">
                            <div class="tank-level {{ $tanque->percentagem < 15 ? 'low' : '' }}" style="width:{{ $tanque->percentagem }}%;"></div>
                            <div class="tank-text">{{ number_format($tanque->stock_atual,0,',','.') }}L / {{ number_format($tanque->capacidade,0,',','.') }}L</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICO — largura total --}}
    <div class="panel-card p-4 mb-3 no-print">
        <div class="panel-title mb-3"><i class="bi bi-graph-up text-primary"></i> Consumo Diário por Empresa (L)</div>
        <div style="height:260px; position:relative;">
            <canvas id="consumptionChart"></canvas>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel no-print">
        <div class="panel-title mb-3"><i class="bi bi-funnel-fill"></i> Filtros</div>
        <form action="{{ route('fuel.index') }}" method="GET">
            <div class="filter-group">
                <div class="filter-item">
                    <label>Data Início</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}">
                </div>
                <div class="filter-item">
                    <label>Data Fim</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}">
                </div>
                <div class="filter-item">
                    <label>Tanque</label>
                    <select name="filter_tank_id">
                        <option value="">Todos</option>
                        @foreach($tanques as $t)
                            <option value="{{ $t->id }}" {{ request('filter_tank_id') == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn-filter btn-filter-apply"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="{{ route('fuel.index') }}" class="btn-filter btn-filter-clear"><i class="bi bi-x-lg"></i> Limpar</a>
                </div>
            </div>
        </form>

        {{-- Resumo do filtro activo --}}
        @if(request('from_date') || request('company') || request('filter_tank_id'))
        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
            <span style="font-size:0.72rem;color:#6c757d;font-weight:600;">RESUMO:</span>
            <span style="font-size:0.72rem;">{{ $contagemRegistos }} registos</span>
            <span class="tipo-badge saida"><i class="bi bi-arrow-down-circle-fill"></i> Saídas: {{ number_format($totalConsumidoFiltro, 0) }}L</span>
            <span class="tipo-badge entrada"><i class="bi bi-arrow-up-circle-fill"></i> Entradas: {{ number_format($totalEntradaFiltro, 0) }}L</span>
        </div>
        @endif
    </div>

    {{-- TABELA --}}
    <div class="table-card">
        <div class="p-3">
            <table id="fuelTable" class="table table-hover align-middle mb-0 w-100">
                <thead>
                    <tr>
                        <th>DATA</th>
                        <th>TIPO</th>
                        <th>TANQUE</th>
                        <th>IDENTIFICAÇÃO</th>
                        <th class="text-center">CONTADORES</th>
                        <th class="text-end">QUANTIDADE</th>
                        <th>OPERADOR / MOTORISTA</th>
                        <th class="text-center no-print">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historico as $item)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark" style="font-size:0.8rem;">{{ date('d/m/Y', strtotime($item->date)) }}</span>
                        </td>
                        <td>
                            <span class="tipo-badge {{ $item->tipo == 'ENTRADA' ? 'entrada' : 'saida' }}">
                                <i class="bi bi-{{ $item->tipo == 'ENTRADA' ? 'arrow-up-circle-fill' : 'arrow-down-circle-fill' }}"></i>
                                {{ $item->tipo }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold" style="font-size:0.8rem;">{{ $item->tanque_nome }}</span>
                        </td>
                        <td>
                            <span style="font-size:0.8rem;font-weight:600;">{{ $item->ident }}</span><br>
                            <span class="text-muted" style="font-size:0.7rem;">{{ $item->company }}</span>
                        </td>
                        <td class="text-center" style="font-size:0.78rem;">
                            @if($item->start_counter !== null)
                                <span class="badge bg-light text-dark border" style="font-size:0.68rem;font-weight:600;">
                                    {{ number_format($item->start_counter,0) }} → {{ number_format($item->end_counter,0) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="fw-bold" style="font-size:0.85rem;color:{{ $item->tipo == 'ENTRADA' ? '#198754' : '#dc3545' }};">
                                {{ $item->tipo == 'ENTRADA' ? '+' : '-' }}{{ number_format($item->quantity,0) }}L
                            </span>
                        </td>
                        <td>
                            <span style="font-size:0.75rem;">
                                @if($item->operator)<i class="bi bi-person-fill text-muted me-1"></i>{{ $item->operator }}<br>@endif
                                @if($item->driver)<i class="bi bi-truck text-muted me-1"></i>{{ $item->driver }}@endif
                            </span>
                        </td>
                        <td class="text-center no-print">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button"
                                        class="action-btn text-primary border-primary border-opacity-25 btn-edit"
                                        data-id="{{ $item->id }}"
                                        data-tipo="{{ $item->tipo }}"
                                        title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button"
                                        class="action-btn text-danger border-danger border-opacity-25 btn-delete"
                                        data-id="{{ $item->id }}"
                                        data-tipo="{{ $item->tipo }}"
                                        data-url="{{ $item->tipo == 'ENTRADA' ? route('fuel.entry.destroy', $item->id) : route('fuel.log.destroy', $item->id) }}"
                                        title="Eliminar">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Área de impressão --}}
    <div id="printArea" style="display:none;">
        <div style="font-family:Arial,sans-serif;padding:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #198754;padding-bottom:16px;margin-bottom:24px;">
                <div>
                    <h2 style="margin:0;color:#198754;">RELATÓRIO DE COMBUSTÍVEL</h2>
                    <p style="margin:4px 0 0;color:#666;font-size:0.85rem;">Controlo de Abastecimentos e Stock</p>
                </div>
                <div style="text-align:right;color:#666;font-size:0.8rem;">
                    <div><strong>Emitido:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                    <div><strong>Por:</strong> {{ auth()->user()->name }}</div>
                </div>
            </div>
            <table style="width:100%;border-collapse:collapse;font-size:0.8rem;">
                <thead>
                    <tr style="background:#198754;color:white;">
                        <th style="padding:8px 10px;">Data</th>
                        <th style="padding:8px 10px;">Tipo</th>
                        <th style="padding:8px 10px;">Tanque</th>
                        <th style="padding:8px 10px;">Identificação</th>
                        <th style="padding:8px 10px;text-align:center;">Contadores</th>
                        <th style="padding:8px 10px;text-align:right;">Qtd</th>
                        <th style="padding:8px 10px;">Operador</th>
                        <th style="padding:8px 10px;">Motorista</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historico as $item)
                    <tr style="border-bottom:1px solid #eee;{{ $loop->even ? 'background:#f9f9f9;' : '' }}">
                        <td style="padding:6px 10px;">{{ date('d/m/Y', strtotime($item->date)) }}</td>
                        <td style="padding:6px 10px;font-weight:bold;color:{{ $item->tipo == 'ENTRADA' ? '#198754' : '#dc3545' }};">{{ $item->tipo }}</td>
                        <td style="padding:6px 10px;">{{ $item->tanque_nome }}</td>
                        <td style="padding:6px 10px;">{{ $item->ident }}<br><small style="color:#666;">{{ $item->company }}</small></td>
                        <td style="padding:6px 10px;text-align:center;">{{ $item->start_counter !== null ? number_format($item->start_counter,0).' → '.number_format($item->end_counter,0) : '—' }}</td>
                        <td style="padding:6px 10px;text-align:right;font-weight:bold;color:{{ $item->tipo == 'ENTRADA' ? '#198754' : '#dc3545' }};">{{ $item->tipo == 'ENTRADA' ? '+' : '-' }}{{ number_format($item->quantity,0) }}L</td>
                        <td style="padding:6px 10px;">{{ $item->operator ?: '—' }}</td>
                        <td style="padding:6px 10px;">{{ $item->driver ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:40px;display:flex;justify-content:space-around;">
                <div style="text-align:center;"><div style="border-top:1px solid #000;width:180px;padding-top:6px;font-size:0.75rem;font-weight:bold;">Operador / Responsável</div></div>
                <div style="text-align:center;"><div style="border-top:1px solid #000;width:180px;padding-top:6px;font-size:0.75rem;font-weight:bold;">Visto / Administração</div></div>
            </div>
            <p style="margin-top:30px;text-align:center;color:#999;font-size:0.7rem;">Documento gerado automaticamente · {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

</div>

{{-- MODAL: Atestar Tanque --}}
<div class="modal fade" id="modalEntrada" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-success text-white border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i>Nova Entrada de Cisterna</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('fuel.storeEntry') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label-sm">Tanque de Destino</label>
                        <select name="tank_id" class="form-select" required>
                            @foreach($tanques as $tanque)
                                <option value="{{ $tanque->id }}">{{ $tanque->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label-sm">Data</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-sm">Litros Recebidos</label>
                            <input type="number" name="quantity" class="form-control" placeholder="Ex: 5000" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label-sm">Fornecedor / Guia</label>
                        <input type="text" name="supplier" class="form-control" placeholder="Ex: Petromoc / Guia 001">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light btn-sm px-4" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold px-4" style="border-radius:8px;">
                        <i class="bi bi-check-circle me-1"></i> Confirmar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Editar Abastecimento --}}
<div class="modal fade" id="editLogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0" style="background:#1a56db;">
                <h6 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2"></i>Editar Abastecimento</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLogForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_log_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label-sm">Viatura / Placa</label>
                        <input type="text" name="plate" id="edit_plate" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label-sm">Contador Inicial</label>
                            <input type="number" name="start_counter" id="edit_start" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-sm">Contador Final</label>
                            <input type="number" name="end_counter" id="edit_end" class="form-control" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label-sm">Empresa</label>
                        <input type="text" name="company" id="edit_company" list="empresas-list" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light btn-sm px-4" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:8px;">
                        <i class="bi bi-check-circle me-1"></i> Guardar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function () {

    // DataTable
    $('#fuelTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                className: 'btn btn-sm',
                title: 'Relatorio_Combustivel_{{ date("Ymd") }}'
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                className: 'btn btn-sm',
                orientation: 'landscape',
                pageSize: 'A4'
            }
        ],
        order: [[0, 'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json' },
        pageLength: 20,
        columnDefs: [{ orderable: false, targets: -1 }]
    });

    // Editar
    $(document).on('click', '.btn-edit', function () {
        const id   = $(this).data('id');
        const tipo = $(this).data('tipo');

        if (tipo === 'SAÍDA') {
            $.get(`/fuel-log/${id}/json`, function (data) {
                $('#edit_log_id').val(data.id);
                $('#edit_plate').val(data.plate);
                $('#edit_start').val(data.start_counter);
                $('#edit_end').val(data.end_counter);
                $('#edit_company').val(data.company);
                new bootstrap.Modal(document.getElementById('editLogModal')).show();
            }).fail(() => alert('Erro ao buscar dados.'));
        } else {
            $.get(`/fuel-entry/${id}/json`, function (data) {
                $('#modalEntrada form').attr('action', `/fuel-entry/${id}`);
                if (!$('#modalEntrada input[name="_method"]').length)
                    $('#modalEntrada form').append('<input type="hidden" name="_method" value="PUT">');
                $('#modalEntrada select[name="tank_id"]').val(data.tank_id);
                $('#modalEntrada input[name="date"]').val(data.date.split(' ')[0]);
                $('#modalEntrada input[name="quantity"]').val(data.quantity);
                $('#modalEntrada input[name="supplier"]').val(data.supplier || data.ident);
                new bootstrap.Modal(document.getElementById('modalEntrada')).show();
            }).fail(() => alert('Erro ao buscar dados da entrada.'));
        }
    });

    // Eliminar
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const btn = $(this), url = btn.data('url'), row = btn.closest('tr');
        if (!confirm('Eliminar este registo?')) return;
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span>');
        $.ajax({
            url, type: 'POST',
            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
            success: () => row.fadeOut(300, function () { $(this).remove(); }),
            error: (xhr) => {
                if (xhr.status === 200) { row.fadeOut(300, function () { $(this).remove(); }); }
                else { alert('Erro ao eliminar.'); btn.prop('disabled', false).html('<i class="bi bi-trash3"></i>'); }
            }
        });
    });
});

// Cálculo contadores
function calc() {
    const i = parseFloat(document.getElementById('ci').value) || 0;
    const f = parseFloat(document.getElementById('cf').value) || 0;
    const res = f - i;
    document.getElementById('qty').value = res > 0 ? res : 0;
}

// Impressão
function imprimirRelatorio() {
    document.getElementById('printArea').style.display = 'block';
    window.print();
    document.getElementById('printArea').style.display = 'none';
}

// Gráfico
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('consumptionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labelsCompostas ?? []) !!},
            datasets: {!! json_encode($datasets ?? []) !!}
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { font: { size: 10 }, boxWidth: 10 } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { stacked: true, ticks: { font: { size: 9 }, maxRotation: 45 } },
                y: { stacked: true, beginAtZero: true, ticks: { font: { size: 10 } } }
            }
        }
    });
});
</script>

</x-app-layout>