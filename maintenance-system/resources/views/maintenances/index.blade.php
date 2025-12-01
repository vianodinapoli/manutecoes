<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Manutenções</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🛠️ Histórico de Manutenções</h1>
            
            <a href="{{ route('machines.index') }}" class="btn btn-primary">
                ⚙️ Ver Máquinas
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($maintenances->isEmpty())
            <div class="alert alert-info">
                Não há registos de manutenção no sistema.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover border">
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
                        @foreach ($maintenances as $maintenance)
                            <tr>
                                <td><a href="{{ route('maintenances.show', $maintenance->machine->numero_interno) }}">{{ $maintenance->id }}**</a></td>
                                
                                <td>
                                    <a href="{{ route('machines.show', $maintenance->machine->id) }}">
                                        {{ $maintenance->machine->numero_interno }}
                                    </a>
                                </td>
                                
                                <td>
                                    

                                     @php
                                        $badge_class = match($maintenance->machine->status) {
                                            'Pendente' => 'bg-warning text-dark',
                                            'Em Progresso' => 'bg-info',
                                            'Concluída' => 'bg-success',
                                            'Cancelada' => 'bg-secondary',
                                            default => 'bg-secondary',
                                        };
                                    @endphp 
                                
                                    <span class="badge {{ $badge_class }}">{{ $maintenance->machine->status }}</span> 
                                </td>
                                
                                <td>{{ Str::limit($maintenance->failure_description, 50) }}</td>
                                <td>{{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ $maintenance->start_date ? $maintenance->start_date->format('d/m/Y H:i') : 'Pendente' }}</td>

                                <td>
                                    <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-outline-info me-1">Detalhes</a>
                                    <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>