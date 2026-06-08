<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* ── Layout ── */
    .page-header-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
    .page-title{font-size:1.05rem;font-weight:700;color:#1e293b;margin:0}
    .page-sub{font-size:.72rem;color:#94a3b8;margin:2px 0 0}

    /* ── KPIs ── */
    .kpi-row{display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap}
    .kpi{flex:1;min-width:100px;background:#fff;border:1px solid #e9ecef;border-radius:10px;
         padding:10px 14px;position:relative;overflow:hidden}
    .kpi::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:10px 0 0 10px}
    .kpi.teal::before{background:#0dcaf0}.kpi.green::before{background:#198754}
    .kpi.blue::before{background:#1a56db}.kpi.red::before{background:#dc3545}
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
        padding:1px 6px;font-size:.65rem;font-weight:700;line-height:1.5}
    .sp.alta{background:#e8f0fe;color:#1a56db;border:1px solid #c3d9fa}
    .sp.baixa{background:#fde8e8;color:#dc3545;border:1px solid #f8c4c4}
    .sp-ref{font-size:.55rem;letter-spacing:.5px;text-transform:uppercase;opacity:.65}
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

    @media print{
        .no-print{display:none!important}
        body *{visibility:hidden}
        #reportArea,#reportArea *{visibility:visible}
        #reportArea{position:absolute;left:0;top:0;width:100%;padding:28px}
    }
</style>

<div class="container-fluid py-3 px-4">

    {{-- HEADER --}}
    <div class="page-header-bar no-print">
        <div>
            <div class="page-title"><i class="bi bi-ship me-2" style="color:#0d6efd;"></i>Controlo de Descargas</div>
            <div class="page-sub">Nitrato de Amónio — Gestão e rastreio de camiões</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="imprimirRelatorio()" class="btn btn-outline-secondary btn-sm fw-bold" style="border-radius:7px;font-size:.75rem;">
                <i class="bi bi-printer me-1"></i>Relatório
            </button>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm" style="border-radius:7px;font-size:.75rem;"
                    onclick="abrirModal('{{ route('discharges.create') }}', 'Registar Nova Descarga', 'primary')">
                <i class="bi bi-plus-lg me-1"></i>Nova Descarga
            </button>
        </div>
    </div>

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
        <div class="kpi blue">
            <div class="kpi-lbl">Alta</div>
            <div class="kpi-val" style="color:#1a56db;">{{ number_format($discharges->sum('sacos_alta'),0,',','.') }}</div>
            <div class="kpi-sub">sacos × 50 kg</div>
        </div>
        <div class="kpi red">
            <div class="kpi-lbl">Baixa</div>
            <div class="kpi-val" style="color:#dc3545;">{{ number_format($discharges->sum('sacos_baixa'),0,',','.') }}</div>
            <div class="kpi-sub">sacos × 25 kg</div>
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
                    <option value="alta">Só Alta</option>
                    <option value="baixa">Só Baixa</option>
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
            <div class="d-flex gap-1 align-items-end">
                <button class="btn-f btn-fa" onclick="applyFilters()"><i class="bi bi-search"></i>Filtrar</button>
                <button class="btn-f btn-fc" onclick="clearFilters()"><i class="bi bi-x"></i>Limpar</button>
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
                        <th>SACOS</th>
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
                    $sA = $d->sacos_alta ?? 0;
                    $sB = $d->sacos_baixa ?? 0;
                    $tot = $sA + $sB;
                    $div = $d->sacos_confirmados && $d->sacos_confirmados != $d->numero_sacos;
                @endphp
                <tr data-status="{{ $d->status }}"
                    data-transportadora="{{ strtolower($d->transportadora) }}"
                    data-data="{{ $d->data->format('Y-m-d') }}"
                    data-sacos-alta="{{ $sA }}" data-sacos-baixa="{{ $sB }}"
                    data-divergencia="{{ $div ? 'sim' : 'nao' }}">

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
                    </td>

                    {{-- Transportadora --}}
                    <td>
                        <span style="font-size:.75rem;color:#64748b;">{{ $d->transportadora }}</span>
                    </td>

                    {{-- Sacos --}}
                    <td>
                        <div class="d-flex flex-wrap gap-1 align-items-center">
                            @if($sA > 0)
                            <span class="sp alta"><span class="sp-ref">↑</span><span class="sp-n">{{ $sA }}</span></span>
                            @endif
                            @if($sB > 0)
                            <span class="sp baixa"><span class="sp-ref">↓</span><span class="sp-n">{{ $sB }}</span></span>
                            @endif
                            @if($tot > 0)
                            <span class="sp-total">{{ $tot }}</span>
                            @endif
                            @if($div)
                            <span class="div-badge"><i class="bi bi-exclamation-triangle-fill" style="font-size:.55rem;"></i>{{ $d->sacos_confirmados }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- Peso --}}
                    <td class="text-center">
                        <div class="fw-bold" style="font-size:.75rem;">{{ number_format($d->peso_total_porto,0,',','.') }} kg</div>
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
                            @if(in_array($d->status, ['pending','in_transit']))
                            <button class="btn-balanca"
                                    data-bs-toggle="modal" data-bs-target="#modalConfirm"
                                    data-id="{{ $d->id }}" data-guia="{{ $d->numero_guia }}"
                                    data-sacos="{{ $d->numero_sacos }}" data-saida="{{ $d->hora_saida_porto }}">
                                <i class="bi bi-speedometer2"></i>Balança
                            </button>
                            @endif
                            <button type="button"
                                onclick="abrirModal('{{ route('discharges.show', $d->id) }}', 'Descarga #{{ $d->id }}', 'dark')"
                                class="ab text-primary border-primary border-opacity-25" title="Ver">
                                <i class="bi bi-eye"></i>
                            </button>
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
                        Guia <strong id="confirm_guia"></strong>
                        — <strong id="confirm_sacos"></strong> sacos registados no porto
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
                                   step="0.01" min="0" oninput="calcNetWeight()">
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

                        {{-- Sacos confirmados --}}
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.72rem;font-weight:600;color:#94a3b8;">
                                Sacos Confirmados
                            </label>
                            <input type="number" name="sacos_confirmados" id="sacos_confirmados"
                                   class="form-control form-control-sm" style="border-radius:7px;"
                                   min="1" required>
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

{{-- RELATÓRIO IMPRESSÃO --}}
<div id="reportArea" style="display:none;">
    <div style="font-family:Arial,sans-serif;padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;
                    border-bottom:3px solid #198754;padding-bottom:14px;margin-bottom:20px;">
            <div>
                <h2 style="margin:0;color:#198754;font-size:1.1rem;">RELATÓRIO DE DESCARGAS</h2>
                <p style="margin:3px 0 0;color:#666;font-size:.78rem;">Nitrato de Amónio</p>
            </div>
            <div style="text-align:right;color:#666;font-size:.75rem;">
                <div>Emitido: {{ now()->format('d/m/Y H:i') }}</div>
                <div>Total: {{ $discharges->count() }} registos</div>
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:.72rem;">
            <thead>
                <tr style="background:#1a56db;color:white;">
                    <th style="padding:6px 8px;">Nº</th><th style="padding:6px 8px;">Data</th>
                    <th style="padding:6px 8px;">Guia</th><th style="padding:6px 8px;">Motorista</th>
                    <th style="padding:6px 8px;">Transportadora</th>
                    <th style="padding:6px 8px;text-align:center;">Alta</th>
                    <th style="padding:6px 8px;text-align:center;">Baixa</th>
                    <th style="padding:6px 8px;text-align:center;">Total</th>
                    <th style="padding:6px 8px;text-align:right;">Peso Líq.</th>
                    <th style="padding:6px 8px;text-align:center;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($discharges as $d)
                @php $dA=$d->sacos_alta??0; $dB=$d->sacos_baixa??0; @endphp
                <tr style="border-bottom:1px solid #eee;{{ $loop->even ? 'background:#f9f9f9;' : '' }}">
                    <td style="padding:5px 8px;color:#666;">#{{ $d->id }}</td>
                    <td style="padding:5px 8px;">{{ $d->data->format('d/m/Y') }}</td>
                    <td style="padding:5px 8px;font-weight:bold;color:#1a56db;">{{ $d->numero_guia }}</td>
                    <td style="padding:5px 8px;">{{ $d->motorista }}</td>
                    <td style="padding:5px 8px;">{{ $d->transportadora }}</td>
                    <td style="padding:5px 8px;text-align:center;color:#1a56db;font-weight:bold;">{{ $dA }}</td>
                    <td style="padding:5px 8px;text-align:center;color:#dc3545;font-weight:bold;">{{ $dB }}</td>
                    <td style="padding:5px 8px;text-align:center;font-weight:bold;">{{ $dA+$dB }}</td>
                    <td style="padding:5px 8px;text-align:right;">{{ $d->peso_liquido ? number_format($d->peso_liquido,0,',','.').' kg' : '—' }}</td>
                    <td style="padding:5px 8px;text-align:center;">{{ $d->status_label }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:24px;text-align:center;color:#aaa;font-size:.65rem;">
            Documento gerado automaticamente · {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let dtTable, _activeMenu = null;

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

    $(document).on('click','[data-bs-target="#modalConfirm"]',function(){
        const b=$(this);
        $('#confirm_guia').text(b.data('guia'));
        $('#confirm_sacos').text(b.data('sacos'));
        $('#sacos_confirmados').val(b.data('sacos'));
        window._bagsFromPort = b.data('sacos');
        window._departureTime = b.data('saida');
        $('#formConfirm').attr('action',`/discharges/${b.data('id')}/confirm`);
        $('#hora_chegada_balanca,#peso_bruto,#tara,#net_weight_calc,#transport_time_calc').val('');
        $('#divergence_alert').addClass('d-none');
    });

    $('#hora_chegada_balanca').on('change', calcTransportTime);
    $(document).on('input','#sacos_confirmados',function(){
        const c=parseInt($(this).val())||0, p=parseInt(window._bagsFromPort)||0;
        $('#divergence_alert').toggleClass('d-none', !(c!==p&&c>0));
    });

    document.getElementById('modalPrincipal').addEventListener('hidden.bs.modal',function(){
        document.getElementById('modalConteudo').innerHTML='';
        document.getElementById('modalConteudo').style.display='none';
        document.getElementById('modalLoading').style.display='flex';
    });
});

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

    fetch(url+'?modal=1',{ headers:{'X-Requested-With':'XMLHttpRequest','Accept':'text/html'} })
    .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.text(); })
    .then(html=>{
        const doc=new DOMParser().parseFromString(html,'text/html');
        const div=document.getElementById('modalConteudo');
        div.innerHTML=doc.body?doc.body.innerHTML:html;
        div.style.display='block';
        document.getElementById('modalLoading').style.display='none';
        div.querySelectorAll('script').forEach(s=>{ const n=document.createElement('script'); n.textContent=s.textContent; s.parentNode.replaceChild(n,s); });
        div.querySelectorAll('form').forEach(f=>{ f.addEventListener('submit',function(e){ e.preventDefault(); submeterFormModal(this); }); });
    })
    .catch(err=>{
        document.getElementById('modalConteudo').innerHTML=`<div class="alert alert-danger m-3">Erro: ${err.message}</div>`;
        document.getElementById('modalConteudo').style.display='block';
        document.getElementById('modalLoading').style.display='none';
    });
}

function submeterFormModal(form) {
    const btn=form.querySelector('[type=submit]');
    if(btn){ btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>A guardar...'; }
    fetch(form.action,{ method:'POST', body:new FormData(form), headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r=>r.text())
    .then(html=>{
        const doc=new DOMParser().parseFromString(html,'text/html');
        if(doc.querySelector('.alert-danger')){
            const div=document.getElementById('modalConteudo');
            div.innerHTML=doc.body?doc.body.innerHTML:html;
            div.querySelectorAll('script').forEach(s=>{ const n=document.createElement('script'); n.textContent=s.textContent; s.parentNode.replaceChild(n,s); });
            div.querySelectorAll('form').forEach(f=>{ f.addEventListener('submit',function(e){ e.preventDefault(); submeterFormModal(this); }); });
        } else {
            bootstrap.Modal.getInstance(document.getElementById('modalPrincipal')).hide();
            window.location.reload();
        }
    })
    .catch(()=>{ bootstrap.Modal.getInstance(document.getElementById('modalPrincipal')).hide(); window.location.reload(); });
}

function imprimirRelatorio(){
    document.getElementById('reportArea').style.display='block';
    window.print();
    document.getElementById('reportArea').style.display='none';
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

function applyFilters(){
    const df=$('#fDe').val(), dt=$('#fAte').val(),
          tr=$('#fTrans').val().toLowerCase(), st=$('#fStatus').val(),
          tc=$('#fCarga').val(), dv=$('#fDiv').val();
    $('#dTable tbody tr').each(function(){
        const r=$(this), rA=parseInt(r.data('sacos-alta'))||0, rB=parseInt(r.data('sacos-baixa'))||0;
        let show=true;
        if(df && r.data('data')<df) show=false;
        if(dt && r.data('data')>dt) show=false;
        if(tr && !r.data('transportadora').includes(tr)) show=false;
        if(st && r.data('status')!==st) show=false;
        if(tc==='alta' && !(rA>0&&rB===0)) show=false;
        if(tc==='baixa' && !(rB>0&&rA===0)) show=false;
        if(tc==='misto' && !(rA>0&&rB>0)) show=false;
        if(dv && r.data('divergencia')!==dv) show=false;
        r.toggle(show);
    });
    renderTags(df,dt,tr,st,tc,dv); dtTable.draw();
}
function clearFilters(){
    $('#fDe,#fAte').val('');
    $('#fTrans,#fStatus,#fCarga,#fDiv').val('');
    $('#dTable tbody tr').show(); $('#activeTags').html(''); dtTable.draw();
}
function renderTags(df,dt,tr,st,tc,dv){
    const m={df,dt,tr,st,tc,dv};
    const l={df:`De: ${df}`,dt:`Até: ${dt}`,tr:`Transportadora: ${tr}`,st:`Estado: ${st}`,tc:`Carga: ${tc}`,dv:`Divergência: ${dv}`};
    let html='';
    for(const [k,v] of Object.entries(m)){ if(v) html+=`<span class="ftag">${l[k]} <span class="rm" onclick="removeTag('${k}')">✕</span></span>`; }
    $('#activeTags').html(html);
}
function removeTag(k){
    const m={df:'fDe',dt:'fAte',tr:'fTrans',st:'fStatus',tc:'fCarga',dv:'fDiv'};
    $(`#${m[k]}`).val(''); applyFilters();
}

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