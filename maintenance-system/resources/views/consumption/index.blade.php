<x-app-layout>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
.consumption-page, .consumption-page * { font-family: 'DM Sans', sans-serif; }
.mono { font-family: 'DM Mono', monospace; }
.consumption-page { background: #f4f5f7; min-height: 100vh; padding: 28px; }
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
.page-header-left h4 { font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -.3px; }
.page-header-left p { font-size: .78rem; color: #94a3b8; margin: 0; }
.header-badge { background: #0f172a; color: #fff; border-radius: 8px; padding: 6px 14px; font-size: .72rem; font-weight: 700; letter-spacing: .5px; display: inline-flex; align-items: center; gap: 6px; }
.header-badge span { background: #c60a1a; border-radius: 4px; padding: 1px 6px; font-size: .65rem; }
.period-bar { background: #fff; border-radius: 12px; border: 1px solid #e9ecef; padding: 14px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; box-shadow: 0 1px 6px rgba(0,0,0,.04); }
.period-bar label { font-size: .68rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 0; white-space: nowrap; }
.period-bar input, .period-bar select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 12px; font-size: .8rem; color: #1e293b; background: #f8fafc; outline: none; transition: border-color .2s; font-family: 'DM Sans', sans-serif; }
.period-bar input:focus, .period-bar select:focus { border-color: #c60a1a; box-shadow: 0 0 0 3px rgba(198,10,26,.08); background: #fff; }
.btn-apply { background: #c60a1a; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; font-size: .78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background .2s; font-family: 'DM Sans', sans-serif; }
.btn-apply:hover { background: #a80816; }
.btn-clear { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; font-size: .78rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background .2s; font-family: 'DM Sans', sans-serif; }
.btn-clear:hover { background: #e2e8f0; }
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width:900px){ .kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:500px){ .kpi-grid { grid-template-columns: 1fr; } }
.kpi-card { background: #fff; border-radius: 14px; border: 1px solid #e9ecef; padding: 18px 20px; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.04); transition: transform .15s, box-shadow .15s; }
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.kpi-card::after { content: ''; position: absolute; bottom: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: .06; }
.kpi-card.red::after   { background: #c60a1a; }
.kpi-card.blue::after  { background: #2563eb; }
.kpi-card.amber::after { background: #d97706; }
.kpi-card.teal::after  { background: #0891b2; }
.kpi-accent { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .95rem; margin-bottom: 12px; }
.kpi-card.red .kpi-accent   { background: #fef2f2; color: #c60a1a; }
.kpi-card.blue .kpi-accent  { background: #eff6ff; color: #2563eb; }
.kpi-card.amber .kpi-accent { background: #fffbeb; color: #d97706; }
.kpi-card.teal .kpi-accent  { background: #ecfeff; color: #0891b2; }
.kpi-label { font-size: .65rem; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; }
.kpi-value { font-size: 1.9rem; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 4px; letter-spacing: -1px; }
.kpi-sub   { font-size: .7rem; color: #94a3b8; }
.analysis-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
@media(max-width:860px){ .analysis-grid { grid-template-columns: 1fr; } }
.panel { background: #fff; border-radius: 14px; border: 1px solid #e9ecef; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.04); }
.panel-header { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
.panel-title { font-size: .75rem; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.panel-title i { color: #c60a1a; }
.panel-badge { background: #f1f5f9; color: #64748b; border-radius: 6px; padding: 2px 8px; font-size: .65rem; font-weight: 700; }
.panel-body  { padding: 16px 20px; }
.rank-item { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.rank-item:last-child { margin-bottom: 0; }
.rank-num { width: 24px; height: 24px; border-radius: 6px; background: #f1f5f9; color: #64748b; font-size: .65rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rank-num.top { background: #0f172a; color: #fff; }
.rank-info { flex: 1; min-width: 0; }
.rank-name { font-size: .8rem; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
.rank-bar-wrap { height: 5px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
.rank-bar { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #c60a1a, #e85d5d); transition: width 1s cubic-bezier(.4,0,.2,1); }
.rank-qty { font-size: .75rem; font-weight: 800; color: #0f172a; white-space: nowrap; flex-shrink: 0; font-family: 'DM Mono', monospace; }
.equip-item { display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 10px; margin-bottom: 8px; border: 1px solid #f1f5f9; transition: background .15s; }
.equip-item:hover { background: #f8fafc; }
.equip-item:last-child { margin-bottom: 0; }
.equip-avatar { width: 38px; height: 38px; border-radius: 10px; background: #fef2f2; color: #c60a1a; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; font-weight: 700; }
.equip-name { font-size: .8rem; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
.equip-meta { font-size: .68rem; color: #94a3b8; }
.equip-count { margin-left: auto; text-align: right; }
.equip-count-num { font-size: 1.05rem; font-weight: 800; color: #c60a1a; font-family: 'DM Mono', monospace; display: block; }
.equip-count-lbl { font-size: .62rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.heat-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 6px; padding: 4px 0; }
.heat-cell { aspect-ratio: 1; border-radius: 6px; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: .6rem; font-weight: 700; cursor: default; transition: transform .1s; }
.heat-cell:hover { transform: scale(1.1); z-index: 2; }
.heat-cell .heat-month { color: #94a3b8; font-size: .58rem; letter-spacing: .5px; text-transform: uppercase; }
.heat-cell .heat-val { color: #0f172a; font-size: .78rem; font-family: 'DM Mono', monospace; }
.heat-cell.l0 { background: #f1f5f9; }
.heat-cell.l1 { background: #fee2e2; }
.heat-cell.l2 { background: #fca5a5; }
.heat-cell.l3 { background: #f87171; }
.heat-cell.l4 { background: #c60a1a; }
.heat-cell.l4 .heat-month, .heat-cell.l4 .heat-val,
.heat-cell.l3 .heat-month, .heat-cell.l3 .heat-val { color: #fff; }
.table-panel { background: #fff; border-radius: 14px; border: 1px solid #e9ecef; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.04); }
#movTable thead th { font-size: .64rem; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e9ecef; padding: 10px 14px; white-space: nowrap; }
#movTable tbody td { padding: 10px 14px; font-size: .8rem; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
#movTable tbody tr:hover td { background: #f8fafc; }
#movTable tbody tr:last-child td { border-bottom: none; }
.item-chip { display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e9ecef; border-radius: 6px; padding: 3px 8px; font-size: .75rem; font-weight: 600; color: #1e293b; }
.qty-badge { background: #fef2f2; color: #c60a1a; border-radius: 5px; padding: 2px 7px; font-size: .72rem; font-weight: 800; font-family: 'DM Mono', monospace; }
.mnt-link { font-size: .75rem; font-weight: 700; color: #2563eb; text-decoration: none; }
.mnt-link:hover { text-decoration: underline; }
div.dataTables_wrapper div.dataTables_filter input,
div.dataTables_wrapper div.dataTables_length select { border-radius: 8px; border: 1px solid #e2e8f0; padding: 6px 10px; font-size: .78rem; font-family: 'DM Sans', sans-serif; }
@keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.kpi-card, .panel, .table-panel { animation: fadeUp .4s ease both; }
.kpi-card:nth-child(1){ animation-delay:.05s }
.kpi-card:nth-child(2){ animation-delay:.10s }
.kpi-card:nth-child(3){ animation-delay:.15s }
.kpi-card:nth-child(4){ animation-delay:.20s }
</style>

<div class="consumption-page">

    {{-- HEADER --}}
    <div class="page-header">
        <div class="page-header-left">
            <h4><i class="bi bi-bar-chart-line-fill" style="color:#c60a1a;margin-right:8px;"></i>Centro de Consumo</h4>
            <p>Rastreio de materiais consumidos em manutenções · Stock de saída por equipamento</p>
        </div>
        <div class="header-badge">
            <i class="bi bi-calendar3"></i>
            {{ now()->format('Y') }}
            <span>LIVE</span>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="period-bar">
        <div class="d-flex flex-column gap-1">
            <label>De</label>
            <input type="date" id="filterFrom" value="{{ now()->startOfYear()->format('Y-m-d') }}">
        </div>
        <div class="d-flex flex-column gap-1">
            <label>Até</label>
            <input type="date" id="filterTo" value="{{ now()->format('Y-m-d') }}">
        </div>
        <div class="d-flex flex-column gap-1">
            <label>Equipamento</label>
            <select id="filterMachine">
                <option value="">Todos</option>
                @foreach($machines as $m)
                <option value="{{ $m->id }}">{{ $m->numero_interno }} — {{ $m->nome }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-column gap-1">
            <label>Item de Stock</label>
            <select id="filterItem">
                <option value="">Todos</option>
                @foreach($stockItems as $si)
                <option value="{{ $si->id }}">{{ $si->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex gap-2 align-items-end ms-auto">
            <button class="btn-apply" onclick="applyFilters()"><i class="bi bi-search"></i> Aplicar</button>
            <button class="btn-clear" onclick="clearFilters()"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card red">
            <div class="kpi-accent"><i class="bi bi-box-seam"></i></div>
            <div class="kpi-label">Total Saídas</div>
            <div class="kpi-value mono">{{ number_format($totalUnits) }}</div>
            <div class="kpi-sub">unidades consumidas</div>
        </div>
        <div class="kpi-card blue">
            <div class="kpi-accent"><i class="bi bi-arrow-left-right"></i></div>
            <div class="kpi-label">Movimentos</div>
            <div class="kpi-value mono">{{ $totalMovs }}</div>
            <div class="kpi-sub">registos de saída</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-accent"><i class="bi bi-star-fill"></i></div>
            <div class="kpi-label">Item Mais Usado</div>
            <div class="kpi-value" style="font-size:1rem;padding-top:4px;line-height:1.3;">{{ $topItemName }}</div>
            <div class="kpi-sub">{{ number_format($topItemQty) }} unidades</div>
        </div>
        <div class="kpi-card teal">
            <div class="kpi-accent"><i class="bi bi-gear-fill"></i></div>
            <div class="kpi-label">Equip. Mais Gastador</div>
            <div class="kpi-value" style="font-size:1.1rem;padding-top:4px;">{{ $topMachName }}</div>
            <div class="kpi-sub">{{ number_format($topMachQty) }} unidades consumidas</div>
        </div>
    </div>

    {{-- RANKINGS --}}
    <div class="analysis-grid">

        {{-- Top Itens --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-trophy-fill"></i> Top Itens Consumidos</div>
                <span class="panel-badge">Top 8</span>
            </div>
            <div class="panel-body">
                @foreach($itemRanking as $i => $item)
                <div class="rank-item">
                    <div class="rank-num {{ $i < 3 ? 'top' : '' }}">{{ $i + 1 }}</div>
                    <div class="rank-info">
                        <div class="rank-name">{{ $item['name'] }}</div>
                        <div class="rank-bar-wrap">
                            <div class="rank-bar" style="width:{{ $maxItemQty > 0 ? round(($item['qty']/$maxItemQty)*100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="rank-qty">{{ number_format($item['qty']) }}</div>
                </div>
                @endforeach
                @if($itemRanking->isEmpty())
                <p class="text-muted text-center small mt-3">Sem dados de consumo registados.</p>
                @endif
            </div>
        </div>

        {{-- Top Equipamentos --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><i class="bi bi-gear-wide-connected"></i> Equipamentos Mais Gastadores</div>
                <span class="panel-badge">Top 6</span>
            </div>
            <div class="panel-body">
                @foreach($machRanking as $mach)
                <div class="equip-item">
                    <div class="equip-avatar">{{ strtoupper(substr($mach['num'], 0, 2)) }}</div>
                    <div>
                        <div class="equip-name">{{ $mach['num'] }}</div>
                        <div class="equip-meta">{{ Str::limit($mach['nome'], 32) }} · {{ $mach['movs'] }} mov.</div>
                    </div>
                    <div class="equip-count">
                        <span class="equip-count-num">{{ number_format($mach['qty']) }}</span>
                        <span class="equip-count-lbl">unid.</span>
                    </div>
                </div>
                @endforeach
                @if($machRanking->isEmpty())
                <p class="text-muted text-center small mt-3">Sem dados de equipamentos registados.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- HEATMAP MENSAL --}}
    <div class="panel" style="margin-bottom:24px;">
        <div class="panel-header">
            <div class="panel-title"><i class="bi bi-grid-3x3"></i> Consumo Mensal — {{ now()->year }}</div>
            <span class="panel-badge">Intensidade de saídas</span>
        </div>
        <div class="panel-body">
            <div class="heat-grid">
                @foreach($monthlyQty as $m => $qty)
                @php
                    $pct   = $maxMonthly > 0 ? $qty / $maxMonthly : 0;
                    $level = $pct > .75 ? 'l4' : ($pct > .5 ? 'l3' : ($pct > .2 ? 'l2' : ($qty > 0 ? 'l1' : 'l0')));
                @endphp
                <div class="heat-cell {{ $level }}" title="{{ $months[$m-1] }}: {{ $qty }} unidades">
                    <span class="heat-month">{{ $months[$m-1] }}</span>
                    <span class="heat-val">{{ $qty > 999 ? round($qty/1000,1).'k' : $qty }}</span>
                </div>
                @endforeach
            </div>
            <div class="d-flex align-items-center gap-3 mt-3">
                <span style="font-size:.65rem;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:1px;">Intensidade:</span>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:12px;height:12px;border-radius:3px;background:#f1f5f9;"></div>
                    <span style="font-size:.65rem;color:#64748b;">Sem saída</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:12px;height:12px;border-radius:3px;background:#fee2e2;"></div>
                    <span style="font-size:.65rem;color:#64748b;">Baixo</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:12px;height:12px;border-radius:3px;background:#fca5a5;"></div>
                    <span style="font-size:.65rem;color:#64748b;">Médio</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:12px;height:12px;border-radius:3px;background:#f87171;"></div>
                    <span style="font-size:.65rem;color:#64748b;">Alto</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div style="width:12px;height:12px;border-radius:3px;background:#c60a1a;"></div>
                    <span style="font-size:.65rem;color:#64748b;">Crítico</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA DE MOVIMENTOS --}}
    <div class="table-panel">
        <div class="panel-header" style="padding:16px 20px;">
            <div class="panel-title"><i class="bi bi-table"></i> Todos os Movimentos de Stock</div>
            <span class="panel-badge">{{ $totalMovs }} registos</span>
        </div>
        <div class="p-3">
            <table class="table table-hover mb-0" id="movTable" style="width:100%">
                <thead>
                    <tr>
                        <th>DATA</th>
                        <th>ITEM</th>
                        <th>QTD</th>
                        <th>EQUIPAMENTO</th>
                        <th>MANUTENÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($movs->sortByDesc('created_at') as $mov)
                <tr data-machine="{{ $mov->machine_id }}"
                    data-item="{{ $mov->stock_item_id }}"
                    data-date="{{ optional($mov->created_at)->format('Y-m-d') }}">
                    <td>
                        <span style="font-size:.75rem;color:#64748b;">
                            {{ optional($mov->created_at)->format('d/m/Y') ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <span class="item-chip">
                            <i class="bi bi-box-seam" style="color:#c60a1a;font-size:.7rem;"></i>
                            {{ optional($mov->stockItem)->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td><span class="qty-badge">{{ $mov->quantity }}</span></td>
                    <td>
                        @if($mov->machine)
                            <span style="font-size:.78rem;font-weight:600;color:#1e293b;">{{ $mov->machine->numero_interno }}</span>
                            <span style="font-size:.68rem;color:#94a3b8;"> · {{ Str::limit($mov->machine->nome, 20) }}</span>
                        @else
                            <span class="text-muted" style="font-size:.75rem;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($mov->maintenance_id)
                            <a href="{{ route('maintenances.show', $mov->maintenance_id) }}" class="mnt-link">
                                <i class="bi bi-tools me-1"></i>#{{ $mov->maintenance_id }}
                            </a>
                        @else
                            <span class="text-muted" style="font-size:.75rem;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let movDT;

$(document).ready(function () {
    movDT = $('#movTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[0, 'desc']],
        pageLength: 15,
    });

    setTimeout(() => {
        document.querySelectorAll('.rank-bar').forEach(b => {
            const w = b.style.width;
            b.style.width = '0';
            requestAnimationFrame(() => { b.style.width = w; });
        });
    }, 200);
});

function applyFilters() {
    const from    = $('#filterFrom').val();
    const to      = $('#filterTo').val();
    const machine = $('#filterMachine').val();
    const item    = $('#filterItem').val();

    $('#movTable tbody tr').each(function () {
        const $row    = $(this);
        const rowDate = $row.data('date') || '';
        const rowMach = String($row.data('machine') || '');
        const rowItem = String($row.data('item') || '');

        let show = true;
        if (from    && rowDate < from)        show = false;
        if (to      && rowDate > to)          show = false;
        if (machine && rowMach !== machine)   show = false;
        if (item    && rowItem !== item)      show = false;

        $row.toggle(show);
    });

    movDT.draw();
}

function clearFilters() {
    $('#filterFrom').val('{{ now()->startOfYear()->format("Y-m-d") }}');
    $('#filterTo').val('{{ now()->format("Y-m-d") }}');
    $('#filterMachine').val('');
    $('#filterItem').val('');
    $('#movTable tbody tr').show();
    movDT.draw();
}
</script>

</x-app-layout>