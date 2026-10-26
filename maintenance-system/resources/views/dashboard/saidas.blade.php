<x-app-layout>

    @include('components.skeleton-loading')
    @include('dashboard._estilos')

<div class="container-fluid py-4 px-4">

    {{-- CABEÇALHO --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div style="font-size:.6rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#94a3b8;margin-bottom:4px;">
                Painel de Controlo
            </div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.3rem;">Saídas de Carga</h4>
            <p style="font-size:.75rem;color:#94a3b8;margin:0;">Camiões carregados e carga que saiu do armazém</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dash-tabs">
                <a href="{{ route('dashboard.descargas') }}">Descargas</a>
                <a href="{{ route('dashboard.saidas') }}" class="active">Saídas</a>
            </div>
            <div style="font-size:.72rem;color:#94a3b8;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:6px 14px;">
                <i class="bi bi-calendar3 me-1"></i> {{ now()->format('d M, Y') }}
            </div>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card blue">
                <div class="kpi-icon blue"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="kpi-label">Saídas abertas</div>
                    <div class="kpi-value">{{ $saidasAbertas }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">{{ $porCarregar }} camiões por carregar</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="kpi-label">Carregados hoje</div>
                    <div class="kpi-value">{{ $camioesHoje }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card yellow">
                <div class="kpi-icon yellow"><i class="bi bi-box-seam-fill"></i></div>
                <div>
                    <div class="kpi-label">Saído este mês</div>
                    <div class="kpi-value">{{ number_format($qtdMes, 0, ',', ' ') }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">{{ number_format($pesoMesTon, 1, ',', ' ') }} t líquidas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div>
                    <div class="kpi-label">Diferenças no mês</div>
                    <div class="kpi-value">{{ $alertasMes }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">acima de {{ $tolerancia }}% (quantidade vs peso)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS (linha 1) --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="chart-card h-100">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Quantidade saída por mês</div>
                        <div class="chart-card-sub">Últimos 12 meses</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;"><canvas id="mensalChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Saídas por navio</div>
                        <div class="chart-card-sub">Origem da carga</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;">
                        @if($porNavio->isEmpty())
                            <div class="chart-empty">Sem saídas registadas</div>
                        @else
                            <canvas id="navioChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICO (linha 2) --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-card-head">
                    <div>
                        <div class="chart-card-title">Camiões carregados por dia</div>
                        <div class="chart-card-sub">Últimos 14 dias</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:240px;"><canvas id="diasChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA DE CAMIÕES CARREGADOS --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-icon"><i class="bi bi-speedometer2"></i></div>
            <div>
                <div style="font-size:.82rem;font-weight:700;color:#334155;">Camiões carregados</div>
                <div style="font-size:.7rem;color:#94a3b8;">Do mais recente para o mais antigo</div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tabelaSaidas" class="table tabela-dash mb-0">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Matrícula / Motorista</th>
                        <th>Lista / Cliente</th>
                        <th>Navio</th>
                        <th class="text-end">Qtd.</th>
                        <th class="text-end">Peso líquido</th>
                        <th class="text-end">Diferença</th>
                        <th>Estado</th>
                        <th class="text-end">Data / Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimas as $c)
                    <tr>
                        <td>@if($c->ticket_numero)<span class="mono">{{ $c->ticket_numero }}</span>@else — @endif</td>
                        <td>
                            <div class="fw-semibold" style="color:#1e293b;">{{ $c->matricula ?: '—' }}</div>
                            <div style="font-size:.7rem;color:#94a3b8;">{{ $c->motorista ?: '—' }}</div>
                        </td>
                        <td>
                            <div>{{ $c->lista }}</div>
                            <div style="font-size:.7rem;color:#94a3b8;">{{ $c->cliente ?: '—' }}</div>
                        </td>
                        <td>{{ $c->navio }}</td>
                        <td class="text-end" data-order="{{ $c->qtd }}">{{ number_format($c->qtd, 0, ',', ' ') }}</td>
                        <td class="text-end" data-order="{{ $c->liquido ?? 0 }}">
                            {{ $c->liquido !== null ? number_format($c->liquido, 0, ',', ' ') . ' kg' : '—' }}
                        </td>
                        <td class="text-end" data-order="{{ $c->diff_pct ?? 0 }}">
                            @if($c->diff_pct !== null)
                                {{ $c->diff_pct > 0 ? '+' : '' }}{{ number_format($c->diff_pct, 1, ',', '') }}%
                            @else — @endif
                        </td>
                        <td>
                            @if($c->diff_pct === null)
                                <span class="cat-badge outra">Sem peso</span>
                            @else
                                <span class="cat-badge {{ $c->alerta ? 'alerta' : 'ok' }}">{{ $c->alerta ? 'Verificar' : 'Normal' }}</span>
                            @endif
                        </td>
                        <td class="text-end" data-order="{{ $c->data->timestamp }}">
                            <div class="fw-semibold" style="font-size:.78rem;">{{ $c->data->format('H:i') }}</div>
                            <div style="font-size:.68rem;color:#94a3b8;">{{ $c->data->format('d/m/Y') }}</div>
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

    const cores = ['#1e293b','#c60a1a','#187fc4','#7ed953','#f59e0b','#8b3ecf','#342cc9','#707070'];
    const eixos = {
        x: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8', precision: 0 }, beginAtZero: true }
    };

    $('#tabelaSaidas').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 8,
        order: [[8, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
    });

    // Linha — quantidade saída por mês
    new Chart(document.getElementById('mensalChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($meses) !!},
            datasets: [{
                label: 'Saído',
                data: {!! json_encode($qtdMensal) !!},
                borderColor: '#c60a1a',
                backgroundColor: 'rgba(198,10,26,0.05)',
                borderWidth: 2, tension: 0.4, fill: true,
                pointRadius: 3, pointBackgroundColor: '#c60a1a'
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: eixos }
    });

    // Donut — por navio
    const navioEl = document.getElementById('navioChart');
    if (navioEl) {
        new Chart(navioEl, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($porNavio->pluck('nome')->values()) !!},
                datasets: [{
                    data: {!! json_encode($porNavio->pluck('total')->values()) !!},
                    backgroundColor: cores, hoverOffset: 10, borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false, cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 }, color: '#64748b', padding: 12 } } }
            }
        });
    }

    // Barras — camiões por dia
    new Chart(document.getElementById('diasChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($dias) !!},
            datasets: [{
                label: 'Camiões',
                data: {!! json_encode($camioesDia) !!},
                backgroundColor: '#1e293b', borderRadius: 4
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: eixos }
    });
});
</script>
</x-app-layout>