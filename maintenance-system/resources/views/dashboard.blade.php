<x-app-layout>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>
    /* ── KPI cards ── */
    .kpi-card{background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
    .kpi-card.blue  {border-left:4px solid #1a56db}
    .kpi-card.yellow{border-left:4px solid #f59e0b}
    .kpi-card.red   {border-left:4px solid #dc2626}
    .kpi-card.green {border-left:4px solid #16a34a}
    .kpi-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
    .kpi-icon.blue  {background:#eff6ff;color:#1a56db}
    .kpi-icon.yellow{background:#fefce8;color:#f59e0b}
    .kpi-icon.red   {background:#fef2f2;color:#dc2626}
    .kpi-icon.green {background:#f0fdf4;color:#16a34a}
    .kpi-label{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:2px}
    .kpi-value{font-size:1.5rem;font-weight:800;color:#1e293b;line-height:1}

    /* ── Chart cards ── */
    .chart-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.04);overflow:hidden}
    .chart-card-head{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .chart-card-title{font-size:.8rem;font-weight:700;color:#334155}
    .chart-card-sub{font-size:.7rem;color:#94a3b8;font-weight:400}
    .chart-card-body{padding:20px}

    /* ── Activity table card ── */
    .table-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.04);overflow:hidden}
    .table-card-head{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px}
    .table-card-icon{width:32px;height:32px;background:#1e293b;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.85rem;flex-shrink:0}

    /* ── Table ── */
    #tabelaAtividades thead th{font-size:.6rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:9px 14px}
    #tabelaAtividades tbody td{font-size:.8rem;color:#334155;padding:10px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    #tabelaAtividades tbody tr:last-child td{border-bottom:none}
    #tabelaAtividades tbody tr:hover{background:#f8fafc}

    /* ── Activity dot ── */
    .act-dot{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .act-dot.stock      {background:#fefce8;color:#f59e0b}
    .act-dot.maintenance{background:#f0f9ff;color:#0369a1}
    .act-dot.default    {background:#eff6ff;color:#1a56db}

    /* ── Category badge ── */
    .cat-badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:6px;font-size:.68rem;font-weight:700;border:1px solid}
    .cat-badge.stock      {background:#fefce8;color:#92400e;border-color:#fde68a}
    .cat-badge.maintenance{background:#f0f9ff;color:#0369a1;border-color:#bae6fd}
    .cat-badge.default    {background:#eff6ff;color:#1a56db;border-color:#bfdbfe}

    /* ── Avatar ── */
    .avatar{width:28px;height:28px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:#475569;flex-shrink:0}

    /* ── DataTables overrides ── */
    .dataTables_wrapper{padding:14px 20px 16px}
    .dataTables_wrapper .dataTables_filter input{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:5px 10px;color:#334155}
    .dataTables_wrapper .dataTables_filter input:focus{outline:none;border-color:#64748b;box-shadow:0 0 0 3px rgba(100,116,139,.1)}
    .dataTables_wrapper .dataTables_length select{font-size:.8rem;border:1px solid #e2e8f0;border-radius:8px;padding:4px 8px}
    .dataTables_wrapper .dataTables_info,.dataTables_wrapper .dataTables_paginate{font-size:.75rem;color:#94a3b8;margin-top:10px}
    .dataTables_wrapper .paginate_button{border-radius:6px!important;font-size:.75rem!important}
    .dataTables_wrapper .paginate_button.current{background:#1e293b!important;color:#fff!important;border:none!important;border-radius:6px!important}
</style>

<div class="container-fluid py-4 px-4">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Painel de Controlo
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.3rem;">
                Gestão Industrial
            </h4>
            <p style="font-size:.75rem;color:#94a3b8;margin:0;">Monitorização geral de equipamentos e stock</p>
        </div>
        <div style="font-size:.72rem;color:#94a3b8;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:6px 14px;">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->format('d M, Y') }}
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['title'=>'Equipamentos / Máquinas','value'=>$totalMaquinas,    'icon'=>'bi-gear-fill',               'color'=>'blue'],
                ['title'=>'Avariados',               'value'=>$maquinasParadas, 'icon'=>'bi-pause-btn-fill',          'color'=>'yellow'],
                ['title'=>'Stock Crítico',            'value'=>$stockCritico,    'icon'=>'bi-exclamation-triangle-fill','color'=>'red'],
                ['title'=>'Compras Pendentes',        'value'=>$comprasPendentes,'icon'=>'bi-cart-check-fill',         'color'=>'green'],
            ];
        @endphp
        @foreach($stats as $s)
        <div class="col-md-3">
            <div class="kpi-card {{ $s['color'] }}">
                <div class="kpi-icon {{ $s['color'] }}"><i class="bi {{ $s['icon'] }}"></i></div>
                <div>
                    <div class="kpi-label">{{ $s['title'] }}</div>
                    <div class="kpi-value">{{ $s['value'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="chart-card h-100">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Actividade de Manutenção</div>
                        <div class="chart-card-sub">Últimos 12 meses</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;"><canvas id="manutencaoChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-card-head">
                    <div class="chart-card-title">Inventário por Categoria</div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;"><canvas id="stockChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA DE ACTIVIDADES --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-icon"><i class="bi bi-list-stars"></i></div>
            <div>
                <div style="font-size:.82rem;font-weight:700;color:#334155;">Fluxo de Actividades</div>
                <div style="font-size:.7rem;color:#94a3b8;">Registos cronológicos das operações</div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tabelaAtividades" class="table mb-0">
                <thead>
                    <tr>
                        <th>Evento / Descrição</th>
                        <th>Responsável</th>
                        <th>Categoria</th>
                        <th class="text-end">Data / Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $activity)
                    @php
                        $dotClass = match($activity->type) {
                            'stock'       => 'stock',
                            'maintenance' => 'maintenance',
                            default       => 'default',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="act-dot {{ $dotClass }}">
                                    <i class="bi bi-circle-fill" style="font-size:.45rem;"></i>
                                </div>
                                <span class="fw-semibold">{{ $activity->description }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar">{{ strtoupper(substr($activity->user_name, 0, 1)) }}</div>
                                <span style="color:#94a3b8;font-size:.78rem;">{{ $activity->user_name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="cat-badge {{ $dotClass }}">{{ ucfirst($activity->type) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="fw-semibold" style="font-size:.78rem;">{{ $activity->created_at->format('H:i') }}</div>
                            <div style="font-size:.68rem;color:#94a3b8;">{{ $activity->created_at->format('d/m/Y') }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function () {

    $('#tabelaAtividades').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 5,
        order: [[3, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
    });

    // Gráfico linha — Manutenções
    new Chart(document.getElementById('manutencaoChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($meses) !!},
            datasets: [{
                label: 'Intervenções',
                data: {!! json_encode($contagemManutencoes) !!},
                borderColor: '#1e293b',
                backgroundColor: 'rgba(30,41,59,0.04)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: '#1e293b'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' } }
            }
        }
    });

    // Gráfico donut — Stock
    new Chart(document.getElementById('stockChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($dadosStock->pluck('nome')) !!},
            datasets: [{
                data: {!! json_encode($dadosStock->pluck('total')) !!},
                backgroundColor: [
                    /* Cyan puro e variações */
                    '#00ffff','#00cccc','#009999','#006666','#00e5e5',
                    '#33ffff','#66ffff','#99ffff','#00b3b3','#007a7a',
                    /* Magenta puro e variações */
                    '#ff00ff','#cc00cc','#990099','#660066','#e500e5',
                    '#ff33ff','#ff66ff','#ff99ff','#b300b3','#7a007a',
                    /* Yellow puro e variações */
                    '#ffff00','#cccc00','#999900','#666600','#e5e500',
                    '#ffff33','#ffff66','#ffff99','#b3b300','#7a7a00',
                    /* Key/Black variações (substituído por tons escuros coloridos) */
                    '#1a1a2e','#16213e','#0f3460','#533483','#2b2d42',
                    /* Cyan + Magenta = Azul/Violeta */
                    '#0000ff','#3300ff','#6600ff','#9900ff','#cc00ff',
                    '#0033ff','#0066ff','#0099ff','#00ccff','#3366ff',
                    /* Cyan + Yellow = Verde */
                    '#00ff00','#00cc33','#00ff66','#00ff99','#00ffcc',
                    '#33ff00','#66ff00','#99ff00','#ccff00','#66ff33',
                    /* Magenta + Yellow = Vermelho/Laranja */
                    '#ff0000','#ff3300','#ff6600','#ff9900','#ffcc00',
                    '#ff0033','#ff0066','#ff0099','#ff00cc','#cc3300',
                    /* Mistura C+M a 50% */
                    '#7f00ff','#ff007f','#007fff','#00ff7f','#7fff00',
                    '#ff7f00','#7f7fff','#ff7f7f','#7fff7f','#7f7f00',
                    /* Mistura C+M+Y a várias percentagens */
                    '#ff6680','#80ff66','#6680ff','#ffaa00','#00ffaa',
                    '#aa00ff','#ff00aa','#00aaff','#aaff00','#ff5500',
                    /* Tons vibrantes intermédios */
                    '#ff4444','#44ff44','#4444ff','#ffaa44','#44ffaa',
                    '#aa44ff','#ff44aa','#44aaff','#aaff44','#ff8844',
                ],
                hoverOffset: 10,
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 9, font: { size: 10 }, color: '#64748b', padding: 12 }
                }
            }
        }
    });

});
</script>
</x-app-layout>