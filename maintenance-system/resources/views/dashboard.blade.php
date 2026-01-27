<x-app-layout>
    <style>
        :root { --admin-bg: #f8fafc; }
        body { background-color: var(--admin-bg); font-family: 'Inter', sans-serif; }
        .card-custom { border-radius: 1rem; border: none; transition: transform 0.2s; }
        .icon-shape { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 0.75rem; }
        .avatar-circle { width: 32px; height: 32px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: #475569; }
        /* DataTable Style Overrides */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #0d6efd !important; color: white !important; border: none !important; border-radius: 0.5rem; }
        .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 0.05em; color: #64748b; border-top: none; }
    </style>

    <div class="container-fluid py-4 px-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold tracking-tight text-slate-800 mb-0">Gestão Industrial<span class="text-primary">.</span></h2>
                <p class="text-muted small">Monitorização geral de equipamentos e stock</p>
            </div>
            <div class="d-none d-md-block text-end">
                <span class="badge bg-white text-dark shadow-sm py-2 px-3 rounded-pill border">
                    <i class="bi bi-calendar3 me-2 text-primary"></i> {{ now()->format('d M, Y') }}
                </span>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @php
                $stats = [
                    ['title' => 'EQUIPAMENTOS', 'value' => $totalMaquinas, 'icon' => 'bi-gear-fill', 'color' => 'primary'],
                    ['title' => 'AVARIADOS', 'value' => $maquinasParadas, 'icon' => 'bi-pause-btn-fill', 'color' => 'warning'],
                    ['title' => 'STOCK CRÍTICO', 'value' => $stockCritico, 'icon' => 'bi-exclamation-triangle-fill', 'color' => 'danger'],
                    ['title' => 'COMPRAS PEND.', 'value' => $comprasPendentes, 'icon' => 'bi-cart-check-fill', 'color' => 'success'],
                ];
            @endphp

            @foreach($stats as $stat)
            <div class="col-md-3">
                <div class="card card-custom shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-{{ $stat['color'] }} bg-opacity-10 me-3">
                            <i class="bi {{ $stat['icon'] }} text-{{ $stat['color'] }} fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-black d-block mb-0" style="font-size: 0.65rem; letter-spacing: 1px;">{{ $stat['title'] }}</small>
                            <h4 class="mb-0 fw-bold">{{ $stat['value'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm p-4 bg-white h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">Atividade de Manutenção <span class="text-muted fw-normal small">(12 meses)</span></h6>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="manutencaoChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-custom shadow-sm p-4 bg-white h-100">
                    <h6 class="fw-bold mb-4">Inventário por Categoria</h6>
                    <div style="height: 300px;">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom shadow-sm overflow-hidden bg-white">
            <div class="card-header bg-white border-0 py-4 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary p-2 rounded-3 me-3">
                        <i class="bi bi-list-stars text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Fluxo de Atividades</h5>
                        <p class="text-muted small mb-0">Registos cronológicos das operações</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="tabelaAtividades" class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>Evento / Descrição</th>
                                <th>Responsável</th>
                                <th>Categoria</th>
                                <th class="text-end">Data/Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            @php
                                $badgeColor = match($activity->type) {
                                    'stock' => 'warning',
                                    'maintenance' => 'info',
                                    default => 'primary'
                                };
                            @endphp
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-{{ $badgeColor }} bg-opacity-10 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="fas fa-circle text-{{ $badgeColor }}" style="font-size: 8px;"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $activity->description }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">{{ strtoupper(substr($activity->user_name, 0, 1)) }}</div>
                                        <span class="text-muted small">{{ $activity->user_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} px-3 border border-{{ $badgeColor }} border-opacity-25">
                                        {{ ucfirst($activity->type) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-dark small mb-0">{{ $activity->created_at->format('H:i') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $activity->created_at->format('d/m/Y') }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // DataTable Traduzida e Estilizada
            $('#tabelaAtividades').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json" },
                pageLength: 5,
                order: [[3, "asc"]],
                dom: '<"d-flex justify-content-between align-items-center mb-3"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
            });

            // Gráfico Manutenção
            new Chart(document.getElementById('manutencaoChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($meses) !!},
                    datasets: [{
                        label: 'Intervenções',
                        data: {!! json_encode($contagemManutencoes) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#0d6efd'
                    }]
                },

                
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            // Gráfico Stock
            new Chart(document.getElementById('stockChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($dadosStock->pluck('nome')) !!},
                    datasets: [{
                        data: {!! json_encode($dadosStock->pluck('total')) !!},
                        backgroundColor: ['#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'],
                        hoverOffset: 15
                    }]
                },
                options: { 
                    maintainAspectRatio: false, 
                    cutout: '70%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
                }
            });
        });
    </script>
</x-app-layout>