<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Manutenções Ativas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css" rel="stylesheet">
    
    <style>
        /* Estilos personalizados */
        .container {
            max-width: 1400px;
        }
        .table thead th {
            font-weight: 700;
            cursor: pointer;
        }
        .badge {
            min-width: 90px;
        }
    </style>
</head>
<body>
    <x-app-layout>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>🛠️ Manutenções Ativas (Pendente / Em Andamento)</h4>
            
            <a href="{{ route('machines.index') }} " class="btn btn-primary shadow-sm">
                ⚙️ Ver Máquinas
            </a>
        </div>
        
        @if($maintenances->isEmpty())
             <div class="alert alert alert-info">
                Não há registos de manutenção Ativos no sistema.
            </div>
        @endif

        <div class="card p-3 mb-4 shadow-sm">
            <h6 class="card-title mb-3">🔍 Filtro de Manutenções por Período</h6>
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="min-date" class="form-label">Data Inicial (Entrada)</label>
                    <input type="date" id="min-date" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="max-date" class="form-label">Data Final (Entrada)</label>
                    <input type="date" id="max-date" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="clear-filters" class="btn btn-outline-secondary w-100">Limpar Filtros</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="maintenanceTable" class="table table-striped table-hover border">
                <thead class="table-dark">
                    <tr>
                        <th># ID</th>
                        <th>Máquina (Nº Interno)</th>
                        <th>Status</th>
                        <th>Avaria Reportada</th>
                        <th>Data de Entrada</th>
                        <th>Agendado para</th>
                        <th>Início Real</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($maintenances as $maintenance)
                        
                        @php
                            $status_lower = strtolower($maintenance->status);
                            
                            $badge_class = match($status_lower) {
                                'pendente' => 'bg-warning text-dark',
                                'em_manutencao' => 'bg-info',
                                default => 'bg-secondary',
                            };
                            
                            $status_display = ucfirst(str_replace('_', ' ', $status_lower));
                        @endphp
                        
                        <tr>
                            <td>
                                <a href="{{ route('maintenances.show', $maintenance->id) }}">{{ $maintenance->id }}</a>
                            </td>
                            
                            <td>
                                @if($maintenance->machine)
                                    <a href="{{ route('machines.show', $maintenance->machine_id) }}">
                                        {{ $maintenance->machine->numero_interno }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            
                            <td>
                                <span class="badge {{ $badge_class }}">{{ $status_display }}</span> 
                            </td>
                            
                            <td>{{ Str::limit($maintenance->failure_description, 50) }}</td>
                            
                            <td>
                                {{ optional($maintenance->data_entrada)->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            
                            <td>{{ optional($maintenance->scheduled_date)->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            
                            <td>{{ optional($maintenance->start_date)->format('d/m/Y H:i') ?? 'Pendente' }}</td>

                            <td>
                                <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-outline-info me-1">Detalhes</a>
                                <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            
            // --- 1. FUNÇÃO CUSTOMIZADA PARA FILTRO DE DATA ---
            
            // Estende a funcionalidade de busca do DataTables para aceitar ranges de data.
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = $('#min-date').val(); // Data mínima no formato YYYY-MM-DD (input)
                    var max = $('#max-date').val(); // Data máxima no formato YYYY-MM-DD (input)
                    
                    // Pega o valor da coluna "Data de Entrada" (índice 4) no formato 'd/m/Y' da tabela
                    var date = data[4]; 
                    
                    // Função auxiliar para converter 'd/m/Y' para 'YYYYMMDD' para comparação
                    var parseDate = function(dateString) {
                        if (!dateString || dateString === 'N/A') return null;
                        var parts = dateString.split('/');
                        // Retorna no formato YYYYMMDD
                        return parts[2] + parts[1] + parts[0]; 
                    };

                    // Converte as datas de entrada e de filtro para o formato comparável
                    var valDate = parseDate(date);
                    var minDate = min ? min.replace(/-/g, '') : null;
                    var maxDate = max ? max.replace(/-/g, '') : null;
                    
                    // Se a data na linha for 'N/A' e não houver filtros, exibe a linha
                    if (valDate === null) {
                        return (!minDate && !maxDate);
                    }

                    // Lógica de Filtragem:
                    if ((minDate === null) && (maxDate === null)) {
                        return true; // Sem filtros, exibe tudo
                    }
                    if (minDate !== null && maxDate === null) {
                        return valDate >= minDate; // Apenas filtro inicial
                    }
                    if (minDate === null && maxDate !== null) {
                        return valDate <= maxDate; // Apenas filtro final
                    }
                    if (minDate !== null && maxDate !== null) {
                        return valDate >= minDate && valDate <= maxDate; // Filtro de range
                    }

                    return false;
                }
            );

            // --- 2. INICIALIZAÇÃO DO DATATABLES ---

            var table = $('#maintenanceTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
                },
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],
                order: [[4, 'desc']] // Ordena por Data de Entrada (índice 4)
            });

            // --- 3. EVENT LISTENERS PARA OS CAMPOS DE DATA ---

            // Quando o valor de Data Inicial ou Data Final muda, redesenha a tabela
            $('#min-date, #max-date').on('change', function() {
                table.draw();
            });
            
            // Listener para o botão de limpar filtros
            $('#clear-filters').on('click', function() {
                $('#min-date').val('');
                $('#max-date').val('');
                table.draw();
            });
        });
    </script>
    </x-app-layout>
</body>
</html>