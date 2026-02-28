<x-app-layout>
    <div class="print-header">
        <h2 class="text-center">RELATÓRIO DE MOVIMENTAÇÃO DE COMBUSTÍVEL</h2>
        <p class="text-center">Emitido em: {{ date('d/m/Y H:i') }} | Gerado por: {{ auth()->user()->name }}</p>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos de ecrã vs Impressão */
        .print-header, #print-footer { display: none; }

        @media print {
            .btn, .modal, form, .nav, .sidebar, .navbar, .alert-info, .dataTables_filter, .dataTables_length, .dataTables_info, .dataTables_paginate, .d-print-none {
                display: none !important;
            }
            .print-header { display: block !important; border-bottom: 2px solid #000; margin-bottom: 20px; }
            #print-footer { display: block !important; margin-top: 50px; }
            .container { width: 100% !important; max-width: 100% !important; margin: 0; padding: 0; }
            .card { border: none !important; shadow: none !important; }
            .table td, .table th { border: 1px solid #ddd !important; font-size: 9pt !important; }
            body { background-color: white !important; }
        }

        /* Estilo dos Mini Tanques */
        .tank-visor { height: 40px; background: #eee; border-radius: 8px; border: 2px solid #ddd; overflow: hidden; position: relative; }
        .tank-level { height: 100%; transition: width 1s ease-in-out; background: linear-gradient(90deg, #00b09b, #96c93d); }
        .tank-text { position: absolute; width: 100%; text-align: center; top: 50%; transform: translateY(-50%); font-weight: 700; color: #111; font-size: 0.75rem; }
        
        .card-fuel { border-radius: 12px; border: none; }
        .table-compact th { font-size: 0.7rem; text-transform: uppercase; background: #f8f9fa; color: #666; }
        .table-compact td { font-size: 0.85rem; vertical-align: middle; }
        .bg-entrada { background-color: #e6f4ea !important; color: #1e7e34 !important; border: none; }
        .bg-saida { background-color: #fce8e6 !important; color: #d93025 !important; border: none; }
        .signature-line { border-top: 1px solid #000; width: 200px; margin-top: 40px; display: inline-block; }
    </style>

    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <h4 class="fw-bold text-dark mb-0"><i class="bi bi-fuel-pump-fill text-primary me-2"></i>Gestão de Combustível</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                    <i class="bi bi-plus-circle me-1"></i> Atestar Tanque
                </button>
                <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm">
                    <i class="bi bi-printer me-1"></i> Imprimir Relatório
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4 d-print-none">
            <div class="col-md-4">
                <div class="card card-fuel shadow-sm p-3 h-100 bg-white">
                    <h6 class="fw-bold text-muted mb-3 small text-uppercase">Níveis de Stock Atual</h6>
                    <div class="overflow-auto" style="max-height: 380px;">
                        @foreach($tanques as $tanque)
                        <div class="mb-3 p-2 border-start border-3 border-success rounded bg-light">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold small">{{ $tanque->nome }}</span>
                                <span class="small fw-bold">{{ number_format($tanque->percentagem, 1) }}%</span>
                            </div>
                            <div class="tank-visor">
                                <div class="tank-level" style="width: {{ $tanque->percentagem }}%; {{ $tanque->percentagem < 15 ? 'background: #dc3545;' : '' }}"></div>
                                <div class="tank-text">{{ number_format($tanque->stock_atual, 0, ',', '.') }}L / {{ number_format($tanque->capacidade, 0, ',', '.') }}L</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-fuel shadow-sm p-4 h-100">
                    <h6 class="fw-bold text-muted mb-3 small text-uppercase">Registrar Abastecimento</h6>
                    <form action="{{ route('fuel.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold">Tanque de Origem</label>
                                <select name="tank_id" class="form-select border-primary" required>
                                    <option value="" selected disabled>Escolha o tanque...</option>
                                    @foreach($tanques as $tanque)
                                        <option value="{{ $tanque->id }}">{{ $tanque->nome }} (Disp: {{ number_format($tanque->stock_atual, 0) }}L)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Data</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold">Viatura</label>
                                <input type="text" name="plate" class="form-control" placeholder="Matrícula" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Contador Inicial</label>
                                <input type="number" name="start_counter" id="ci" class="form-control bg-light" oninput="calc()" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Contador Final</label>
                                <input type="number" name="end_counter" id="cf" class="form-control bg-light" oninput="calc()" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold text-primary">Total (L)</label>
                                <input type="number" id="qty" name="quantity" class="form-control border-primary fw-bold text-primary" readonly>
                            </div>
                         <div class="col-md-4">
    <label class="small fw-bold">Empresa</label>
    <input type="text" name="company" list="empresas-list" class="form-control" placeholder="Selecione ou digite...">
    <datalist id="empresas-list">
        <option value="Tanque da Fem">
        <option value="Bymoze">
        <option value="Nitro">
        <option value="Bomba Móvel">
    </datalist>
</div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Operador</label>
                                <input type="text" name="operator" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold">Motorista</label>
                                <input type="text" name="driver" class="form-control">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Confirmar Saída</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
         <div class="row g-3 mb-4">
    <div class=""> <div class="card card-fuel shadow-sm p-3">
            <h6 class="fw-bold text-muted mb-3 small text-uppercase text-center">
                <i class="bi bi-graph-up me-2"></i>Consumo Diário por Empresa (L)
            </h6>
            <div style="height: 220px; position: relative;">
                <canvas id="consumptionChart"></canvas>
            </div>
        </div>
    </div>
</div>
        </div>

        <div class="card mb-4 shadow-sm border-0 d-print-none">
            <div class="card-body bg-light rounded">
                <form action="{{ route('fuel.index') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <label class="small fw-bold">Datas</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="small fw-bold">Empresa</label>
                        <input type="text" name="company" class="form-control form-control-sm" value="{{ request('company') }}" placeholder="Nome da empresa">
                    </div> --}}
                    <div class="col-md-3">
                        <label class="small fw-bold">Tanque</label>
                        <select name="filter_tank_id" class=" form-select-sm form-control bg-light"">
                            <option value="">Todos os Tanques</option>
                            @foreach($tanques as $t)
                                <option value="{{ $t->id }}" {{ request('filter_tank_id') == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-1">
                        <button type="submit" class="btn btn-dark btn-sm w-100">Filtrar</button>
                        <a href="{{ route('fuel.index') }}" class="btn btn-outline-secondary btn-sm w-100">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        @if(request('from_date') || request('company') || request('filter_tank_id'))
        <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Resumo do Filtro Atual</h6>
                <small>Registos: {{ $contagemRegistos }}</small>
            </div>
            <div class="text-end">
                <span class="badge bg-danger me-1">Saídas: {{ number_format($totalConsumidoFiltro, 0) }}L</span>
                <span class="badge bg-success">Entradas: {{ number_format($totalEntradaFiltro, 0) }}L</span>
            </div>
        </div>
        @endif

        <div class="card card-fuel shadow-sm overflow-hidden">
            <div class="table-responsive p-3">
                <table id="fuelTable" class="table table-hover table-compact w-100">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Tanque</th>
                            <th>Identificação</th>
                            <th class="text-center">Contadores</th>
                            <th class="text-end">Qtd</th>
                            <th>Operador / Motorista</th>
                            <th class="text-center d-print-none">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historico as $item)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($item->date)) }}</td>
                            <td>
                                <span class="badge {{ $item->tipo == 'ENTRADA' ? 'bg-entrada' : 'bg-saida' }} rounded-pill px-3">
                                    {{ $item->tipo }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ $item->tanque_nome }}</td>
                            <td>{{ $item->ident }} <br><small class="text-muted">{{ $item->company }}</small></td>
                            <td class="text-center">
                                {{ $item->start_counter !== null ? number_format($item->start_counter, 0) . ' - ' . number_format($item->end_counter, 0) : '---' }}
                            </td>
                            <td class="text-end fw-bold {{ $item->tipo == 'ENTRADA' ? 'text-success' : 'text-danger' }}">
                                {{ $item->tipo == 'ENTRADA' ? '+' : '-' }} {{ number_format($item->quantity, 0) }}L
                            </td>
                            <td class="small">
                                <strong>Op:</strong> {{ $item->operator ?: '---' }}<br>
                                <strong>Mot:</strong> {{ $item->driver ?: '---' }}
                            </td>
                       <td class="text-center d-print-none">
    <div class="d-flex justify-content-center gap-1">
        <button type="button" 
        class="btn btn-sm btn-outline-primary rounded-circle btn-edit" 
        data-id="{{ $item->id }}" 
        data-tipo="{{ $item->tipo }}">
    <i class="bi bi-pencil"></i>
</button>

        <button type="button" 
                class="btn btn-sm btn-outline-danger rounded-circle btn-delete" 
                data-id="{{ $item->id }}" 
                data-tipo="{{ $item->tipo }}"
                data-url="{{ $item->tipo == 'ENTRADA' ? route('fuel.entry.destroy', $item->id) : route('fuel.log.destroy', $item->id) }}">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="print-footer" class="text-center">
            <div class="row">
                <div class="col-6">
                    <div class="signature-line"></div>
                    <p class="small fw-bold mt-2">Operador/Responsável</p>
                </div>
                <div class="col-6">
                    <div class="signature-line"></div>
                    <p class="small fw-bold mt-2">Visto/Administração</p>
                </div>
            </div>
            <p class="mt-4 small text-muted">Documento gerado eletronicamente pelo Sistema de Gestão de Combustível.</p>
        </div>
    </div>

    <div class="modal fade" id="modalEntrada" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-success text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-truck me-2"></i>Nova Entrada de Cisterna</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('fuel.storeEntry') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="small fw-bold">Tanque de Destino</label>
                            <select name="tank_id" class="form-select" required>
                                @foreach($tanques as $tanque)
                                    <option value="{{ $tanque->id }}">{{ $tanque->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="small fw-bold">Data</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold">Litros Recebidos</label>
                                <input type="number" name="quantity" class="form-control" placeholder="Ex: 5000" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="small fw-bold">Fornecedor / Guia</label>
                            <input type="text" name="supplier" class="form-control" placeholder="Ex: Petromoc / Guia 001">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-success fw-bold px-4">Confirmar Entrada</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div class="modal fade" id="editLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editLogForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_log_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Abastecimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Viatura/Placa</label>
                        <input type="text" name="plate" id="edit_plate" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Contador Inicial</label>
                            <input type="number" name="start_counter" id="edit_start" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Contador Final</label>
                            <input type="number" name="end_counter" id="edit_end" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Empresa</label>
                        <input type="text" name="company" id="edit_company" list="empresas-list" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>
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

$(document).ready(function() {
    console.log("JavaScript carregado e pronto!");

    // Evento para o botão de EDITAR
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        
        const id = $(this).data('id');
        const tipo = $(this).data('tipo');
        
        if (tipo === 'SAÍDA') {
            const url = `/fuel-log/${id}/json`;
            $.get(url, function(data) {
                $('#edit_log_id').val(data.id);
                $('#edit_plate').val(data.plate);
                $('#edit_start').val(data.start_counter);
                $('#edit_end').val(data.end_counter);
                $('#edit_company').val(data.company);
                
                var myModal = new bootstrap.Modal(document.getElementById('editLogModal'));
                myModal.show();
            }).fail(function(xhr) {
                alert("Erro ao buscar dados: " + xhr.statusText);
            });
        } else {
            // AJUSTE: Implementação da busca para ENTRADA
            const url = `/fuel-entry/${id}/json`;
            $.get(url, function(data) {
                // Preenche os campos no modal de entrada
                // Nota: Verifique se os IDs abaixo correspondem aos inputs do seu #modalEntrada
                $('#modalEntrada form').attr('action', `/fuel-entry/${id}`); // Muda a rota para UPDATE
                $('#modalEntrada form').append('<input type="hidden" name="_method" value="PUT">');
                
                $('#modalEntrada select[name="tank_id"]').val(data.tank_id);
                $('#modalEntrada input[name="date"]').val(data.date.split(' ')[0]);
                $('#modalEntrada input[name="quantity"]').val(data.quantity);
                $('#modalEntrada input[name="supplier"]').val(data.supplier || data.ident);
                
                var myModal = new bootstrap.Modal(document.getElementById('modalEntrada'));
                myModal.show();
            }).fail(function() {
                alert("Erro ao buscar dados da entrada.");
            });
        }
    });
});

// Evento de DELETE com RELOAD para atualizar os gráficos
// Evento de DELETE ajustado
$(document).off('click', '.btn-delete').on('click', '.btn-delete', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation(); // Impede o disparo duplo

    const button = $(this);
    const url = button.data('url');

    if (confirm('Tem certeza que deseja apagar este registo?')) {
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Força o reload para atualizar tanques e gráficos
                window.location.reload();
            },
            error: function(xhr) {
                // Se o status for 200 (OK), ele apagou, mas o AJAX achou que era erro
                if(xhr.status === 200) {
                    window.location.reload();
                } else {
                    alert('Erro ao apagar: ' + xhr.statusText);
                    button.prop('disabled', false).html('<i class="bi bi-trash"></i>');
                }
            }
        });
    }
});


$(document).on('click', '.btn-delete', function() {
    const button = $(this);
    const url = button.data('url');
    const row = button.closest('tr'); // Seleciona a linha da tabela

    if (confirm('Tem certeza que deseja apagar este registo?')) {
        // Desativa o botão para evitar cliques duplos
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Remove a linha com um efeito suave
                row.fadeOut(400, function() {
                    $(this).remove();
                });
                // Opcional: Mostrar um alerta rápido de sucesso (Toast)
            },
            error: function(xhr) {
                alert('Erro ao apagar o registo. Tente novamente.');
                button.prop('disabled', false).html('<i class="bi bi-trash"></i>');
            }
        });
    }
});



        $(document).ready(function() {
            $('#fuelTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"Bf>rtip',
                buttons: [
                    { 
                        extend: 'excel', 
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel', 
                        className: 'btn btn-success btn-sm border-0',
                        title: 'Relatorio_Combustivel_{{ date("Ymd") }}'
                    },
                    { 
                        extend: 'pdf', 
                        text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF', 
                        className: 'btn btn-danger btn-sm border-0',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }
                ],
                order: [[ 0, "desc" ]],
                language: { url: "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json" },
                pageLength: 20
            });
        });

        function calc() {
            const i = parseFloat(document.getElementById('ci').value) || 0;
            const f = parseFloat(document.getElementById('cf').value) || 0;
            const res = f - i;
            document.getElementById('qty').value = res > 0 ? res : 0;
        }


     document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('consumptionChart').getContext('2d');
    
    // Verifica se existem dados para evitar erros de gráfico vazio
    const chartLabels = {!! json_encode($labelsCompostas ?? []) !!};
    const chartDatasets = {!! json_encode($datasets ?? []) !!};

    if (chartLabels.length === 0) {
        console.warn("Nenhum dado encontrado para os últimos 10 dias.");
    }

 new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($labelsCompostas) !!},
        datasets: {!! json_encode($datasets) !!}
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true, // Mantém as cores na mesma barra
                ticks: {
                    autoSkip: false,
                    maxRotation: 45,
                    font: { size: 10 }
                }
            },
            y: {
                stacked: true, // Empilha o volume
                beginAtZero: true
            }
        },
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false
            }
        }
    }
});
});

        
    </script>
</x-app-layout>