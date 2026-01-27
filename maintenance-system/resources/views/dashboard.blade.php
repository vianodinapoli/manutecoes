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
                    <h6 class="fw-bold mb-3">Atividade de Manutenção (12 Meses)</h6>
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
<div class="max-w-md mx-auto p-4 space-y-2">
    <div class="flex items-center justify-between px-2 mb-3">
        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Fluxo Recente</h3>
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
    </div>

    @forelse($activities as $activity)
        @php
            $config = match($activity->type) {
                'stock' => ['icon' => 'fa-box', 'color' => 'orange', 'label' => 'Estoque'],
                'maintenance' => ['icon' => 'fa-wrench', 'color' => 'blue', 'label' => 'Manutenção'],
                default => ['icon' => 'fa-bolt', 'color' => 'indigo', 'label' => 'Sistema']
            };
        @endphp

        <div class="group relative bg-white border border-slate-100 rounded-xl p-3 transition-all duration-300 hover:shadow-[0_10px_20px_rgba(0,0,0,0.04)] hover:border-slate-200 hover:-translate-y-0.5 cursor-default overflow-hidden">
            
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-{{ $config['color'] }}-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

            <div class="flex items-center justify-between gap-3">
                
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-{{ $config['color'] }}-50 flex items-center justify-center transition-transform group-hover:scale-110">
                        <i class="fas {{ $config['icon'] }} text-[12px] text-{{ $config['color'] }}-500"></i>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-slate-700 leading-tight tracking-tight group-hover:text-black transition-colors">
                            {{ $activity->description }}
                        </span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[9px] font-black text-{{ $config['color'] }}-400 uppercase tracking-tighter">{{ $config['label'] }}</span>
                            <span class="text-[9px] text-slate-300">•</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $activity->user_name }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-right flex flex-col items-end shrink-0">
                    <span class="text-[10px] font-black text-slate-800 tabular-nums">
                        {{ $activity->created_at->format('H:i') }}
                    </span>
                    <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter leading-none">
                        {{ $activity->created_at->diffForHumans(['short' => true]) }}
                    </span>
                </div>

            </div>
        </div>
    @empty
        <div class="p-6 text-center border border-dashed border-slate-100 rounded-xl">
            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest text-center">Vazio</span>
        </div>
    @endforelse

    <div class="pt-2">
        <button class="w-full py-2 rounded-lg bg-slate-50 border border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] hover:bg-slate-900 hover:text-white transition-all">
            Histórico Completo
        </button>
    </div>
</div>
       

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

// $('#tabela-atividades').DataTable({
//         "language": {
//             "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json"
//         },
//         "order": [[ 2, "desc" ]], // Ordenar pela data (3ª coluna) por padrão
//         "pageLength": 5,
//         "lengthMenu": [5, 10, 25, 50],
//         "dom": '<"flex justify-between mb-2"f>t<"flex justify-between mt-2"ip>', // Ajuste de layout Tailwind/Bootstrap
//         "columnDefs": [
//             { "orderable": false, "targets": 0 } // Desativa ordenação no ícone/descrição se preferir
//         ]
//     });

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