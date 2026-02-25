<x-app-layout>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>

        /* Estilos para Impressão */
@media print {
    /* Esconde tudo o que não é a tabela */
    .btn, .modal, form, .tank-visor, canvas, .badge, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate, .card-header button {
        display: none !important;
    }
    
    .container { width: 100%; max-width: 100%; margin: 0; padding: 0; }
    .card { border: none !important; shadow: none !important; }
    
    /* Força a exibição da seção de assinatura apenas na impressão */
    #print-footer {
        display: block !important;
        margin-top: 50px;
    }

    /* Ajuste de cores para impressão (badges pretos/brancos para poupar tinta) */
    .table td, .table th { color: #000 !important; border: 1px solid #ddd !important; }
}

/* Esconde o rodapé de assinatura na tela normal */
#print-footer { display: none; }

.signature-line {
    border-top: 1px solid #000;
    width: 250px;
    margin-top: 40px;
    display: inline-block;
}
        .container { max-width: 100%; }
        .card-fuel { border-radius: 15px; border: none; }
        
        /* Estilo do Tanque */
        .tank-visor { height: 70px; background: #eee; border-radius: 12px; border: 3px solid #ddd; overflow: hidden; position: relative; }
        .tank-level { height: 100%; transition: width 1.5s ease-in-out; background: linear-gradient(90deg, #00b09b, #96c93d); }
        .tank-text { position: absolute; width: 100%; text-align: center; top: 50%; transform: translateY(-50%); font-weight: 800; color: #222; text-shadow: 0 0 5px rgba(255,255,255,0.8); font-size: 1.1rem; }
        
        /* Tabela Compacta Otimizada */
        .table-compact th { font-size: 0.65rem; text-transform: uppercase; color: #666; padding: 12px 8px; border-bottom: 2px solid #f8f9fa; }
        .table-compact td { font-size: 0.8rem; padding: 10px 8px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; }
        
        .bg-entrada { background-color: #e6f4ea; color: #1e7e34; }
        .bg-saida { background-color: #fce8e6; color: #d93025; }

        /* Ajustes DataTables para combinar com seu layout */
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #eee; border-radius: 20px; padding: 4px 12px; font-size: 0.8rem; }
        .dataTables_wrapper .dataTables_length select { border: 1px solid #eee; border-radius: 5px; font-size: 0.8rem; }
    </style>

    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-fuel-pump-fill text-primary me-2"></i>Gestão de Combustível</h4>
            <button type="button" class="btn btn-success btn-sm shadow-sm fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                <i class="bi bi-droplet-fill me-1"></i> Abastecer Tanque
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="card card-fuel shadow-sm p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-muted mb-0 small text-uppercase">Nível Atual do Stock</h6>
                        <span class="badge bg-light text-dark border">{{ number_format($percentagem, 1) }}%</span>
                    </div>
                    <div class="tank-visor mb-2">
                        <div id="tank-fill" class="tank-level" style="width: {{ $percentagem }}%; {{ $percentagem < 20 ? 'background: #dc3545;' : '' }}"></div>
                        <div class="tank-text">{{ number_format($restante, 0, ',', '.') }} L</div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted px-1 mb-4" style="font-size: 0.7rem;">
                        <span>CAPACIDADE: {{ number_format($capacidadeTotal, 0, ',', '.') }}L</span>
                        <span class="fw-bold text-{{ $percentagem < 20 ? 'danger' : 'success' }}">{{ $percentagem < 20 ? 'REABASTECER AGORA' : 'NÍVEL SEGURO' }}</span>
                    </div>
                    <canvas id="consumptionChart" style="max-height: 160px;"></canvas>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card card-fuel shadow-sm p-4 h-100">
                    <h6 class="fw-bold text-muted mb-4 small text-uppercase">Registrar Saída (Abastecimento)</h6>
                    <form action="{{ route('fuel.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4"><label class="small fw-bold text-secondary">Data</label><input type="date" name="date" class="form-control form-control-sm border-light-subtle" value="{{ date('Y-m-d') }}" required></div>
                            <div class="col-md-4"><label class="small fw-bold text-secondary">Matrícula</label><input type="text" name="plate" class="form-control form-control-sm border-light-subtle" placeholder="Ex: LDA-12-34" required></div>
                            <div class="col-md-4"><label class="small fw-bold text-secondary">Empresa</label><input type="text" name="company" class="form-control form-control-sm border-light-subtle"></div>

                            <div class="col-md-4"><label class="small fw-bold text-secondary">Contador Inicial (L)</label><input type="number" name="start_counter" id="ci" class="form-control form-control-sm bg-light-subtle" oninput="calc()" required></div>
                            <div class="col-md-4"><label class="small fw-bold text-secondary">Contador Final (L)</label><input type="number" name="end_counter" id="cf" class="form-control form-control-sm bg-light-subtle" oninput="calc()" required></div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-primary">Qtd. Abastecida</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" id="qty" class="form-control form-control-sm border-primary fw-bold text-primary bg-white" readonly>
                                    <span class="input-group-text bg-primary text-white border-primary">L</span>
                                </div>
                            </div>
                            <div class="col-md-6"><label class="small fw-bold text-secondary">Operador</label><input type="text" name="operator" class="form-control form-control-sm"></div>
                            <div class="col-md-6"><label class="small fw-bold text-secondary">Motorista</label><input type="text" name="driver" class="form-control form-control-sm"></div>
                            <div class="col-12 text-end"><button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm rounded-pill">Confirmar Saída</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card card-fuel shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark small text-uppercase">Histórico de Movimentações (Entradas e Saídas)</h6>
        <button class="btn btn-sm btn-outline-secondary border-0"><i class="bi bi-download me-1"></i> CSV</button>
    </div>
    <div class="table-responsive p-2">
        <table id="fuelTable" class="table table-hover table-compact mb-0 w-100">
            <thead class="bg-light">
                <tr>
                    <th>Data</th>
                    <th>Operação</th>
                    <th>Identificação / Viatura</th>
                    <th>Empresa / Dep.</th>
                    <th class="text-center">Cont. Inicial</th>
                    <th class="text-center">Cont. Final</th>
                    <th class="text-center">Quantidade</th>
                    <th>Responsável</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historico as $item)
                <tr>
                    <td class="text-muted" data-sort="{{ strtotime($item->date) }}">
                        {{ date('d/m/Y', strtotime($item->date)) }}
                    </td>
                    <td>
                        @if($item->tipo == 'ENTRADA')
                            <span class="badge bg-entrada rounded-pill px-2" style="font-size: 0.6rem;">ENTRADA</span>
                        @else
                            <span class="badge bg-saida rounded-pill px-2" style="font-size: 0.6rem;">SAÍDA</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $item->ident }}</td>
                    <td>{{ $item->company ?: '---' }}</td>
                    
                    <td class="text-center text-muted">
                        {{ $item->start_counter ? number_format($item->start_counter, 0, ',', '.') : '---' }}
                    </td>
                    <td class="text-center text-muted">
                        {{ $item->end_counter ? number_format($item->end_counter, 0, ',', '.') : '---' }}
                    </td>

                    <td class="text-center">
                        <span class="fw-bold {{ $item->tipo == 'ENTRADA' ? 'text-success' : 'text-danger' }}">
                            {{ $item->tipo == 'ENTRADA' ? '+' : '-' }} {{ number_format($item->quantity, 0, ',', '.') }} L
                        </span>
                    </td>
                    <td><span class="small fw-semibold text-secondary">{{ $item->responsavel ?: 'Sistema' }}</span></td>
                    <td class="text-center">
                        <button class="btn btn-light btn-sm rounded-circle"><i class="bi bi-pencil small"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted small italic">Nenhum registro encontrado nas movimentações.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalEntrada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h6 class="modal-title fw-bold">Entrada de Cisterna</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('fuel.storeEntry') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary">Data do Atesto</label>
                        <input type="date" name="date" class="form-control form-control-sm shadow-none" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary">Litros Adicionados</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="quantity" class="form-control shadow-none" placeholder="Ex: 2000" step="0.01" required>
                            <span class="input-group-text bg-light">L</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-secondary">Fornecedor / Ref.</label>
                        <input type="text" name="supplier" class="form-control form-control-sm shadow-none" placeholder="Ex: Petromoc">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-3 shadow-sm">Confirmar Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            $('#fuelTable').DataTable({
                "order": [[ 0, "desc" ]], // Ordenar por data decrescente
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json"
                },
                "pageLength": 10,
                "responsive": true
            });
        });

        function calc() {
            const i = parseFloat(document.getElementById('ci').value) || 0;
            const f = parseFloat(document.getElementById('cf').value) || 0;
            const res = f - i;
            document.getElementById('qty').value = res > 0 ? res : 0;
        }

        // Gráfico (mantendo sua lógica original)
        const ctx = document.getElementById('consumptionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($historico->where('tipo', 'SAÍDA')->take(7)->reverse()->pluck('date')->map(fn($d) => date('d/m', strtotime($d)))) !!},
                datasets: [{
                    label: 'Consumo (L)',
                    data: {!! json_encode($historico->where('tipo', 'SAÍDA')->take(7)->reverse()->pluck('quantity')) !!},
                    backgroundColor: '#0d6efd',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { display: false }, x: { grid: { display: false }, ticks: { font: { size: 9 } } } }
            }
        });
    </script>
</x-app-layout>