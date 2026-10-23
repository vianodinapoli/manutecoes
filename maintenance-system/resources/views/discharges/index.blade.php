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
@endphp

<style>
    /* ── Layout ── */
    .page-header-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}

    /* ── Cabeçalho da operação ── */
    .op-head{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:14px 18px;margin-bottom:14px}
    .op-ref{font-size:.72rem;color:#94a3b8}
    .op-ref b{color:#475569;font-weight:600}
    .op-bar{height:10px;border-radius:6px;background:#f1f3f5;overflow:hidden;margin:10px 0 4px}
    .op-bar>div{height:100%;border-radius:6px;background:linear-gradient(90deg,#1a56db,#0dcaf0)}
    .op-bar.done>div{background:#198754}
    .op-nums{display:flex;justify-content:space-between;font-size:.7rem;color:#64748b;flex-wrap:wrap;gap:6px}
    .meta-chip{display:inline-block;background:#f1f3f5;color:#475569;border-radius:4px;
               font-size:.62rem;padding:1px 7px;margin:2px 3px 0 0}

    /* ── Stickers de transportadoras ── */
    .tp-label{font-size:.6rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8;margin-right:6px}
    .tp-chip{display:inline-flex;align-items:center;gap:4px;background:#eef2ff;color:#3b4cca;border:1px solid #d5dcff;
             border-radius:20px;font-size:.64rem;font-weight:600;padding:2px 9px;margin:2px 3px 0 0;white-space:nowrap}
    .tp-chip.extra{background:#fd7e14;color:#fff;border-color:#fd7e14;box-shadow:0 0 0 3px rgba(253,126,20,.22)}

    /* ── Plano vs realizado ── */
    #planoTable th{font-size:.58rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#868e96;
                   border-bottom:1px solid #e9ecef;padding:6px 8px;white-space:nowrap}
    #planoTable td{font-size:.76rem;padding:6px 8px;vertical-align:middle;border-bottom:1px solid #f1f3f5}
    .prod-dot{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:6px}
    .mini-bar{height:6px;border-radius:4px;background:#f1f3f5;overflow:hidden;min-width:70px}
    .mini-bar>div{height:100%;border-radius:4px}

    /* ── KPIs ── */
    .kpi-row{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
    .kpi{flex:1;min-width:100px;background:#fff;border:1px solid #e9ecef;border-radius:10px;
         padding:10px 14px;position:relative;overflow:hidden}
    .kpi::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:10px 0 0 10px}
    .kpi.teal::before{background:#0dcaf0}.kpi.green::before{background:#198754}
    .kpi.purple::before{background:#6f42c1}.kpi.gold::before{background:#fd7e14}
    .kpi-lbl{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#adb5bd;margin-bottom:2px}
    .kpi-val{font-size:1.35rem;font-weight:800;line-height:1.1;color:#1e293b}
    .kpi-sub{font-size:.62rem;color:#adb5bd;margin-top:1px}

    /* ── Filter panel ── */
    .filter-panel{background:#fff;border:1px solid #e9ecef;border-radius:10px;
                  padding:14px 18px;margin-bottom:14px}
    .filter-row{display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end}
    .fi{display:flex;flex-direction:column;gap:3px}
    .fi label{font-size:.62rem;font-weight:600;letter-spacing:.6px;text-transform:uppercase;color:#94a3b8}
    .fi select,.fi input{border:1px solid #dee2e6;border-radius:6px;padding:5px 10px;
                          font-size:.78rem;color:#343a40;background:#f8f9fa;min-width:120px;outline:none}
    .fi select:focus,.fi input:focus{border-color:#0d6efd;background:#fff}
    .btn-f{padding:6px 14px;border-radius:6px;font-size:.75rem;font-weight:600;
           cursor:pointer;border:1px solid #dee2e6;display:inline-flex;align-items:center;gap:5px}
    .btn-fa{background:#0d6efd;color:#fff;border-color:#0d6efd}.btn-fa:hover{background:#0b5ed7}
    .btn-fc{background:#f8f9fa;color:#495057}.btn-fc:hover{background:#e9ecef}
    .btn-fr{background:#198754;color:#fff;border-color:#198754}.btn-fr:hover{background:#146c43}
    .atr-chip{display:inline-block;font-size:.55rem;font-weight:700;letter-spacing:.5px;padding:1px 6px;border-radius:3px;margin:2px 0 0 4px}
    .atr-chip.normal{background:#e8f0fe;color:#1a56db}.atr-chip.link{background:#fde8e8;color:#dc3545}
    .active-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:8px}
    .ftag{display:inline-flex;align-items:center;gap:4px;background:#e7f1ff;color:#0d6efd;
          border:1px solid #b6d0ff;border-radius:20px;padding:2px 8px;font-size:.65rem;font-weight:500}
    .ftag .rm{cursor:pointer;opacity:.6}.ftag .rm:hover{opacity:1}

    /* ── Table ── */
    .table-card{background:#fff;border:1px solid #e9ecef;border-radius:10px;overflow:hidden}
    #dTable thead tr{background:#f8f9fa}
    #dTable thead th{font-size:.6rem;font-weight:700;letter-spacing:.9px;text-transform:uppercase;
                      color:#868e96;border-bottom:1px solid #e9ecef;padding:7px 10px;white-space:nowrap}
    #dTable tbody td{padding:5px 10px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.78rem}
    #dTable tbody tr:hover{background:#fafbff}
    #dTable tbody tr:last-child td{border-bottom:none}

    /* ── Sacos pills ── */
    .sp{display:inline-flex;align-items:center;gap:3px;border-radius:4px;
        padding:1px 6px;font-size:.65rem;font-weight:700;line-height:1.5;border:1px solid}
    .sp-ref{font-size:.55rem;letter-spacing:.5px;text-transform:uppercase;opacity:.75}
    .sp-n{font-size:.75rem;font-weight:800}
    .sp-total{font-size:.6rem;color:#94a3b8;background:#f1f3f5;border-radius:3px;padding:0 4px}
    .div-badge{display:inline-flex;align-items:center;gap:2px;background:#fff3cd;color:#856404;
               border:1px solid #ffc107;border-radius:3px;padding:0 5px;font-size:.58rem;font-weight:600}

    /* ── Status badge ── */
    .sbadge{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;
            border-radius:20px;font-size:.63rem;font-weight:600;white-space:nowrap}

    /* ── Action btns ── */
    .ab{width:24px;height:24px;border-radius:5px;display:inline-flex;align-items:center;
        justify-content:center;font-size:.68rem;border:1px solid;transition:all .12s;
        text-decoration:none;cursor:pointer;background:transparent}
    .btn-balanca{font-size:.62rem;padding:2px 8px;border-radius:20px;font-weight:700;
                 background:#198754;color:#fff;border:none;white-space:nowrap;
                 display:inline-flex;align-items:center;gap:3px}
    .btn-balanca:hover{background:#146c43;color:#fff}

    /* ── Motorista block ── */
    .motorista-name{font-size:.78rem;font-weight:600;color:#1e293b;line-height:1.2}
    .mat-badge{display:inline-block;background:#1e293b;color:#fff;border-radius:3px;
               font-size:.55rem;letter-spacing:1px;padding:1px 5px;margin-top:2px;font-weight:600}

    /* ── DataTable overrides ── */
    div.dataTables_wrapper div.dataTables_filter input{border-radius:6px;border:1px solid #dee2e6;
        padding:4px 10px;font-size:.78rem}
    div.dataTables_wrapper div.dataTables_length select{border-radius:6px;border:1px solid #dee2e6;
        padding:3px 6px;font-size:.78rem}
    div.dataTables_wrapper div.dataTables_info{font-size:.72rem;color:#94a3b8;padding-top:6px}
    div.dataTables_wrapper div.dataTables_paginate .paginate_button{font-size:.72rem;border-radius:5px!important}

    .modal-loading{display:flex;align-items:center;justify-content:center;
                   height:260px;flex-direction:column;gap:10px;color:#adb5bd}

</style>

<div class="container-fluid py-3 px-4">

    {{-- HEADER --}}
    <div class="page-header-bar no-print">
        <div>
            <a href="{{ $readonly ? route('operations.archive') : route('operations.index') }}"
               class="text-decoration-none" style="font-size:.7rem;color:#94a3b8;">
                <i class="bi bi-arrow-left me-1"></i>{{ $readonly ? 'Arquivo' : 'Operações' }}
            </a>
            <div class="page-title mt-1">
                <i class="bi bi-ship me-2" style="color:#0d6efd;"></i>{{ $operation->navio }}
                @if($readonly)
                    <span class="badge bg-secondary ms-2" style="font-size:.6rem;vertical-align:middle;">ARQUIVADA</span>
                @else
                    <span class="badge ms-2" style="background:#e0f7fc;color:#0aa2c0;font-size:.6rem;vertical-align:middle;">EM CURSO</span>
                @endif
            </div>
            <div class="page-sub">{{ $operation->empresa }} — Nitrato de Amónio</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operations.report', $operation) }}" target="_blank"
               class="btn btn-outline-secondary btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;">
                <i class="bi bi-file-earmark-text me-1"></i>Relatório
            </a>

            @if(!$readonly)
                <button class="btn btn-outline-primary btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;"
                        onclick='abrirOperacaoModal(@json($operation->modalPayload()))'>
                    <i class="bi bi-pencil me-1"></i>Editar Operação
                </button>

                <form action="{{ route('operations.archiveOperation', $operation) }}" method="POST" class="m-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-dark btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;"
                            onclick="return confirm('Enviar esta operação para o arquivo?')">
                        <i class="bi bi-archive me-1"></i>Arquivar
                    </button>
                </form>

                <button class="btn btn-primary btn-sm fw-bold shadow-sm" style="border-radius:7px;font-size:.75rem;"
                        onclick="abrirModal('{{ route('discharges.create', ['operation_id' => $operation->id]) }}', 'Registar Nova Descarga', 'primary')">
                    <i class="bi bi-plus-lg me-1"></i>Nova Descarga
                </button>
            @else
                <form action="{{ route('operations.restore', $operation) }}" method="POST" class="m-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline-warning btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;"
                            onclick="return confirm('Reactivar esta operação?')">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reactivar
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ALERTAS --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 no-print" style="font-size:.78rem;border-radius:8px;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- TOAST --}}
    @if(session('success'))
    <div class="position-fixed top-0 end-0 p-3 no-print" style="z-index:9999;">
        <div id="successToast" class="toast show border-0"
             style="border-radius:10px;min-width:240px;overflow:hidden;
                    background:rgba(25,135,84,.95);backdrop-filter:blur(12px);
                    box-shadow:0 8px 24px rgba(25,135,84,.3);">
            <div class="d-flex align-items-center gap-2 px-3 py-2">
                <i class="bi bi-check-circle-fill text-white" style="font-size:1.1rem;"></i>
                <div style="font-size:.75rem;color:rgba(255,255,255,.95);">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white opacity-75 ms-auto"
                        data-bs-dismiss="toast" style="font-size:.5rem;"></button>
            </div>
            <div style="height:2px;background:rgba(255,255,255,.15);">
                <div id="toastProg" style="height:100%;width:100%;background:rgba(255,255,255,.5);transition:width 3.5s linear;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- CABEÇALHO DA OPERAÇÃO + PLANO VS REALIZADO --}}
    <div class="op-head no-print">
        <div class="op-ref">
            BL: <b>{{ $operation->bl ?: '—' }}</b> &nbsp;·&nbsp;
            Factura: <b>{{ $operation->factura ?: '—' }}</b> &nbsp;·&nbsp;
            Empresa: <b>{{ $operation->empresa }}</b>
            @if($operation->data_chegada) &nbsp;·&nbsp; Chegada: <b>{{ $operation->data_chegada->format('d/m/Y') }}</b> @endif
            @if($operation->started_at) &nbsp;·&nbsp; Iniciada: <b>{{ $operation->started_at->format('d/m/Y H:i') }}</b> @endif
            @if($readonly && $operation->archived_at) &nbsp;·&nbsp; Arquivada em: <b>{{ $operation->archived_at->format('d/m/Y') }}</b> @endif
        </div>

        @if(!empty($operation->metadata))
        <div>
            @foreach($operation->metadata as $k => $v)
                <span class="meta-chip"><b>{{ $k }}:</b> {{ $v }}</span>
            @endforeach
        </div>
        @endif

        @if(!empty($operation->transportadoras))
        <div class="mt-1">
            <span class="tp-label">Transportadoras</span>
            @foreach($operation->transportadoras as $t)
                <span class="tp-chip {{ !empty($t['extra']) ? 'extra' : '' }}"
                      @if(!empty($t['extra'])) title="Fora da lista prevista (acrescentada ao lançar um camião)" @endif>
                    @if(!empty($t['extra']))<i class="bi bi-exclamation-triangle-fill"></i>@endif{{ $t['nome'] }}
                </span>
            @endforeach
        </div>
        @endif

        @if($operation->observacoes)
            <div class="mt-1" style="font-size:.72rem;color:#64748b;"><i class="bi bi-chat-left-text me-1"></i>{{ $operation->observacoes }}</div>
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
                        <span class="text-muted fw-normal" style="font-size:.65rem;">
                            @if($pi->unidade === 'saco') · {{ $fmt($pi->peso_unitario_kg) }} kg/saco @else · granel @endif
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

        <div class="op-bar {{ $pct >= 100 ? 'done' : '' }}"><div style="width:{{ $pct }}%"></div></div>
        <div class="op-nums">
            <span><b>{{ number_format($recebido, 1, ',', '.') }} t</b> pesadas na balança de {{ number_format($esperado, 1, ',', '.') }} t esperadas ({{ $pct }}%)</span>
            <span style="color:{{ $falta > 0 ? '#dc3545' : '#198754' }};">
                {{ $falta > 0 ? 'Faltam ' . number_format($falta, 1, ',', '.') . ' t' : ($falta < 0 ? 'Excesso de ' . number_format(abs($falta), 1, ',', '.') . ' t' : 'Quantidade completa') }}
            </span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row no-print">
        <div class="kpi teal">
            <div class="kpi-lbl">Em Trânsito</div>
            <div class="kpi-val" style="color:#0dcaf0;">{{ $discharges->where('status','in_transit')->count() }}</div>
            <div class="kpi-sub">camiões</div>
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
            <div class="kpi-val" style="color:#fd7e14;font-size:1.1rem;padding-top:3px;">
                {{ number_format($discharges->whereNotNull('peso_liquido')->sum('peso_liquido')/1000,1,',','.') }}t
            </div>
            <div class="kpi-sub">confirmado</div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="filter-panel no-print">
        <div class="filter-row">
            <div class="fi"><label>De</label><input type="date" id="fDe"></div>
            <div class="fi"><label>Até</label><input type="date" id="fAte"></div>
            <div class="fi">
                <label>Transportadora</label>
                <select id="fTrans">
                    <option value="">Todas</option>
                    @foreach($discharges->pluck('transportadora')->unique()->sort() as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fi">
                <label>Estado</label>
                <select id="fStatus">
                    <option value="">Todos</option>
                    <option value="in_transit">Em Trânsito</option>
                    <option value="confirmed">Confirmado</option>
                    <option value="pending">Pendente</option>
                </select>
            </div>
            <div class="fi">
                <label>Carga</label>
                <select id="fCarga">
                    <option value="">Todos</option>
                    @foreach($planItems as $pi)
                    <option value="{{ mb_strtolower($pi->nome) }}">Só {{ $pi->nome }}</option>
                    @endforeach
                    <option value="misto">Misto</option>
                </select>
            </div>
            <div class="fi">
                <label>Divergência</label>
                <select id="fDiv">
                    <option value="">Todos</option>
                    <option value="sim">Com divergência</option>
                    <option value="nao">Sem divergência</option>
                </select>
            </div>
            <div class="fi">
                <label>Atrelado</label>
                <select id="fAtrel">
                    <option value="">Todos</option>
                    <option value="normal">Normal (até 24)</option>
                    <option value="link">Link (mais de 24)</option>
                    <option value="nd">Não definido</option>
                </select>
            </div>
            <div class="d-flex gap-1 align-items-end flex-wrap">
                <button class="btn-f btn-fa" onclick="applyFilters()"><i class="bi bi-search"></i>Filtrar</button>
                <button class="btn-f btn-fc" onclick="clearFilters()"><i class="bi bi-x"></i>Limpar</button>
                <button class="btn-f btn-fr" onclick="gerarRelatorioCargas()"
                        title="Relatório de cargas por transportadora e tipo de atrelado, com os filtros aplicados">
                    <i class="bi bi-file-earmark-bar-graph"></i>Gerar relatório
                </button>
            </div>
        </div>
        <div class="active-tags" id="activeTags"></div>
    </div>

    {{-- TABELA --}}
    <div class="table-card no-print">
        <div class="p-2 pt-3">
            <table class="table table-hover align-middle mb-0" id="dTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:70px">#&nbsp;/&nbsp;DATA</th>
                        <th style="width:90px">GUIA</th>
                        <th>MOTORISTA</th>
                        <th>TRANSPORTADORA</th>
                        <th>CARGA</th>
                        <th class="text-center" style="width:100px">PESO</th>
                        <th class="text-center" style="width:70px">TEMPO</th>
                        <th class="text-center" style="width:100px">ESTADO</th>
                        <th class="text-center" style="width:120px">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($discharges as $d)
                @php
                    $stClass = match($d->status){
                        'confirmed'  => 'bg-success bg-opacity-10 text-success',
                        'in_transit' => 'bg-info bg-opacity-10 text-info',
                        default      => 'bg-warning bg-opacity-10 text-warning',
                    };
                    $stIcon = match($d->status){
                        'confirmed'  => 'check-circle-fill',
                        'in_transit' => 'truck',
                        default      => 'hourglass-split',
                    };
                    $div = $d->hasDivergence();
                    $produtos = $d->items->map(fn ($i) => mb_strtolower($i->operationItem->nome))->implode('|');
                    $itensBalanca = $d->items->map(fn ($i) => [
                        'id'         => $i->operation_item_id,
                        'nome'       => $i->operationItem->nome,
                        'unidade'    => $i->operationItem->unidade,
                        'quantidade' => (float) $i->quantidade,
                    ])->values();
                @endphp
                <tr data-status="{{ $d->status }}"
                    data-transportadora="{{ strtolower($d->transportadora) }}"
                    data-data="{{ $d->data->format('Y-m-d') }}"
                    data-produtos="{{ $produtos }}"
                    data-divergencia="{{ $div ? 'sim' : 'nao' }}"
                    data-atrelado="{{ $d->tipo_atrelado ?: 'nd' }}">

                    {{-- Nº / Data --}}
                    <td>
                        <span class="fw-bold" style="font-size:.75rem;color:#64748b;">#{{ $d->id }}</span>
                        <div style="font-size:.68rem;color:#94a3b8;">{{ $d->data->format('d/m/Y') }}</div>
                    </td>

                    {{-- Guia --}}
                    <td>
                        <span class="fw-bold" style="font-size:.75rem;color:#1a56db;">{{ $d->numero_guia }}</span>
                    </td>

                    {{-- Motorista --}}
                    <td>
                        <div class="motorista-name">{{ $d->motorista }}</div>
                        <span class="mat-badge">{{ $d->matricula }}</span>
                        @if($d->tipo_atrelado)
                            <span class="atr-chip {{ $d->tipo_atrelado }}"
                                  title="{{ $d->atreladoInconsistente() ? 'Não bate com o nº de sacos' : 'Atrelado ' . $d->tipo_atrelado }}">
                                {{ strtoupper($d->tipo_atrelado) }}@if($d->atreladoInconsistente()) ⚠@endif
                            </span>
                        @endif
                    </td>

                    {{-- Transportadora --}}
                    <td>
                        <span style="font-size:.75rem;color:#64748b;">{{ $d->transportadora }}</span>
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
                            <span class="sp-total">{{ $d->total_sacos }}</span>
                            @endif
                            @if($div)
                            <span class="div-badge"><i class="bi bi-exclamation-triangle-fill" style="font-size:.55rem;"></i>{{ $d->sacos_confirmados }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- Peso --}}
                    <td class="text-center">
                        <div class="fw-bold" style="font-size:.75rem;">{{ number_format($d->peso_total_estimado, 0, ',', '.') }} kg</div>
                        @if($d->peso_liquido)
                        <div style="font-size:.65rem;color:#198754;">Líq: {{ number_format($d->peso_liquido,0,',','.') }}</div>
                        @endif
                    </td>

                    {{-- Tempo --}}
                    <td class="text-center">
                        @if($d->tempo_transporte)
                        <span style="font-size:.7rem;color:#64748b;"><i class="bi bi-clock" style="font-size:.6rem;"></i> {{ $d->tempo_transporte }}m</span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>

                    {{-- Estado --}}
                    <td class="text-center">
                        <span class="sbadge {{ $stClass }}">
                            <i class="bi bi-{{ $stIcon }}" style="font-size:.55rem;"></i>
                            {{ $d->status_label }}
                        </span>
                    </td>

                    {{-- Ações --}}
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                            @if(!$readonly && in_array($d->status, ['pending','in_transit']))
                            <button class="btn-balanca"
                                    data-bs-toggle="modal" data-bs-target="#modalConfirm"
                                    data-id="{{ $d->id }}" data-guia="{{ $d->numero_guia }}"
                                    data-itens='@json($itensBalanca)'
                                    data-saida="{{ $d->hora_saida_porto }}">
                                <i class="bi bi-speedometer2"></i>Balança
                            </button>
                            @endif
                            <button type="button"
                                onclick="abrirModal('{{ route('discharges.show', $d->id) }}', 'Descarga #{{ $d->id }}', 'dark')"
                                class="ab text-primary border-primary border-opacity-25" title="Ver">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if(!$readonly)
                            <button type="button"
                                onclick="abrirModal('{{ route('discharges.edit', $d->id) }}', 'Editar #{{ $d->id }}', 'warning')"
                                class="ab text-warning border-warning border-opacity-25" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->user()->hasRole('super-admin'))
                            <form action="{{ route('discharges.destroy', $d->id) }}" method="POST" class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="ab text-danger border-danger border-opacity-25"
                                        onclick="return confirm('Eliminar?')" title="Eliminar">
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
<div class="modal fade" id="modalPrincipal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 text-white" id="modalHeader"
                 style="background:linear-gradient(135deg,#1a56db,#0dcaf0);padding:12px 20px;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-text" id="modalIcon" style="font-size:1rem;"></i>
                    <h5 class="modal-title fw-bold mb-0" style="font-size:.9rem;" id="modalTitulo">A carregar...</h5>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="min-height:360px;">
                <div id="modalLoading" class="modal-loading">
                    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;"></div>
                    <span style="font-size:.78rem;">A carregar...</span>
                </div>
                <div id="modalConteudo" style="display:none;padding:20px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BALANÇA --}}
<div class="modal fade" id="modalConfirm" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header bg-success text-white border-0" style="padding:12px 20px;">
                <h5 class="modal-title fw-bold" style="font-size:.88rem;">
                    <i class="bi bi-speedometer2 me-2"></i>Confirmação na Balança
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formConfirm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body p-4">

                    {{-- Info da guia --}}
                    <div class="alert py-2 mb-3 border-0"
                         style="border-radius:8px;background:#e8f4fd;color:#1a56db;font-size:.78rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Guia <strong id="confirm_guia"></strong> — confirme a quantidade de cada produto
                    </div>

                    <div class="row g-2">

                        {{-- Hora chegada + tempo --}}
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Hora de Chegada
                            </label>
                            <input type="time" name="hora_chegada_balanca" id="hora_chegada_balanca"
                                   class="form-control form-control-sm" style="border-radius:7px;" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Tempo Transporte
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="transport_time_calc" class="form-control bg-light"
                                       style="border-radius:7px 0 0 7px;" readonly placeholder="Auto">
                                <span class="input-group-text small">min</span>
                            </div>
                        </div>

                        {{-- Peso Bruto com botão de leitura --}}
                        <div class="col-12">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Peso Bruto (kg)
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="peso_bruto" id="peso_bruto"
                                       class="form-control" style="border-radius:7px 0 0 7px;"
                                       step="0.01" min="0" placeholder="0.00"
                                       oninput="calcNetWeight()" required>
                                <button type="button" id="btnLerBalanca"
                                        onclick="lerBalancaUI()"
                                        class="btn btn-sm btn-outline-success fw-bold"
                                        style="border-radius:0 7px 7px 0;font-size:.72rem;white-space:nowrap;padding:0 12px;">
                                    <i class="bi bi-usb-symbol me-1"></i>Ler Balança
                                </button>
                            </div>
                            {{-- Status da leitura --}}
                            <div id="balanca-status" class="mt-1" style="font-size:.68rem;min-height:16px;"></div>
                        </div>

                        {{-- Tara --}}
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Tara (kg)
                            </label>
                            <input type="number" name="tara" id="tara"
                                   class="form-control form-control-sm" style="border-radius:7px;"
                                   step="0.01" min="0" oninput="calcNetWeight()" required>
                        </div>

                        {{-- Peso líquido calculado --}}
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Peso Líquido (kg)
                            </label>
                            <input type="text" id="net_weight_calc"
                                   class="form-control form-control-sm bg-light fw-bold text-success"
                                   style="border-radius:7px;" readonly placeholder="Calculado auto.">
                        </div>

                        {{-- Quantidades confirmadas, uma por produto (gerado por JS) --}}
                        <div class="col-12">
                            <label class="form-label mb-1" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Quantidade confirmada por produto
                            </label>
                            <div id="confirmItens" class="row g-2"></div>
                            <div id="divergence_alert" class="text-danger d-none"
                                 style="font-size:.68rem;margin-top:3px;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Divergência detectada!
                            </div>
                        </div>

                        {{-- Config porta (colapsável) --}}
                        <div class="col-12 mt-1">
                            <a class="text-muted" style="font-size:.68rem;cursor:pointer;text-decoration:none;"
                               data-bs-toggle="collapse" href="#configSerial">
                                <i class="bi bi-gear me-1"></i>Configurações da porta serial
                            </a>
                            <div class="collapse" id="configSerial">
                                <div class="row g-2 mt-1 p-2 rounded"
                                     style="background:#f8f9fa;border:1px solid #e9ecef;">
                                    <div class="col-6">
                                        <label style="font-size:.65rem;color:#94a3b8;font-weight:600;">
                                            Baud Rate
                                        </label>
                                        <select id="cfg_baud" class="form-select form-select-sm"
                                                style="border-radius:6px;font-size:.75rem;">
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
                                        <label style="font-size:.65rem;color:#94a3b8;font-weight:600;">
                                            Paridade
                                        </label>
                                        <select id="cfg_parity" class="form-select form-select-sm"
                                                style="border-radius:6px;font-size:.75rem;">
                                            <option value="none" selected>None</option>
                                            <option value="even">Even</option>
                                            <option value="odd">Odd</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2"
                                             style="font-size:.68rem;color:#94a3b8;">
                                            <i class="bi bi-info-circle"></i>
                                            Consulta o manual da balança se não souberes os valores.
                                            O mais comum é 9600/8N1.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Observações --}}
                        <div class="col-12">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Observações
                            </label>
                            <textarea name="observacoes" class="form-control form-control-sm"
                                      style="border-radius:7px;" rows="2"
                                      placeholder="Ex: saco rasgado, diferença de peso..."></textarea>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding:0 20px 16px;">
                    <button type="button" class="btn btn-light btn-sm px-3"
                            style="border-radius:7px;" data-bs-dismiss="modal"
                            onclick="Balanca.cancelar()">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold"
                            style="border-radius:7px;">
                        <i class="bi bi-check-circle me-1"></i>Confirmar e Guardar
                    </button>
                </div>
            </form>
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
let dtTable, _activeMenu = null;
let _itensBalanca = [];

$(document).ready(function(){
    dtTable = $('#dTable').DataTable({
        language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order:[[0,'desc']],
        columnDefs:[{ orderable:false, targets:[7,8] }],
        pageLength:15,
    });

    @if(session('success'))
    const t = document.getElementById('successToast');
    new bootstrap.Toast(t,{delay:3500}).show();
    setTimeout(()=>{ document.getElementById('toastProg').style.width='0%'; },50);
    @endif

    // Abrir modal da balança: gera um campo por produto
    $(document).on('click','[data-bs-target="#modalConfirm"]',function(){
        const b=$(this);
        $('#confirm_guia').text(b.data('guia'));
        window._departureTime = b.data('saida');
        $('#formConfirm').attr('action',`/discharges/${b.data('id')}/confirm`);
        $('#hora_chegada_balanca,#peso_bruto,#tara,#net_weight_calc,#transport_time_calc').val('');
        $('#divergence_alert').addClass('d-none');
        construirCamposBalanca(b.data('itens') || []);
    });

    $('#hora_chegada_balanca').on('change', calcTransportTime);

    document.getElementById('modalPrincipal').addEventListener('hidden.bs.modal',function(){
        document.getElementById('modalConteudo').innerHTML='';
        document.getElementById('modalConteudo').style.display='none';
        document.getElementById('modalLoading').style.display='flex';
    });
});

/* ── Balança: campos por produto ── */
function construirCamposBalanca(itens) {
    _itensBalanca = itens;
    const box = document.getElementById('confirmItens');
    box.innerHTML = '';

    itens.forEach(it => {
        const col = document.createElement('div');
        col.className = 'col-md-6';

        const lbl = document.createElement('label');
        lbl.style.cssText = 'font-size:.68rem;color:#64748b;font-weight:600;';
        lbl.textContent = `${it.nome} (registado: ${it.quantidade.toLocaleString('pt-PT')} ${it.unidade === 'tonelada' ? 't' : 'sacos'})`;

        const inp = document.createElement('input');
        inp.type = 'number';
        inp.name = `confirmados[${it.id}]`;
        inp.className = 'form-control form-control-sm confirm-qtd';
        inp.style.borderRadius = '7px';
        inp.min = 0;
        inp.step = it.unidade === 'tonelada' ? '0.001' : '1';
        inp.value = it.quantidade;
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
    document.activeElement.blur();
    new bootstrap.Modal(document.getElementById('modalPrincipal')).show();

    // a URL de "create" já traz ?operation_id=…, por isso escolhemos ? ou &
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
        document.getElementById('modalConteudo').innerHTML=`<div class="alert alert-danger m-3">Erro: ${err.message}</div>`;
        document.getElementById('modalConteudo').style.display='block';
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
    box.className = 'alert alert-danger py-2 small mb-3 erros-modal';
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
        bootstrap.Modal.getInstance(document.getElementById('modalPrincipal')).hide();
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
    const [hD,mD]=dep.split(':').map(Number),[hA,mA]=arr.split(':').map(Number);
    const diff=(hA*60+mA)-(hD*60+mD);
    $('#transport_time_calc').val(diff>=0?diff:'—');
}
function calcNetWeight(){
    const net=(parseFloat($('#peso_bruto').val())||0)-(parseFloat($('#tara').val())||0);
    $('#net_weight_calc').val(net>0?net.toFixed(2):'—');
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
        if (v) html += `<span class="ftag">${l[k]} <span class="rm" onclick="removeTag('${k}')">✕</span></span>`;
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

/* ── Leitura da balança (porta serial) ── */
async function lerBalancaUI() {
    const btn = document.getElementById('btnLerBalanca');
    const status = document.getElementById('balanca-status');

    const setStatus = (msg, cor = '#64748b') => {
        status.innerHTML = `<span style="color:${cor};">${msg}</span>`;
    };

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>A ler...';

    try {
        const peso = await Balanca.lerPeso({
            baudRate : parseInt(document.getElementById('cfg_baud').value)   || 9600,
            parity   : document.getElementById('cfg_parity').value           || 'none',
            dataBits : 8,
            stopBits : 1,
            onStatus : msg => setStatus('⏳ ' + msg),
        });

        document.getElementById('peso_bruto').value = peso.toFixed(2);
        calcNetWeight();
        setStatus(`✓ Peso lido: ${peso.toFixed(2)} kg`, '#198754');

    } catch (err) {
        setStatus('✗ ' + err.message, '#dc3545');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-usb-symbol me-1"></i>Ler Balança';
    }
}
</script>

</x-app-layout>