<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Manutenções</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- DataTables CSS (Bootstrap 5 Theme) -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css" rel="stylesheet">
    
    <style>
        /* Estilos personalizados para melhorar a visualização */
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
            <h1>🛠️ Histórico de Manutenções</h1>
            
            <!-- Link para as máquinas (Substitua por: {{ route('machines.index') }} ) -->
            <a href="{{ route('machines.index') }} " class="btn btn-primary shadow-sm">
                ⚙️ Ver Máquinas
            </a>
        </div>

        <!-- Você pode remover esta seção de comentário se estiver usando o Blade -->
        <!-- <div class="alert alert-info">
            Não há registos de manutenção no sistema.
        </div> -->

        <div class="table-responsive">
            <!-- A tabela precisa do ID 'maintenanceTable' para ser inicializada pelo DataTables -->
            <table id="maintenanceTable" class="table table-striped table-hover border">
                <thead class="table-dark">
                    <tr>
                        <th># ID</th>
                        <th>Máquina (Nº Interno)</th>
                        <th>Status</th>
                        <th>Avaria Reportada</th>
                        <th>Agendado para</th>
                        <th>Início Real</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 
                        ******************************************************************
                        * ESTE É O BLOCO DE LÓGICA BLADE QUE DEVE SER COPIADO *
                        ******************************************************************
                    -->
                    @foreach ($maintenances as $maintenance)
                        {{-- FILTRO APLICADO: Exibe apenas manutenções com status 'Em Progresso' ou 'Pendente' --}}
                        @if ($maintenance->status === 'Em Progresso' || $maintenance->status === 'Pendente')
                        @php
                            // Lógica para determinar a cor da badge com base no status
                            $badge_class = match($maintenance->status) {
                                'Pendente' => 'bg-warning text-dark',
                                'Em Progresso' => 'bg-info text-dark',
                                'Concluída' => 'bg-success',
                                'Cancelada' => 'bg-secondary',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <!-- Link para a página de detalhes da manutenção (ID correto) -->
                            <td><a href="{{ route('maintenances.show', $maintenance->id) }}">{{ $maintenance->id }}</a></td>
                            
                            <td>
                                <!-- Link para a máquina associada -->
                                <a href="{{ route('machines.show', $maintenance->machine_id) }}">
                                    {{ $maintenance->machine->numero_interno }}
                                </a>
                            </td>
                            
                            <td>
                                <span class="badge {{ $badge_class }}">{{ $maintenance->status }}</span> 
                            </td>
                            
                            <td>{{ Str::limit($maintenance->failure_description, 50) }}</td>
                            <td>{{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>{{ $maintenance->start_date ? $maintenance->start_date->format('d/m/Y H:i') : 'Pendente' }}</td>

                            <td>
                                <!-- BOTÃO DETALHES USANDO O ID CORRETO -->
                                <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-outline-info me-1">Detalhes</a>
                                <!-- BOTÃO EDITAR USANDO O ID CORRETO -->
                                <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            </td>
                        </tr>
                        @endif {{-- FIM DO FILTRO --}}
                    @endforeach
                    
                    <!-- 
                        ******************************************************************
                        * FIM DA LÓGICA BLADE REAL. REMOVA OS DADOS MOCKADOS ABAIXO! *
                        ******************************************************************
                    -->
                    <!-- 
                        DADOS MOCKADOS ESTÃO AQUI COMO COMENTÁRIO PARA REFERÊNCIA ESTÁTICA.
                    -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery é necessário para o DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    <!-- DataTables Core JS e Integração Bootstrap 5 -->
    <script src="https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

    <script>
        // Inicializa o DataTables
        $(document).ready(function() {
            $('#maintenanceTable').DataTable({
                // Configuração de localização para Português (Brasil)
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/pt-BR.json'
                },
                // Desabilita a ordenação na coluna de Ações (última coluna)
                columnDefs: [
                    { orderable: false, targets: [6] }
                ],
                // Ordem inicial: ID de forma decrescente (do mais novo para o mais antigo)
                order: [[0, 'desc']] 
            });
        });
    </script>
</body>
</html>