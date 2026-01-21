<x-app-layout>
    <div class="py-4 px-4" style="background-color: #f4f6f9;">
        <h2 class="fw-bold mb-4">Gestão Industrial <span class="text-primary">.</span></h2>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-gear-fill text-primary fs-4"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">TOTAL DE EQUIPAMENTOS</small>
                    <h4 class="mb-0 fw-bold">{{ $totalMaquinas }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-pause-btn-fill text-warning fs-4"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">EQUIPAMENTOS AVARIADOS</small>
                    <h4 class="mb-0 fw-bold">{{ $maquinasParadas }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">STOCK CRÍTICO</small>
                    <h4 class="mb-0 fw-bold">{{ $stockCritico }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-cart-check-fill text-success fs-4"></i>
                </div>
                <div>
                    <small class="text-muted fw-bold">COMPRAS PENDENTES</small>
                    <h4 class="mb-0 fw-bold">{{ $comprasPendentes }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

  

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                    <h6 class="fw-bold mb-3">Atividade de Manutenção (6 Meses)</h6>
                    <div style="height: 300px;">
                        <canvas id="manutencaoChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
                    <h6 class="fw-bold mb-3">Inventário por Categoria</h6>
                    <div style="height: 300px;">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuração do Gráfico de Manutenções
        new Chart(document.getElementById('manutencaoChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($meses) !!},
                datasets: [{
                    label: 'Intervenções',
                    data: {!! json_encode($contagemManutencoes) !!},
                    borderColor: '#0d6efd',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(13, 110, 253, 0.05)'
                }]
            },
            options: { maintainAspectRatio: false }
        });

       // Gráfico de Rosca (Peças Reais)
new Chart(document.getElementById('stockChart'), {
    type: 'doughnut',
    data: {
        // Agora pegamos o 'nome' do artigo
        labels: {!! json_encode($dadosStock->pluck('nome')) !!},
        datasets: [{
            data: {!! json_encode($dadosStock->pluck('total')) !!},
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                '#6f42c1', '#fd7e14', '#20c997', '#007bff', '#6c757d'
            ],
            borderWidth: 2,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    font: { size: 10 }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' Qtd: ' + context.raw + ' unidades';
                    }
                }
            }
        }
    }
});
    </script>
</x-app-layout>