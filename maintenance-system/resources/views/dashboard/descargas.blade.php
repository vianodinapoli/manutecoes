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
            <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.3rem;">Descargas</h4>
            <p style="font-size:.75rem;color:#94a3b8;margin:0;">Resumo das operações (navios) e do que já foi confirmado na balança</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dash-tabs">
                <a href="{{ route('dashboard.descargas') }}" class="active">Descargas</a>
                <a href="{{ route('dashboard.saidas') }}">Saídas</a>
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
                <div class="kpi-icon blue"><i class="bi bi-water"></i></div>
                <div>
                    <div class="kpi-label">Operações em curso</div>
                    <div class="kpi-value">{{ $opsEmCurso }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">{{ $opsArquivadas }} arquivadas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card yellow">
                <div class="kpi-icon yellow"><i class="bi bi-box-seam-fill"></i></div>
                <div>
                    <div class="kpi-label">Qtd. esperada</div>
                    <div class="kpi-value">{{ number_format($esperadoAtivas, 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card green">
                <div class="kpi-icon green"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="kpi-label">Qtd. recebida</div>
                    <div class="kpi-value">{{ number_format($recebidoAtivas, 0, ',', ' ') }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">
                        {{ $camioesAtivas }} camiões · {{ number_format($toneladasAtivas, 1, ',', ' ') }} t
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card red">
                <div class="kpi-icon red"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="kpi-label">Ainda em falta</div>
                    <div class="kpi-value">{{ number_format($emFalta, 0, ',', ' ') }}</div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:3px;">
                        {{ number_format($transitoAtivas, 0, ',', ' ') }} por confirmar na balança
                    </div>
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
                        <div class="chart-card-title">Quantidade recebida por mês</div>
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
                        <div class="chart-card-title">Recebido por tipo</div>
                        <div class="chart-card-sub">Operações em curso</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;">
                        @if($porTipo->isEmpty())
                            <div class="chart-empty">Sem recepções confirmadas</div>
                        @else
                            <canvas id="tipoChart"></canvas>
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
                        <div class="chart-card-title">Esperado vs recebido por operação</div>
                        <div class="chart-card-sub">Operações em curso (até 8)</div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div style="height:260px;">
                        @if($labelsOps->isEmpty())
                            <div class="chart-empty">Nenhuma operação em curso</div>
                        @else
                            <canvas id="operacoesChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABELA DE OPERAÇÕES --}}
    <div class="table-card">
        <div class="table-card-head">
            <div class="table-card-icon"><i class="bi bi-list-stars"></i></div>
            <div>
                <div style="font-size:.82rem;font-weight:700;color:#334155;">Operações</div>
                <div style="font-size:.7rem;color:#94a3b8;">
                    {{ $opsEmCurso }} em curso · {{ $opsArquivadas }} arquivadas
                    @if($opsOutras > 0) · {{ $opsOutras }} outras @endif
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="tabelaOperacoes" class="table tabela-dash mb-0">
                <thead>
                    <tr>
                        <th>Navio</th>
                        <th>Empresa</th>
                        <th>BL / Factura</th>
                        <th class="text-end">Esperado</th>
                        <th class="text-end">Recebido</th>
                        <th class="text-end">Camiões</th>
                        <th>Progresso</th>
                        <th>Estado</th>
                        <th class="text-end">Criada em</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operacoes as $op)
                    <tr>
                        <td class="fw-semibold" style="color:#1e293b;">{{ $op->navio }}</td>
                        <td>{{ $op->empresa ?: '—' }}</td>
                        <td>
                            @if($op->bl)<span class="mono">{{ $op->bl }}</span>@endif
                            @if($op->factura)<span class="mono">{{ $op->factura }}</span>@endif
                            @if(!$op->bl && !$op->factura)—@endif
                        </td>
                        <td class="text-end" data-order="{{ $op->esperado }}">{{ number_format($op->esperado, 0, ',', ' ') }}</td>
                        <td class="text-end" data-order="{{ $op->recebido }}">{{ number_format($op->recebido, 0, ',', ' ') }}</td>
                        <td class="text-end" data-order="{{ $op->camioes }}">{{ $op->camioes }}</td>
                        <td data-order="{{ $op->pct }}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="prog flex-grow-1">
                                    <span class="{{ $op->pct > 100 ? 'over' : ($op->pct == 100 ? 'full' : '') }}"
                                          style="width:{{ min(100, $op->pct) }}%"></span>
                                </div>
                                <span style="font-size:.72rem;color:#64748b;width:36px;">{{ $op->pct }}%</span>
                            </div>
                        </td>
                        <td><span class="cat-badge {{ $op->badge }}">{{ $op->estado_label }}</span></td>
                        <td class="text-end" data-order="{{ \Carbon\Carbon::parse($op->created_at)->timestamp }}">
                            <div class="fw-semibold" style="font-size:.78rem;">{{ \Carbon\Carbon::parse($op->created_at)->format('d/m/Y') }}</div>
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
        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8' }, beginAtZero: true }
    };

    $('#tabelaOperacoes').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        pageLength: 8,
        order: [[8, 'desc']],
        dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
    });

    // Linha — recebido por mês
    new Chart(document.getElementById('mensalChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($meses) !!},
            datasets: [{
                label: 'Recebido',
                data: {!! json_encode($contagemMensal) !!},
                borderColor: '#1e293b',
                backgroundColor: 'rgba(30,41,59,0.04)',
                borderWidth: 2, tension: 0.4, fill: true,
                pointRadius: 3, pointBackgroundColor: '#1e293b'
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: eixos }
    });

    // Donut — por tipo
    const tipoEl = document.getElementById('tipoChart');
    if (tipoEl) {
        new Chart(tipoEl, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($porTipo->pluck('nome')->values()) !!},
                datasets: [{
                    data: {!! json_encode($porTipo->pluck('total')->values()) !!},
                    backgroundColor: cores, hoverOffset: 10, borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false, cutout: '72%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 }, color: '#64748b', padding: 12 } } }
            }
        });
    }

    // Barras — esperado vs recebido por operação
    const opEl = document.getElementById('operacoesChart');
    if (opEl) {
        new Chart(opEl, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelsOps) !!},
                datasets: [
                    { label: 'Esperado', data: {!! json_encode($esperadoOps) !!}, backgroundColor: '#cbd5e1', borderRadius: 4 },
                    { label: 'Recebido', data: {!! json_encode($recebidoOps) !!}, backgroundColor: '#1e293b', borderRadius: 4 }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10 }, color: '#64748b' } } },
                scales: eixos
            }
        });
    }
});
</script>
</x-app-layout>