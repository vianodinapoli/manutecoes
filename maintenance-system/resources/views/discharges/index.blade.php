<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .filter-panel { background:#fff; border-radius:14px; border:1px solid #e9ecef; padding:20px 24px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    .filter-panel .filter-title { font-size:0.72rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#adb5bd; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
    .filter-group { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; }
    .filter-item { display:flex; flex-direction:column; gap:5px; }
    .filter-item label { font-size:0.7rem; font-weight:600; letter-spacing:0.8px; text-transform:uppercase; color:#6c757d; }
    .filter-item select, .filter-item input { border:1px solid #dee2e6; border-radius:8px; padding:7px 12px; font-size:0.82rem; color:#343a40; background:#f8f9fa; outline:none; transition:border-color 0.2s,box-shadow 0.2s; min-width:130px; }
    .filter-item select:focus, .filter-item input:focus { border-color:#0d6efd; box-shadow:0 0 0 3px rgba(13,110,253,0.1); background:#fff; }
    .btn-filter { padding:8px 18px; border-radius:8px; font-size:0.8rem; font-weight:600; cursor:pointer; border:none; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s; }
    .btn-filter-apply { background:#0d6efd; color:#fff; }
    .btn-filter-apply:hover { background:#0b5ed7; }
    .btn-filter-clear { background:#f1f3f5; color:#495057; border:1px solid #dee2e6; }
    .btn-filter-clear:hover { background:#e9ecef; }
    .active-filters { display:flex; flex-wrap:wrap; gap:6px; margin-top:12px; }
    .filter-tag { display:inline-flex; align-items:center; gap:5px; background:#e7f1ff; color:#0d6efd; border:1px solid #b6d0ff; border-radius:20px; padding:3px 10px; font-size:0.72rem; font-weight:500; }
    .filter-tag .remove-tag { cursor:pointer; opacity:0.6; font-size:0.8rem; }
    .filter-tag .remove-tag:hover { opacity:1; }

    /* Tabela */
    .table-card { background:#fff; border-radius:14px; border:1px solid #e9ecef; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    #dischargesTable thead tr { background:#f8f9fa; }
    #dischargesTable thead th { font-size:0.67rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#868e96; border-bottom:1px solid #e9ecef; padding:8px 12px; white-space:nowrap; }
    #dischargesTable tbody td { padding:7px 12px; vertical-align:middle; border-bottom:1px solid #f1f3f5; font-size:0.82rem; }
    #dischargesTable tbody tr:hover { background:#f8f9ff; }
    #dischargesTable tbody tr:last-child td { border-bottom:none; }

    /* ── Sacos — compacto ── */
    .sacos-wrap { display:flex; flex-direction:column; gap:3px; }

    /* pills: ALTA 12 / BAIXA 8 */
    .saco-pill {
        display:inline-flex; align-items:center; gap:4px;
        border-radius:5px; padding:2px 7px;
        font-size:0.7rem; font-weight:700;
        width:fit-content; line-height:1.4;
    }
    .saco-pill.alta  { background:#e8f0fe; color:#1a56db; border:1px solid #b8d4f8; }
    .saco-pill.baixa { background:#fde8e8; color:#dc3545; border:1px solid #f8b8b8; }
    .saco-pill .pill-ref { font-size:0.58rem; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; opacity:0.65; }
    .saco-pill .pill-n   { font-size:0.8rem; font-weight:800; }

    /* total + divergência */
    .sacos-footer { display:flex; align-items:center; gap:5px; margin-top:1px; }
    .sacos-total-badge {
        font-size:0.62rem; font-weight:600; color:#6c757d;
        background:#f1f3f5; border-radius:4px; padding:1px 6px;
    }
    .divergence-badge { display:inline-flex; align-items:center; gap:3px; background:#fff3cd; color:#856404; border:1px solid #ffc107; border-radius:4px; padding:1px 6px; font-size:0.6rem; font-weight:600; }

    /* KPI Cards */
    .kpi-card { background:#fff; border-radius:14px; border:1px solid #e9ecef; padding:16px 20px; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:transform 0.15s,box-shadow 0.15s; height:100%; position:relative; overflow:hidden; }
    .kpi-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,0.08); }
    .kpi-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:14px 0 0 14px; }
    .kpi-card.teal::before   { background:#0dcaf0; }
    .kpi-card.green::before  { background:#198754; }
    .kpi-card.blue::before   { background:#1a56db; }
    .kpi-card.red::before    { background:#dc3545; }
    .kpi-card.purple::before { background:#6f42c1; }
    .kpi-card.gold::before   { background:#fd7e14; }
    .kpi-label { font-size:0.65rem; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:#adb5bd; margin-bottom:6px; }
    .kpi-value { font-size:1.7rem; font-weight:800; line-height:1; margin-bottom:2px; }
    .kpi-sub   { font-size:0.72rem; color:#adb5bd; }
    .kpi-icon  { position:absolute; right:16px; top:50%; transform:translateY(-50%); font-size:2rem; opacity:0.1; }

    .status-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:0.68rem; font-weight:600; white-space:nowrap; }
    .action-btn { width:28px; height:28px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:0.72rem; border:1px solid; transition:all 0.15s; text-decoration:none; cursor:pointer; background:transparent; }

    div.dataTables_wrapper div.dataTables_filter input { border-radius:8px; border:1px solid #dee2e6; padding:6px 12px; font-size:0.82rem; }
    div.dataTables_wrapper div.dataTables_length select { border-radius:8px; border:1px solid #dee2e6; padding:4px 8px; font-size:0.82rem; }

    @media print {
        .no-print { display:none !important; }
        body * { visibility:hidden; }
        #reportArea, #reportArea * { visibility:visible; }
        #reportArea { position:absolute; left:0; top:0; width:100%; padding:30px; }
    }
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-ship text-primary me-2"></i>Controlo de Descargas</h4>
            <p class="text-muted small mb-0">Nitrato de Amónio — Gestão e rastreio de camiões</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="imprimirRelatorio()" class="btn btn-outline-dark btn-sm fw-bold px-3">
                <i class="bi bi-printer me-1"></i> Imprimir Relatório
            </button>
            <a href="{{ route('discharges.create') }}" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Registar Descarga
            </a>
        </div>
    </div>

    {{-- TOAST --}}
    @if(session('success'))
    <div class="position-fixed top-0 end-0 p-3 no-print" style="z-index:9999;">
        <div id="successToast" class="toast show border-0"
             style="border-radius:10px;min-width:260px;overflow:hidden;
                    background:linear-gradient(135deg,rgba(25,135,84,0.92),rgba(32,201,151,0.92));
                    backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.25)!important;
                    box-shadow:0 8px 32px rgba(25,135,84,0.3);">
            <div class="d-flex align-items-center gap-3 px-3 py-3">
                <i class="bi bi-check-circle-fill text-white" style="font-size:1.4rem;"></i>
                <div style="font-size:0.78rem;color:rgba(255,255,255,0.95);line-height:1.4;">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white opacity-75 ms-auto" data-bs-dismiss="toast" style="font-size:0.55rem;"></button>
            </div>
            <div style="height:2px;background:rgba(255,255,255,0.15);overflow:hidden;">
                <div id="toastProgress" style="height:100%;width:100%;background:rgba(255,255,255,0.6);transition:width 3.5s linear;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-3 no-print">
        <div class="col-6 col-md-2">
            <div class="kpi-card teal">
                <div class="kpi-label">Em Trânsito</div>
                <div class="kpi-value" style="color:#0dcaf0;">{{ $discharges->where('status','in_transit')->count() }}</div>
                <div class="kpi-sub">camiões activos</div>
                <i class="bi bi-truck kpi-icon" style="color:#0dcaf0;"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card green">
                <div class="kpi-label">Confirmados</div>
                <div class="kpi-value" style="color:#198754;">{{ $discharges->where('status','confirmed')->count() }}</div>
                <div class="kpi-sub">na balança</div>
                <i class="bi bi-check-circle-fill kpi-icon" style="color:#198754;"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card purple">
                <div class="kpi-label">Total Registos</div>
                <div class="kpi-value" style="color:#6f42c1;">{{ $discharges->count() }}</div>
                <div class="kpi-sub">nesta listagem</div>
                <i class="bi bi-clipboard-data kpi-icon" style="color:#6f42c1;"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card blue">
                <div class="kpi-label">Total Alta</div>
                <div class="kpi-value" style="color:#1a56db;">{{ number_format($discharges->sum('sacos_alta'), 0, ',', '.') }}</div>
                <div class="kpi-sub">sacos × 50 kg</div>
                <i class="bi bi-arrow-up-circle-fill kpi-icon" style="color:#1a56db;"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card red">
                <div class="kpi-label">Total Baixa</div>
                <div class="kpi-value" style="color:#dc3545;">{{ number_format($discharges->sum('sacos_baixa'), 0, ',', '.') }}</div>
                <div class="kpi-sub">sacos × 25 kg</div>
                <i class="bi bi-arrow-down-circle-fill kpi-icon" style="color:#dc3545;"></i>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="kpi-card gold">
                <div class="kpi-label">Peso Líquido</div>
                <div class="kpi-value" style="color:#fd7e14;font-size:1.1rem;padding-top:4px;">
                    {{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido') / 1000, 1, ',', '.') }}t
                </div>
                <div class="kpi-sub">total confirmado</div>
                <i class="bi bi-speedometer2 kpi-icon" style="color:#fd7e14;"></i>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel no-print">
        <div class="filter-title"><i class="bi bi-funnel-fill"></i> Filtros de Pesquisa</div>
        <div class="filter-group">
            <div class="filter-item">
                <label>Data Início</label>
                <input type="date" id="filterDateFrom">
            </div>
            <div class="filter-item">
                <label>Data Fim</label>
                <input type="date" id="filterDateTo">
            </div>
            <div class="filter-item">
                <label>Transportadora</label>
                <select id="filterTransportadora">
                    <option value="">Todas</option>
                    @foreach($discharges->pluck('transportadora')->unique()->sort() as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Estado</label>
                <select id="filterStatus">
                    <option value="">Todos</option>
                    <option value="in_transit">Em Trânsito</option>
                    <option value="confirmed">Confirmado</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Tipo Carga</label>
                <select id="filterTipoCarga">
                    <option value="">Todos</option>
                    <option value="alta">Só Alta</option>
                    <option value="baixa">Só Baixa</option>
                    <option value="misto">Misto</option>
                </select>
            </div>
            <div class="filter-item">
                <label>Divergência</label>
                <select id="filterDivergencia">
                    <option value="">Todos</option>
                    <option value="sim">Com divergência</option>
                    <option value="nao">Sem divergência</option>
                </select>
            </div>
            <div class="d-flex gap-2 align-items-end">
                <button class="btn-filter btn-filter-apply" onclick="applyFilters()"><i class="bi bi-search"></i> Filtrar</button>
                <button class="btn-filter btn-filter-clear" onclick="clearFilters()"><i class="bi bi-x-lg"></i> Limpar</button>
            </div>
        </div>
        <div class="active-filters" id="activeTags"></div>
    </div>

    {{-- TABELA --}}
    <div class="table-card no-print">
        <div class="p-3">
            <table class="table table-hover align-middle mb-0" id="dischargesTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Nº / DATA</th>
                        <th>GUIA</th>
                        <th>MOTORISTA / MATRÍCULA</th>
                        <th>TRANSPORTADORA</th>
                        <th>SACOS</th>
                        <th class="text-center">PESO</th>
                        <th class="text-center">TEMPO</th>
                        <th class="text-center">ESTADO</th>
                        <th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discharges as $discharge)
                    @php
                        $statusClass = match($discharge->status) {
                            'confirmed'  => 'bg-success bg-opacity-10 text-success',
                            'in_transit' => 'bg-info bg-opacity-10 text-info',
                            default      => 'bg-warning bg-opacity-10 text-warning',
                        };
                        $statusIcon = match($discharge->status) {
                            'confirmed'  => 'check-circle-fill',
                            'in_transit' => 'truck',
                            default      => 'hourglass-split',
                        };
                        $sacosAlta     = $discharge->sacos_alta ?? 0;
                        $sacosBaixa    = $discharge->sacos_baixa ?? 0;
                        $totalSacos    = $sacosAlta + $sacosBaixa;
                        $hasDivergence = $discharge->sacos_confirmados
                            && $discharge->sacos_confirmados != $discharge->numero_sacos;
                    @endphp
                    <tr
                        data-status="{{ $discharge->status }}"
                        data-transportadora="{{ strtolower($discharge->transportadora) }}"
                        data-data="{{ $discharge->data->format('Y-m-d') }}"
                        data-sacos-alta="{{ $sacosAlta }}"
                        data-sacos-baixa="{{ $sacosBaixa }}"
                        data-divergencia="{{ $hasDivergence ? 'sim' : 'nao' }}"
                    >
                        <td>
                            <span class="fw-bold text-dark" style="font-size:0.8rem;">#{{ $discharge->id }}</span><br>
                            <span class="text-muted" style="font-size:0.72rem;">{{ $discharge->data->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-primary" style="font-size:0.78rem;">{{ $discharge->numero_guia }}</span>
                        </td>
                        <td>
                            <span style="font-size:0.78rem;font-weight:600;">{{ $discharge->motorista }}</span><br>
                            <span class="badge bg-dark bg-opacity-75" style="font-size:0.6rem;letter-spacing:1px;margin-top:2px;">{{ $discharge->matricula }}</span>
                        </td>
                        <td>
                            <span class="text-secondary" style="font-size:0.78rem;">{{ $discharge->transportadora }}</span>
                        </td>

                        {{-- ── SACOS: pills compactas + total + divergência ── --}}
                        <td>
                            <div class="sacos-wrap">
                                {{-- Pills alta e baixa --}}
                                @if($sacosAlta > 0)
                                <span class="saco-pill alta">
                                    <span class="pill-ref">Alta</span>
                                    <span class="pill-n">{{ $sacosAlta }}</span>
                                </span>
                                @endif
                                @if($sacosBaixa > 0)
                                <span class="saco-pill baixa">
                                    <span class="pill-ref">Baixa</span>
                                    <span class="pill-n">{{ $sacosBaixa }}</span>
                                </span>
                                @endif

                                {{-- Total + divergência na mesma linha --}}
                                @if($totalSacos > 0)
                                <div class="sacos-footer">
                                    <span class="sacos-total-badge">Total {{ $totalSacos }}</span>
                                    @if($hasDivergence)
                                    <span class="divergence-badge">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        Conf. {{ $discharge->sacos_confirmados }}
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="fw-bold" style="font-size:0.78rem;">{{ number_format($discharge->peso_total_porto, 0, ',', '.') }} kg</div>
                            @if($discharge->peso_liquido)
                                <div class="text-success" style="font-size:0.68rem;">Líq: {{ number_format($discharge->peso_liquido, 0, ',', '.') }} kg</div>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($discharge->tempo_transporte)
                                <span class="badge bg-opacity-15 text-dark fw-semibold" style="font-size:0.68rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $discharge->tempo_transporte }}min
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.78rem;">—</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <span class="status-badge {{ $statusClass }}">
                                <i class="bi bi-{{ $statusIcon }}"></i> {{ $discharge->status_label }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                @if($discharge->status === 'pending' || $discharge->status === 'in_transit')
                                    <button class="btn btn-success btn-sm fw-bold"
                                            style="font-size:0.65rem;border-radius:20px;padding:2px 9px;"
                                            data-bs-toggle="modal" data-bs-target="#modalConfirm"
                                            data-id="{{ $discharge->id }}"
                                            data-guia="{{ $discharge->numero_guia }}"
                                            data-sacos="{{ $discharge->numero_sacos }}"
                                            data-saida="{{ $discharge->hora_saida_porto }}">
                                        <i class="bi bi-speedometer2 me-1"></i>Balança
                                    </button>
                                @endif
                                <a href="{{ route('discharges.show', $discharge->id) }}" class="action-btn text-primary border-primary border-opacity-25" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('discharges.edit', $discharge->id) }}" class="action-btn text-warning border-warning border-opacity-25" title="Editar"><i class="bi bi-pencil"></i></a>
                                @if(auth()->user()->hasRole('super-admin'))
                                <form action="{{ route('discharges.destroy', $discharge->id) }}" method="POST" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn text-danger border-danger border-opacity-25"
                                            onclick="return confirm('Eliminar este registo?')" title="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
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

{{-- MODAL BALANÇA --}}
<div class="modal fade" id="modalConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-speedometer2 me-2"></i>Confirmação na Balança</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formConfirm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 small mb-3 border-0" style="border-radius:10px;background:#e8f4fd;color:#1a56db;">
                        <i class="bi bi-info-circle me-1"></i>
                        Guia <strong id="confirm_guia"></strong> —
                        <strong id="confirm_sacos"></strong> sacos registados no porto
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Hora de Chegada</label>
                            <input type="time" name="hora_chegada_balanca" id="hora_chegada_balanca" class="form-control" style="border-radius:8px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Tempo de Transporte</label>
                            <div class="input-group">
                                <input type="text" id="transport_time_calc" class="form-control bg-light" style="border-radius:8px 0 0 8px;" readonly placeholder="Auto">
                                <span class="input-group-text small">min</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Peso Bruto (kg)</label>
                            <input type="number" name="peso_bruto" id="peso_bruto" class="form-control" style="border-radius:8px;" step="0.01" min="0" oninput="calcNetWeight()" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Tara (kg)</label>
                            <input type="number" name="tara" id="tara" class="form-control" style="border-radius:8px;" step="0.01" min="0" oninput="calcNetWeight()" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Peso Líquido (kg)</label>
                            <input type="text" id="net_weight_calc" class="form-control bg-light fw-bold text-success" style="border-radius:8px;" readonly placeholder="Calculado automaticamente">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Sacos Confirmados</label>
                            <input type="number" name="sacos_confirmados" id="sacos_confirmados" class="form-control" style="border-radius:8px;" min="1" required>
                            <div id="divergence_alert" class="text-danger small mt-1 d-none">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Divergência detectada!
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Observações</label>
                            <textarea name="observacoes" class="form-control" style="border-radius:8px;" rows="2" placeholder="Ex: saco rasgado, diferença de peso..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-4" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="border-radius:8px;">
                        <i class="bi bi-check-circle me-1"></i>Confirmar e Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ÁREA IMPRESSÃO --}}
<div id="reportArea" style="display:none;">
    <div style="font-family:Arial,sans-serif;padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid #198754;padding-bottom:16px;margin-bottom:24px;">
            <div>
                <h2 style="margin:0;color:#198754;">RELATÓRIO DE DESCARGAS</h2>
                <p style="margin:4px 0 0;color:#666;font-size:0.85rem;">Nitrato de Amónio — Controlo de Camiões</p>
            </div>
            <div style="text-align:right;color:#666;font-size:0.8rem;">
                <div><strong>Emitido:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                <div><strong>Total:</strong> {{ $discharges->count() }} registos</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
            <div style="background:#f0fdf4;border-left:4px solid #198754;border-radius:8px;padding:12px;">
                <div style="font-size:0.65rem;color:#666;text-transform:uppercase;font-weight:bold;">Total Registos</div>
                <div style="font-size:1.5rem;font-weight:bold;color:#198754;">{{ $discharges->count() }}</div>
            </div>
            <div style="background:#e8f0fe;border-left:4px solid #1a56db;border-radius:8px;padding:12px;">
                <div style="font-size:0.65rem;color:#666;text-transform:uppercase;font-weight:bold;">Total Sacos Alta</div>
                <div style="font-size:1.5rem;font-weight:bold;color:#1a56db;">{{ number_format($discharges->sum('sacos_alta'),0,',','.') }}</div>
                <div style="font-size:0.7rem;color:#666;">{{ number_format($discharges->sum('sacos_alta') * 50,0,',','.') }} kg</div>
            </div>
            <div style="background:#fde8e8;border-left:4px solid #dc3545;border-radius:8px;padding:12px;">
                <div style="font-size:0.65rem;color:#666;text-transform:uppercase;font-weight:bold;">Total Sacos Baixa</div>
                <div style="font-size:1.5rem;font-weight:bold;color:#dc3545;">{{ number_format($discharges->sum('sacos_baixa'),0,',','.') }}</div>
                <div style="font-size:0.7rem;color:#666;">{{ number_format($discharges->sum('sacos_baixa') * 25,0,',','.') }} kg</div>
            </div>
            <div style="background:#fff7e6;border-left:4px solid #fd7e14;border-radius:8px;padding:12px;">
                <div style="font-size:0.65rem;color:#666;text-transform:uppercase;font-weight:bold;">Peso Líquido Total</div>
                <div style="font-size:1.1rem;font-weight:bold;color:#fd7e14;">{{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido'),0,',','.') }} kg</div>
            </div>
        </div>
        <h3 style="font-size:0.9rem;color:#333;border-bottom:2px solid #eee;padding-bottom:8px;margin-bottom:12px;">RESUMO POR TRANSPORTADORA</h3>
        <table style="width:100%;border-collapse:collapse;margin-bottom:24px;font-size:0.8rem;">
            <thead>
                <tr>
                    <th style="padding:9px 12px;text-align:left;background:#198754;color:white;">Transportadora</th>
                    <th style="padding:9px 12px;text-align:center;background:#198754;color:white;">Viagens</th>
                    <th style="padding:9px 12px;text-align:center;background:#1a56db;color:white;">▲ Sacos Alta</th>
                    <th style="padding:9px 12px;text-align:center;background:#1a56db;color:white;">Peso Alta</th>
                    <th style="padding:9px 12px;text-align:center;background:#dc3545;color:white;">▼ Sacos Baixa</th>
                    <th style="padding:9px 12px;text-align:center;background:#dc3545;color:white;">Peso Baixa</th>
                    <th style="padding:9px 12px;text-align:center;background:#198754;color:white;">Total</th>
                    <th style="padding:9px 12px;text-align:right;background:#198754;color:white;">Peso Líquido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discharges->groupBy('transportadora') as $transportadora => $grupo)
                @php
                    $gAlta=$grupo->sum('sacos_alta'); $gBaixa=$grupo->sum('sacos_baixa'); $gTotal=$gAlta+$gBaixa;
                @endphp
                <tr style="border-bottom:1px solid #eee;{{ $loop->even ? 'background:#f9f9f9;' : '' }}">
                    <td style="padding:8px 12px;font-weight:bold;">{{ $transportadora }}</td>
                    <td style="padding:8px 12px;text-align:center;">{{ $grupo->count() }}</td>
                    <td style="padding:8px 12px;text-align:center;"><span style="background:#e8f0fe;color:#1a56db;border:1px solid #b8d4f8;border-radius:20px;padding:3px 10px;font-weight:700;">▲ {{ number_format($gAlta,0,',','.') }}</span></td>
                    <td style="padding:8px 12px;text-align:center;color:#1a56db;font-size:0.75rem;">{{ number_format($gAlta*50,0,',','.') }} kg</td>
                    <td style="padding:8px 12px;text-align:center;"><span style="background:#fde8e8;color:#dc3545;border:1px solid #f8b8b8;border-radius:20px;padding:3px 10px;font-weight:700;">▼ {{ number_format($gBaixa,0,',','.') }}</span></td>
                    <td style="padding:8px 12px;text-align:center;color:#dc3545;font-size:0.75rem;">{{ number_format($gBaixa*25,0,',','.') }} kg</td>
                    <td style="padding:8px 12px;text-align:center;font-weight:bold;">{{ number_format($gTotal,0,',','.') }}</td>
                    <td style="padding:8px 12px;text-align:right;">{{ number_format($grupo->whereNotNull('peso_liquido')->sum('peso_liquido'),0,',','.') }} kg</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php $totA=$discharges->sum('sacos_alta'); $totB=$discharges->sum('sacos_baixa'); @endphp
                <tr style="background:#f0fdf4;font-weight:bold;border-top:2px solid #198754;">
                    <td style="padding:8px 12px;">TOTAL GERAL</td>
                    <td style="padding:8px 12px;text-align:center;">{{ $discharges->count() }}</td>
                    <td style="padding:8px 12px;text-align:center;"><span style="background:#1a56db;color:#fff;border-radius:20px;padding:3px 10px;">▲ {{ number_format($totA,0,',','.') }}</span></td>
                    <td style="padding:8px 12px;text-align:center;color:#1a56db;">{{ number_format($totA*50,0,',','.') }} kg</td>
                    <td style="padding:8px 12px;text-align:center;"><span style="background:#dc3545;color:#fff;border-radius:20px;padding:3px 10px;">▼ {{ number_format($totB,0,',','.') }}</span></td>
                    <td style="padding:8px 12px;text-align:center;color:#dc3545;">{{ number_format($totB*25,0,',','.') }} kg</td>
                    <td style="padding:8px 12px;text-align:center;">{{ number_format($totA+$totB,0,',','.') }}</td>
                    <td style="padding:8px 12px;text-align:right;">{{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido'),0,',','.') }} kg</td>
                </tr>
            </tfoot>
        </table>
        <h3 style="font-size:0.9rem;color:#333;border-bottom:2px solid #eee;padding-bottom:8px;margin-bottom:12px;">DETALHE DOS REGISTOS</h3>
        <table style="width:100%;border-collapse:collapse;font-size:0.75rem;">
            <thead>
                <tr style="background:#1a56db;color:white;">
                    <th style="padding:7px 10px;">Nº</th>
                    <th style="padding:7px 10px;">Data</th>
                    <th style="padding:7px 10px;">Guia</th>
                    <th style="padding:7px 10px;">Motorista</th>
                    <th style="padding:7px 10px;">Transportadora</th>
                    <th style="padding:7px 10px;text-align:center;background:#1565c0;">▲ Alta</th>
                    <th style="padding:7px 10px;text-align:center;background:#1565c0;">Peso Alta</th>
                    <th style="padding:7px 10px;text-align:center;background:#b71c1c;">▼ Baixa</th>
                    <th style="padding:7px 10px;text-align:center;background:#b71c1c;">Peso Baixa</th>
                    <th style="padding:7px 10px;text-align:center;">Total</th>
                    <th style="padding:7px 10px;text-align:right;">Peso Líq.</th>
                    <th style="padding:7px 10px;text-align:center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discharges as $d)
                @php $dA=$d->sacos_alta??0; $dB=$d->sacos_baixa??0; @endphp
                <tr style="border-bottom:1px solid #eee;{{ $loop->even ? 'background:#f9f9f9;' : '' }}">
                    <td style="padding:6px 10px;color:#666;">#{{ $d->id }}</td>
                    <td style="padding:6px 10px;">{{ $d->data->format('d/m/Y') }}</td>
                    <td style="padding:6px 10px;font-weight:bold;color:#1a56db;">{{ $d->numero_guia }}</td>
                    <td style="padding:6px 10px;">{{ $d->motorista }}</td>
                    <td style="padding:6px 10px;">{{ $d->transportadora }}</td>
                    <td style="padding:6px 10px;text-align:center;color:#1a56db;font-weight:bold;">{{ $dA }}</td>
                    <td style="padding:6px 10px;text-align:center;color:#1a56db;font-size:0.7rem;">{{ number_format($dA*50,0,',','.') }} kg</td>
                    <td style="padding:6px 10px;text-align:center;color:#dc3545;font-weight:bold;">{{ $dB }}</td>
                    <td style="padding:6px 10px;text-align:center;color:#dc3545;font-size:0.7rem;">{{ number_format($dB*25,0,',','.') }} kg</td>
                    <td style="padding:6px 10px;text-align:center;font-weight:bold;">{{ $dA+$dB }}</td>
                    <td style="padding:6px 10px;text-align:right;">{{ $d->peso_liquido ? number_format($d->peso_liquido,0,',','.').' kg' : '---' }}</td>
                    <td style="padding:6px 10px;text-align:center;">{{ $d->status_label }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:30px;padding-top:12px;border-top:1px solid #eee;text-align:center;color:#999;font-size:0.7rem;">
            Documento gerado automaticamente · {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dtTable;
$(document).ready(function () {
    dtTable = $('#dischargesTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [7, 8] }],
        pageLength: 15,
    });

    @if(session('success'))
    const toastEl = document.getElementById('successToast');
    new bootstrap.Toast(toastEl, { delay: 3500 }).show();
    setTimeout(() => { document.getElementById('toastProgress').style.width = '0%'; }, 50);
    toastEl.style.opacity = '0'; toastEl.style.transform = 'translateX(60px) scale(0.95)';
    toastEl.style.transition = 'all 0.5s cubic-bezier(0.34,1.56,0.64,1)';
    setTimeout(() => { toastEl.style.opacity = '1'; toastEl.style.transform = 'translateX(0) scale(1)'; }, 50);
    @endif

    $(document).on('click', '[data-bs-target="#modalConfirm"]', function () {
        const id = $(this).data('id');
        $('#confirm_guia').text($(this).data('guia'));
        $('#confirm_sacos').text($(this).data('sacos'));
        $('#sacos_confirmados').val($(this).data('sacos'));
        window._bagsFromPort  = $(this).data('sacos');
        window._departureTime = $(this).data('saida');
        $('#formConfirm').attr('action', `/discharges/${id}/confirm`);
        $('#hora_chegada_balanca,#peso_bruto,#tara,#net_weight_calc,#transport_time_calc').val('');
        $('#divergence_alert').addClass('d-none');
    });

    $('#hora_chegada_balanca').on('change', calcTransportTime);
    $(document).on('input', '#sacos_confirmados', function () {
        const c = parseInt($(this).val()) || 0, p = parseInt(window._bagsFromPort) || 0;
        $('#divergence_alert').toggleClass('d-none', !(c !== p && c > 0));
    });
});

function applyFilters() {
    const df=$('#filterDateFrom').val(), dt=$('#filterDateTo').val(),
          tr=$('#filterTransportadora').val().toLowerCase(), st=$('#filterStatus').val(),
          tc=$('#filterTipoCarga').val(), div=$('#filterDivergencia').val();
    $('#dischargesTable tbody tr').each(function () {
        const r=$(this), rA=parseInt(r.data('sacos-alta'))||0, rB=parseInt(r.data('sacos-baixa'))||0;
        let show=true;
        if(df && r.data('data')<df) show=false;
        if(dt && r.data('data')>dt) show=false;
        if(tr && !r.data('transportadora').includes(tr)) show=false;
        if(st && r.data('status')!==st) show=false;
        if(tc==='alta'  && !(rA>0&&rB===0)) show=false;
        if(tc==='baixa' && !(rB>0&&rA===0)) show=false;
        if(tc==='misto' && !(rA>0&&rB>0))   show=false;
        if(div && r.data('divergencia')!==div) show=false;
        r.toggle(show);
    });
    renderTags(df,dt,tr,st,tc,div); dtTable.draw();
}

function clearFilters() {
    $('#filterDateFrom,#filterDateTo').val('');
    $('#filterTransportadora,#filterStatus,#filterTipoCarga,#filterDivergencia').val('');
    $('#dischargesTable tbody tr').show(); $('#activeTags').html(''); dtTable.draw();
}

function renderTags(df,dt,tr,st,tc,div) {
    const m={df,dt,tr,st,tc,div};
    const l={df:`De: ${df}`,dt:`Até: ${dt}`,tr:`Transportadora: ${tr}`,st:`Estado: ${st}`,tc:`Carga: ${tc}`,div:`Divergência: ${div}`};
    let html='';
    for(const [k,v] of Object.entries(m)){ if(v) html+=`<span class="filter-tag">${l[k]} <span class="remove-tag" onclick="removeTag('${k}')">✕</span></span>`; }
    $('#activeTags').html(html);
}

function removeTag(key) {
    const m={df:'filterDateFrom',dt:'filterDateTo',tr:'filterTransportadora',st:'filterStatus',tc:'filterTipoCarga',div:'filterDivergencia'};
    $(`#${m[key]}`).val(''); applyFilters();
}

function calcTransportTime() {
    const dep=window._departureTime, arr=$('#hora_chegada_balanca').val();
    if(!dep||!arr) return;
    const [hD,mD]=dep.split(':').map(Number),[hA,mA]=arr.split(':').map(Number);
    $('#transport_time_calc').val((hA*60+mA)-(hD*60+mD)>=0?(hA*60+mA)-(hD*60+mD):'---');
}

function calcNetWeight() {
    const net=(parseFloat($('#peso_bruto').val())||0)-(parseFloat($('#tara').val())||0);
    $('#net_weight_calc').val(net>0?net.toFixed(2):'---');
}

function imprimirRelatorio() {
    document.getElementById('reportArea').style.display='block';
    window.print();
    document.getElementById('reportArea').style.display='none';
}
</script>

</x-app-layout>