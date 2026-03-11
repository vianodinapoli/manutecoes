<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .kpi-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.04);transition:transform .15s,box-shadow .15s;height:100%;position:relative;overflow:hidden}
    .kpi-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.08)}
    .kpi-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:14px 0 0 14px}
    .kpi-card.green::before{background:#198754}.kpi-card.blue::before{background:#1a56db}.kpi-card.red::before{background:#dc3545}
    .kpi-card.teal::before{background:#0dcaf0}.kpi-card.gold::before{background:#fd7e14}.kpi-card.purple::before{background:#6f42c1}
    .kpi-label{font-size:.65rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#adb5bd;margin-bottom:6px}
    .kpi-value{font-size:1.7rem;font-weight:800;line-height:1;margin-bottom:2px}
    .kpi-sub{font-size:.72rem;color:#adb5bd}
    .kpi-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;opacity:.08}
    .panel-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    .panel-title{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#adb5bd;display:flex;align-items:center;gap:8px}
    .tank-visor{height:38px;background:#f1f3f5;border-radius:8px;border:1px solid #e9ecef;overflow:hidden;position:relative}
    .tank-level{height:100%;transition:width 1s ease-in-out;background:linear-gradient(90deg,#198754,#20c997)}
    .tank-level.low{background:linear-gradient(90deg,#dc3545,#ff6b6b)}
    .tank-text{position:absolute;width:100%;text-align:center;top:50%;transform:translateY(-50%);font-weight:700;color:#212529;font-size:.72rem}
    .filter-panel{background:#fff;border-radius:14px;border:1px solid #e9ecef;padding:18px 22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    .filter-group{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
    .filter-item{display:flex;flex-direction:column;gap:4px}
    .filter-item label{font-size:.68rem;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:#6c757d}
    .filter-item select,.filter-item input{border:1px solid #dee2e6;border-radius:8px;padding:7px 11px;font-size:.82rem;color:#343a40;background:#f8f9fa;outline:none;transition:border-color .2s,box-shadow .2s;min-width:120px}
    .filter-item select:focus,.filter-item input:focus{border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.1);background:#fff}
    .btn-filter{padding:8px 18px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:6px;transition:all .2s}
    .btn-filter-apply{background:#0d6efd;color:#fff}.btn-filter-apply:hover{background:#0b5ed7}
    .btn-filter-clear{background:#f1f3f5;color:#495057;border:1px solid #dee2e6}.btn-filter-clear:hover{background:#e9ecef}
    .table-card{background:#fff;border-radius:14px;border:1px solid #e9ecef;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    #fuelTable thead tr{background:#f8f9fa}
    #fuelTable thead th{font-size:.67rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#868e96;border-bottom:1px solid #e9ecef;padding:8px 12px;white-space:nowrap}
    #fuelTable tbody td{padding:7px 12px;vertical-align:middle;border-bottom:1px solid #f1f3f5;font-size:.82rem}
    #fuelTable tbody tr:hover{background:#f8fff8}
    #fuelTable tbody tr:last-child td{border-bottom:none}
    .tipo-badge{display:inline-flex;align-items:center;gap:4px;border-radius:6px;padding:2px 8px;font-size:.68rem;font-weight:700;white-space:nowrap}
    .tipo-badge.entrada{background:#e6f4ea;color:#198754;border:1px solid #a3d9b1}
    .tipo-badge.saida{background:#fce8e6;color:#dc3545;border:1px solid #f8b8b8}
    .action-btn{width:28px;height:28px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;font-size:.72rem;border:1px solid;transition:all .15s;text-decoration:none;cursor:pointer;background:transparent}
    .form-label-sm{font-size:.72rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#6c757d;margin-bottom:4px}
    .form-control,.form-select{border-radius:8px;border:1px solid #dee2e6;font-size:.85rem;background:#f8f9fa}
    .form-control:focus,.form-select:focus{border-color:#0d6efd;box-shadow:0 0 0 3px rgba(13,110,253,.1);background:#fff}
    div.dataTables_wrapper div.dataTables_filter input{border-radius:8px;border:1px solid #dee2e6;padding:6px 12px;font-size:.82rem}
    div.dataTables_wrapper div.dataTables_length select{border-radius:8px;border:1px solid #dee2e6;padding:4px 8px;font-size:.82rem}
    .dt-buttons{display:flex;gap:6px}
    .dt-button{border-radius:8px!important;font-size:.78rem!important;font-weight:600!important;padding:5px 14px!important;border:1px solid #dee2e6!important}
    .saldo-section-title{font-size:.68rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#6c757d;display:flex;align-items:center;gap:8px;padding:0 2px;margin-bottom:10px}
    .saldo-section-title::after{content:'';flex:1;height:1px;background:#e9ecef}
    .empresa-card{background:#fff;border-radius:12px;border:1px solid #e9ecef;box-shadow:0 1px 6px rgba(0,0,0,.05);padding:12px 14px;min-width:170px;max-width:210px;flex:1;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s}
    .empresa-card:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.1)}
    .empresa-card.zerada{opacity:.65;border-style:dashed}
    .empresa-card::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:12px 0 0 12px;background:#dc3545}
    .empresa-card.zerada::before{background:#198754}
    .empresa-card-name{font-size:.65rem;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;color:#495057;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-right:28px}
    .empresa-card-litros{font-family:'JetBrains Mono',monospace;font-size:1.35rem;font-weight:700;line-height:1;margin-bottom:2px}
    .empresa-card-litros.deve{color:#dc3545}
    .empresa-card-litros.quite{color:#198754}
    .empresa-card-label{font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#adb5bd;margin-bottom:8px}
    .empresa-card-divider{height:1px;background:#f1f3f5;margin:8px 0}
    .empresa-card-stats{display:flex;gap:10px}
    .empresa-card-stat{display:flex;flex-direction:column;gap:1px}
    .empresa-card-stat-val{font-family:'JetBrains Mono',monospace;font-size:.72rem;font-weight:700;color:#343a40}
    .empresa-card-stat-lbl{font-size:.55rem;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:#adb5bd}
    .empresa-card-progress{height:4px;background:#f1f3f5;border-radius:4px;margin-top:8px;overflow:hidden}
    .empresa-card-progress-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#198754,#20c997);transition:width 1s ease}
    .btn-acertar{position:absolute;right:10px;top:10px;width:24px;height:24px;border-radius:6px;background:#fff0f0;border:1px solid #f8b8b8;color:#dc3545;font-size:.65rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s}
    .btn-acertar:hover{background:#dc3545;color:#fff;border-color:#dc3545;transform:scale(1.1)}
    .empresa-card.zerada .btn-acertar{background:#e6f4ea;border-color:#a3d9b1;color:#198754;pointer-events:none}
    .empresa-card-bar{position:absolute;bottom:0;left:4px;right:0;height:3px;background:#f1f3f5;border-radius:0 0 12px 0}
    .empresa-card-bar-fill{height:100%;background:#dc3545;border-radius:inherit;transition:width 1s ease}
    .empresa-card.zerada .empresa-card-bar-fill{background:#198754}
    .acerto-modal-header{background:linear-gradient(135deg,#1a1a2e,#0d3b2e);padding:18px 20px}
    .acerto-divida-display{background:#fce8e6;border:1px solid #f8b8b8;border-radius:10px;padding:12px 16px;margin-bottom:16px}
    .acerto-slider{width:100%;accent-color:#198754;height:6px;cursor:pointer}
    .acerto-preview{background:#e6f4ea;border:1px solid #a3d9b1;border-radius:8px;padding:10px 14px;margin-top:12px;display:none}
    .fuel-toast{position:fixed;bottom:24px;right:24px;z-index:9999;min-width:280px;border-radius:12px;padding:14px 18px;box-shadow:0 8px 32px rgba(0,0,0,.18);display:flex;align-items:center;gap:10px;font-size:.82rem;font-weight:600;transform:translateY(100px);opacity:0;transition:all .3s cubic-bezier(.34,1.56,.64,1)}
    .fuel-toast.show{transform:translateY(0);opacity:1}
    .fuel-toast.success{background:#fff;border-left:4px solid #198754;color:#1a1a2e}
    .fuel-toast.error{background:#fff;border-left:4px solid #dc3545;color:#1a1a2e}

    /* ── Modal confirmação ── */
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

    @media print{.no-print{display:none!important}}
</style>

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-fuel-pump-fill text-success me-2"></i>Gestão de Combustível</h4>
            <p class="text-muted small mb-0">Controlo de abastecimentos e stock de tanques</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark btn-sm fw-bold px-3" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimir</button>
            <button class="btn btn-success btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEntrada"><i class="bi bi-plus-lg me-1"></i> Atestar Tanque</button>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-3 no-print">
        @foreach($tanques as $i => $tanque)
        @php $colors=['green','blue','teal','gold','purple','red']; $icons=['fuel-pump-fill','droplet-fill','water','archive-fill','database-fill','fuel-pump']; @endphp
        <div class="col-6 col-md-2">
            <div class="kpi-card {{ $colors[$i%6] }}">
                <div class="kpi-label">{{ $tanque->nome }}</div>
                <div class="kpi-value" style="font-size:1.3rem;color:{{ $tanque->percentagem<15?'#dc3545':'#198754' }};">{{ number_format($tanque->percentagem,0) }}%</div>
                <div class="kpi-sub">{{ number_format($tanque->stock_atual,0,',','.') }}L disponíveis</div>
                <i class="bi bi-{{ $icons[$i%6] }} kpi-icon"></i>
                <div style="position:absolute;bottom:0;left:4px;right:0;height:3px;background:#f1f3f5;border-radius:0 0 14px 0;">
                    <div style="height:100%;width:{{ $tanque->percentagem }}%;background:{{ $tanque->percentagem<15?'#dc3545':'#198754' }};border-radius:inherit;transition:width 1s;"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- SALDOS POR EMPRESA --}}
    @if(count($saldosEmpresas) > 0)
    <div class="mb-3 no-print">
        <div class="saldo-section-title">
            <i class="bi bi-building text-danger"></i>
            Saldo Devedor por Empresa
            <span style="font-size:.6rem;color:#adb5bd;font-weight:400;text-transform:none;letter-spacing:0;">— clique em ↺ para registar devolução</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @php $maxDivida = collect($saldosEmpresas)->max('saidas') ?: 1; @endphp
            @foreach($saldosEmpresas as $empresa => $dados)
            @php
                $zerada    = $dados['divida'] == 0;
                $pctAcerto = $dados['saidas'] > 0 ? min(100, ($dados['acertado'] / $dados['saidas']) * 100) : 0;
                $pctBar    = min(100, ($dados['saidas'] / $maxDivida) * 100);
            @endphp
            <div class="empresa-card {{ $zerada ? 'zerada' : '' }}">
                <button class="btn-acertar"
                    @if(!$zerada) onclick="abrirModalAcerto('{{ addslashes($empresa) }}', {{ $dados['divida'] }}, {{ json_encode($tanques) }})" @endif
                    title="{{ $zerada ? 'Dívida liquidada' : 'Registar devolução' }}"
                    {{ $zerada ? 'disabled' : '' }}>
                    <i class="bi bi-{{ $zerada ? 'check-lg' : 'arrow-counterclockwise' }}"></i>
                </button>
                <div class="empresa-card-name">{{ $empresa }}</div>
                <div class="empresa-card-litros {{ $zerada ? 'quite' : 'deve' }}">
                    {{ $zerada ? '0L' : number_format($dados['divida'],0,',','.').'L' }}
                </div>
                <div class="empresa-card-label">{{ $zerada ? '✓ quite' : 'em dívida' }}</div>
                @if($dados['acertado'] > 0)
                <div class="empresa-card-progress">
                    <div class="empresa-card-progress-fill" style="width:{{ $pctAcerto }}%;"></div>
                </div>
                <div style="font-size:.58rem;color:#198754;margin-top:3px;font-weight:600;">
                    {{ number_format($dados['acertado'],0,',','.') }}L já devolvidos ({{ number_format($pctAcerto,0) }}%)
                </div>
                @endif
                <div class="empresa-card-divider"></div>
                <div class="empresa-card-stats">
                    <div class="empresa-card-stat">
                        <div class="empresa-card-stat-val">{{ number_format($dados['saidas'],0,',','.') }}L</div>
                        <div class="empresa-card-stat-lbl">Total retirado</div>
                    </div>
                    <div class="empresa-card-stat">
                        <div class="empresa-card-stat-val">{{ $dados['movimentos'] }}x</div>
                        <div class="empresa-card-stat-lbl">Abastec.</div>
                    </div>
                </div>
                <div class="empresa-card-bar">
                    <div class="empresa-card-bar-fill" style="width:{{ $pctBar }}%;"></div>
                </div>
            </div>
            @endforeach

            @php $totalDivida = collect($saldosEmpresas)->sum('divida'); $totalAcertado = collect($saldosEmpresas)->sum('acertado'); @endphp
            <div style="background:linear-gradient(135deg,#1a1a2e,#0d3b2e);border-radius:12px;padding:12px 14px;min-width:150px;max-width:180px;flex:1;display:flex;flex-direction:column;justify-content:center;position:relative;overflow:hidden;">
                <div style="position:absolute;right:-10px;bottom:-10px;font-size:3rem;opacity:.06;color:#fff;">⛽</div>
                <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:6px;">Dívida Total</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:1.4rem;font-weight:700;color:#fff;line-height:1;">{{ number_format($totalDivida,0,',','.') }}L</div>
                <div style="font-size:.6rem;color:rgba(255,255,255,.4);margin-top:2px;">{{ count($saldosEmpresas) }} empresa(s)</div>
                <div style="height:1px;background:rgba(255,255,255,.1);margin:8px 0;"></div>
                @if($totalAcertado > 0)
                <div style="font-size:.6rem;color:#62c48e;font-weight:600;">✓ {{ number_format($totalAcertado,0,',','.') }}L já devolvidos</div>
                @endif
                <div style="font-size:.6rem;color:rgba(255,255,255,.5);margin-top:4px;">Stock actual:</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:.85rem;font-weight:700;color:#62c48e;">{{ number_format($tanques->sum('stock_atual'),0,',','.') }}L</div>
            </div>
        </div>
    </div>
    @endif

    {{-- PAINEL PRINCIPAL --}}
    <div class="row g-3 mb-3 no-print">
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
                                <option value="Fem">
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
        <div class="col-lg-5">
            <div class="panel-card p-3 h-100">
                <div class="panel-title mb-3"><i class="bi bi-bar-chart-fill" style="color:#0dcaf0;"></i> Níveis de Stock</div>
                <div style="overflow-y:auto;max-height:260px;">
                    @foreach($tanques as $tanque)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold" style="font-size:.78rem;">{{ $tanque->nome }}</span>
                            <span style="font-size:.72rem;font-weight:700;color:{{ $tanque->percentagem<15?'#dc3545':'#198754' }};">{{ number_format($tanque->percentagem,1) }}%</span>
                        </div>
                        <div class="tank-visor">
                            <div class="tank-level {{ $tanque->percentagem<15?'low':'' }}" style="width:{{ $tanque->percentagem }}%;"></div>
                            <div class="tank-text">{{ number_format($tanque->stock_atual,0,',','.') }}L / {{ number_format($tanque->capacidade,0,',','.') }}L</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICO --}}
    <div class="panel-card p-4 mb-3 no-print">
        <div class="panel-title mb-3"><i class="bi bi-graph-up text-primary"></i> Consumo Diário por Empresa (L)</div>
        <div style="height:260px;position:relative;"><canvas id="consumptionChart"></canvas></div>
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
                        <option value="{{ $t->id }}" {{ request('filter_tank_id')==$t->id?'selected':'' }}>{{ $t->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn-filter btn-filter-apply"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="{{ route('fuel.index') }}" class="btn-filter btn-filter-clear"><i class="bi bi-x-lg"></i> Limpar</a>
                </div>
            </div>
        </form>
        @if(request('from_date') || request('company') || request('filter_tank_id'))
        <div class="d-flex align-items-center gap-3 mt-3 pt-3 border-top">
            <span style="font-size:.72rem;color:#6c757d;font-weight:600;">RESUMO:</span>
            <span style="font-size:.72rem;">{{ $contagemRegistos }} registos</span>
            <span class="tipo-badge saida"><i class="bi bi-arrow-down-circle-fill"></i> Saídas: {{ number_format($totalConsumidoFiltro,0) }}L</span>
            <span class="tipo-badge entrada"><i class="bi bi-arrow-up-circle-fill"></i> Entradas: {{ number_format($totalEntradaFiltro,0) }}L</span>
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
                        <th class="text-center">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($historico as $item)
                <tr>
                    <td><span class="fw-bold text-dark" style="font-size:.8rem;">{{ date('d/m/Y', strtotime($item->date)) }}</span></td>
                    <td>
                        <span class="tipo-badge {{ $item->tipo == 'ENTRADA' ? 'entrada' : 'saida' }}">
                            <i class="bi bi-{{ $item->tipo == 'ENTRADA' ? 'arrow-up-circle-fill' : 'arrow-down-circle-fill' }}"></i>
                            {{ $item->tipo }}
                        </span>
                    </td>
                    <td><span class="fw-semibold" style="font-size:.8rem;">{{ $item->tanque_nome }}</span></td>
                    <td>
                        <span style="font-size:.8rem;font-weight:600;">{{ $item->ident }}</span><br>
                        <span class="text-muted" style="font-size:.7rem;">{{ $item->company }}</span>
                    </td>
                    <td class="text-center" style="font-size:.78rem;">
                        @if($item->start_counter !== null)
                            <span class="badge bg-light text-dark border" style="font-size:.68rem;font-weight:600;">
                                {{ number_format($item->start_counter, 0) }} → {{ number_format($item->end_counter, 0) }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <span class="fw-bold" style="font-size:.85rem;color:{{ $item->tipo == 'ENTRADA' ? '#198754' : '#dc3545' }};">
                            {{ $item->tipo == 'ENTRADA' ? '+' : '-' }}{{ number_format($item->quantity, 0) }}L
                        </span>
                    </td>
                    <td>
                        <span style="font-size:.75rem;">
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
                                data-label="{{ $item->tipo }} — {{ $item->ident }} ({{ date('d/m/Y', strtotime($item->date)) }})"
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
</div>

{{-- MODAL CONFIRMAÇÃO ELIMINAR --}}
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-header">
            <div class="confirm-icon"><i class="bi bi-trash3-fill"></i></div>
            <div class="confirm-title">Eliminar registo?</div>
            <div class="confirm-sub">Tens a certeza que queres eliminar<br><strong id="confirmLabel"></strong>?</div>
        </div>
        <div class="confirm-body">
            <div class="confirm-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Esta acção afecta o stock do tanque e não pode ser desfeita.
            </div>
            <div class="confirm-actions">
                <button class="btn-cancel-confirm" onclick="closeConfirm()">
                    <i class="bi bi-x-lg"></i> Cancelar
                </button>
                <button class="btn-delete-confirm" id="confirmOkBtn">
                    <i class="bi bi-trash3"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Registar Devolução --}}
<div class="modal fade" id="modalAcerto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header acerto-modal-header border-0">
                <div>
                    <h6 class="modal-title fw-bold text-white mb-0"><i class="bi bi-arrow-counterclockwise me-2"></i>Registar Devolução</h6>
                    <div id="acerto-empresa-sub" style="font-size:.65rem;color:rgba(255,255,255,.5);letter-spacing:1px;text-transform:uppercase;margin-top:2px;"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="acerto-divida-display">
                    <div style="font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#adb5bd;margin-bottom:4px;">Dívida Actual</div>
                    <div style="display:flex;align-items:baseline;gap:6px;">
                        <span id="acerto-divida-valor" style="font-family:'JetBrains Mono',monospace;font-size:1.6rem;font-weight:700;color:#dc3545;"></span>
                        <span style="font-size:.75rem;color:#adb5bd;">litros</span>
                    </div>
                </div>
                <input type="hidden" id="acerto-company-input">
                <div class="mb-3">
                    <label class="form-label-sm">Tanque de Destino <span style="color:#dc3545;">*</span></label>
                    <select id="acerto-tank-select" class="form-select" required></select>
                    <div style="font-size:.65rem;color:#6c757d;margin-top:4px;"><i class="bi bi-info-circle me-1"></i>O combustível devolvido entra fisicamente neste tanque</div>
                </div>
                <div class="mb-3">
                    <label class="form-label-sm">Data da Devolução <span style="color:#dc3545;">*</span></label>
                    <input type="date" id="acerto-date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label-sm">Quantidade a Devolver (L) <span style="color:#dc3545;">*</span></label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="number" id="acerto-qty-input" class="form-control" placeholder="0" min="1" style="max-width:110px;" oninput="syncSlider()">
                        <input type="range" id="acerto-qty-slider" class="acerto-slider flex-1" min="1" max="100" value="0" oninput="syncInput()">
                    </div>
                    <div style="display:flex;gap:6px;margin-top:8px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:.7rem;border-radius:6px;" onclick="setAcertoPct(25)">25%</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:.7rem;border-radius:6px;" onclick="setAcertoPct(50)">50%</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:.7rem;border-radius:6px;" onclick="setAcertoPct(75)">75%</button>
                        <button type="button" class="btn btn-sm btn-success" style="font-size:.7rem;border-radius:6px;" onclick="setAcertoPct(100)">Zerar Dívida</button>
                    </div>
                </div>
                <div class="acerto-preview" id="acerto-preview">
                    <div style="font-size:.62rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#198754;margin-bottom:4px;">Saldo após devolução</div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <span id="preview-restante" style="font-family:'JetBrains Mono',monospace;font-size:1.1rem;font-weight:700;color:#198754;"></span>
                            <span style="font-size:.72rem;color:#6c757d;"> restantes</span>
                        </div>
                        <div id="preview-quite-badge" style="display:none;background:#198754;color:#fff;border-radius:6px;padding:3px 10px;font-size:.65rem;font-weight:700;letter-spacing:1px;">✓ QUITE</div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label-sm">Observação (opcional)</label>
                    <input type="text" id="acerto-notes" class="form-control" placeholder="Ex: Devolução referente a Março...">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-light btn-sm px-4" style="border-radius:8px;" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success fw-bold px-4" style="border-radius:8px;" onclick="submeterAcerto()">
                    <i class="bi bi-check-circle me-1"></i> Confirmar Devolução
                </button>
            </div>
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
                    <button type="submit" class="btn btn-success fw-bold px-4" style="border-radius:8px;"><i class="bi bi-check-circle me-1"></i> Confirmar Entrada</button>
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
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:8px;"><i class="bi bi-check-circle me-1"></i> Guardar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="fuelToast" class="fuel-toast">
    <i id="fuelToastIcon" class="bi bi-check-circle-fill" style="font-size:1.1rem;"></i>
    <span id="fuelToastMsg"></span>
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
let _deleteUrl = null;
let _deleteRow = null;

$(document).ready(function(){
    $('#fuelTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
        buttons: [
            { extend:'excel', text:'<i class="bi bi-file-earmark-excel me-1"></i> Excel', className:'btn btn-sm', title:'Relatorio_Combustivel_{{ date("Ymd") }}' },
            { extend:'pdf',   text:'<i class="bi bi-file-earmark-pdf me-1"></i> PDF',     className:'btn btn-sm', orientation:'landscape', pageSize:'A4' }
        ],
        order: [[0,'desc']],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json' },
        pageLength: 20,
        columnDefs: [{ orderable: false, targets: 7 }]
    });

    // ── Editar ──
    $(document).on('click', '.btn-edit', function(){
        const id   = $(this).data('id');
        const tipo = $(this).data('tipo');
        if (tipo === 'SAÍDA') {
            $.get(`/fuel-log/${id}/json`, function(data){
                $('#edit_log_id').val(data.id);
                $('#edit_plate').val(data.plate);
                $('#edit_start').val(data.start_counter);
                $('#edit_end').val(data.end_counter);
                $('#edit_company').val(data.company);
                new bootstrap.Modal(document.getElementById('editLogModal')).show();
            }).fail(() => showToast('Erro ao buscar dados.', 'error'));
        } else {
            $.get(`/fuel-entry/${id}/json`, function(data){
                $('#modalEntrada form').attr('action', `/fuel-entry/${id}`);
                if (!$('#modalEntrada input[name="_method"]').length)
                    $('#modalEntrada form').append('<input type="hidden" name="_method" value="PUT">');
                $('#modalEntrada select[name="tank_id"]').val(data.tank_id);
                $('#modalEntrada input[name="date"]').val(data.date.split(' ')[0]);
                $('#modalEntrada input[name="quantity"]').val(data.quantity);
                $('#modalEntrada input[name="supplier"]').val(data.supplier || data.ident);
                new bootstrap.Modal(document.getElementById('modalEntrada')).show();
            }).fail(() => showToast('Erro ao buscar dados.', 'error'));
        }
    });

    // ── Abrir modal de confirmação ──
    $(document).on('click', '.btn-delete', function(){
        _deleteUrl = $(this).data('url');
        _deleteRow = $(this).closest('tr');
        $('#confirmLabel').text($(this).data('label'));
        $('#confirmOkBtn').prop('disabled', false)
            .html('<i class="bi bi-trash3"></i> Eliminar');
        $('#confirmOverlay').addClass('open');
    });
});

// ── Confirmar eliminação ──
$('#confirmOkBtn').on('click', function(){
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> A eliminar...';

    $.ajax({
        url: _deleteUrl,
        type: 'POST',
        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
        success: function(){
            closeConfirm();
            _deleteRow.fadeOut(300, function(){ $(this).remove(); });
            showToast('Registo eliminado com sucesso.', 'success');
        },
        error: function(xhr){
            closeConfirm();
            if (xhr.status === 200) {
                _deleteRow.fadeOut(300, function(){ $(this).remove(); });
                showToast('Registo eliminado.', 'success');
            } else {
                showToast('Erro ao eliminar. Tenta novamente.', 'error');
            }
        }
    });
});

$('#confirmOverlay').on('click', function(e){
    if (e.target === this) closeConfirm();
});

$(document).on('keydown', function(e){
    if (e.key === 'Escape') closeConfirm();
});

function closeConfirm(){
    $('#confirmOverlay').removeClass('open');
}

function calc(){
    const i = parseFloat(document.getElementById('ci').value) || 0;
    const f = parseFloat(document.getElementById('cf').value) || 0;
    document.getElementById('qty').value = f - i > 0 ? f - i : 0;
}

function showToast(msg, type = 'success'){
    const t = document.getElementById('fuelToast');
    document.getElementById('fuelToastMsg').textContent = msg;
    document.getElementById('fuelToastIcon').className = 'bi bi-' + (type === 'success' ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-danger');
    t.className = 'fuel-toast ' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 4000);
}

let _acertoDivida = 0;

function abrirModalAcerto(empresa, divida, tanques){
    _acertoDivida = divida;
    document.getElementById('acerto-empresa-sub').textContent = empresa;
    document.getElementById('acerto-company-input').value = empresa;
    document.getElementById('acerto-divida-valor').textContent = Number(divida).toLocaleString('pt-PT') + 'L';
    document.getElementById('acerto-qty-input').value = '';
    document.getElementById('acerto-qty-input').max = divida;
    document.getElementById('acerto-qty-slider').max = divida;
    document.getElementById('acerto-qty-slider').value = 0;
    document.getElementById('acerto-preview').style.display = 'none';
    document.getElementById('acerto-notes').value = '';
    const sel = document.getElementById('acerto-tank-select');
    sel.innerHTML = '';
    tanques.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.id;
        opt.textContent = t.nome + ' (' + Number(t.stock_atual).toLocaleString('pt-PT') + 'L)';
        sel.appendChild(opt);
    });
    new bootstrap.Modal(document.getElementById('modalAcerto')).show();
}

function syncSlider(){ const v = parseFloat(document.getElementById('acerto-qty-input').value) || 0; document.getElementById('acerto-qty-slider').value = Math.min(v, _acertoDivida); updatePreview(v); }
function syncInput(){  const v = parseFloat(document.getElementById('acerto-qty-slider').value) || 0; document.getElementById('acerto-qty-input').value = v; updatePreview(v); }
function setAcertoPct(pct){ const v = Math.round(_acertoDivida * pct / 100); document.getElementById('acerto-qty-input').value = v; document.getElementById('acerto-qty-slider').value = v; updatePreview(v); }

function updatePreview(v){
    const preview = document.getElementById('acerto-preview');
    if (v > 0) {
        const restante = Math.max(0, _acertoDivida - v);
        document.getElementById('preview-restante').textContent = Number(restante).toLocaleString('pt-PT') + 'L';
        document.getElementById('preview-quite-badge').style.display = restante === 0 ? 'block' : 'none';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function submeterAcerto(){
    const empresa = document.getElementById('acerto-company-input').value;
    const tankId  = document.getElementById('acerto-tank-select').value;
    const date    = document.getElementById('acerto-date').value;
    const qty     = parseFloat(document.getElementById('acerto-qty-input').value);
    const notes   = document.getElementById('acerto-notes').value;
    if (!qty || qty < 1)         { showToast('Indique a quantidade a devolver.', 'error'); return; }
    if (qty > _acertoDivida)     { showToast('Não pode devolver mais do que a dívida actual.', 'error'); return; }
    $.ajax({
        url: '{{ route("fuel.settlement.store") }}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', company: empresa, tank_id: tankId, date: date, quantity: qty, notes: notes },
        success: function(res){
            bootstrap.Modal.getInstance(document.getElementById('modalAcerto')).hide();
            showToast(res.message, 'success');
            setTimeout(() => window.location.reload(), 1500);
        },
        error: function(xhr){ showToast(xhr.responseJSON?.message || 'Erro ao registar devolução.', 'error'); }
    });
}

document.addEventListener('DOMContentLoaded', function(){
    const ctx = document.getElementById('consumptionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels:   {!! json_encode($labelsCompostas ?? []) !!},
            datasets: {!! json_encode($datasets ?? []) !!}
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { font: { size: 10 }, boxWidth: 10 } }, tooltip: { mode: 'index', intersect: false } },
            scales: { x: { stacked: true, ticks: { font: { size: 9 }, maxRotation: 45 } }, y: { stacked: true, beginAtZero: true, ticks: { font: { size: 10 } } } }
        }
    });
});
</script>

</x-app-layout>