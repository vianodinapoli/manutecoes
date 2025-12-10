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
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🛠️ Manutenções Ativas (Pendente / Em Andamento)</h1>
            
            <a href="{{ route('machines.index') }} " class="btn btn-primary shadow-sm">
                ⚙️ Ver Máquinas
            </a>
        </div>

        @if($maintenances->isEmpty())
             <div class="alert alert alert-info">
                Não há registos de manutenção Ativos no sistema.
            </div>
        @endif

        <div class="table-responsive">
            <table id="maintenanceTable" class="table table-striped table-hover border">
                <thead class="table-dark">
                    <tr>
                        <th># ID</th>
                        <th>Máquina (Nº Interno)</th>
                        <th>Status</th>
                        <th>Avaria Reportada</th>
                        <th>Data de Entrada</th> {{-- NOVA COLUNA AQUI --}}
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
            $('#maintenanceTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
                },
                // A coluna de Ações agora é a última (índice 7), e DataTables ajusta-se automaticamente.
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],
                order: [[0, 'desc']] 
            });
        });
    </script>
</body>
</html>