<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Máquina: {{ $machine->numero_interno }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes da Máquina: <span class="text-primary">{{ $machine->numero_interno }}</span></h1>
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">
                ⬅️ Voltar à Lista
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-warning">
                ✏️ Editar Máquina
            </a>
            <a href="#" class="btn btn-danger">
                ➕ Nova Manutenção
            </a>
        </div>
        
        <div class="row">
            
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Informação de Identificação</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nº Interno:</strong> {{ $machine->numero_interno }}</li>
                        <li class="list-group-item"><strong>Tipo de Equipamento:</strong> {{ $machine->tipo_equipamento }}</li>
                        <li class="list-group-item"><strong>Marca:</strong> {{ $machine->marca ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Modelo:</strong> {{ $machine->modelo ?? 'N/A' }}</li>
                        <li class="list-group-item"><strong>Localização:</strong> {{ $machine->localizacao }}</li>
                        <li class="list-group-item"><strong>Operador/Responsável:</strong> {{ $machine->operador ?? 'N/A' }}</li>
                        <li class="list-group-item">
                            <strong>Status Operacional:</strong> 
                            @php
                                $badge_class = match($machine->status) {
                                    'Operacional' => 'bg-success',
                                    'Em Manutenção' => 'bg-info text-dark',
                                    'Avariada' => 'bg-danger',
                                    'Desativada' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badge_class }}">{{ $machine->status }}</span>
                        </li>
                        <li class="list-group-item"><strong>Data de Registo:</strong> {{ $machine->created_at->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Observações Detalhadas</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $machine->observacoes ?? 'Nenhuma observação detalhada.' }}</p>
                    </div>
                </div>
            </div>

        </div> 
        
        <hr class="my-4">

        @if (isset($maintenances))
            <h2>Histórico de Manutenções ({{ $maintenances->count() }})</h2>

            @if ($maintenances->isEmpty())
                <div class="alert alert-info mt-3">
                    Ainda não há registos de manutenção para esta máquina.
                </div>
            @else
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-hover border">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Estado</th>
                                <th>Descrição da Avaria</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($maintenances as $maintenance)
                                <tr>
                                    <td>{{ $maintenance->id }}</td>
                                    <td>
                                        @php
                                            $badge_class = match($maintenance->status) {
                                                'Pendente', 'pendente' => 'bg-warning text-dark',
                                                'Em Progresso', 'em progresso' => 'bg-info',
                                                'Concluída', 'concluída' => 'bg-success',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badge_class }}">{{ $maintenance->status }}</span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($maintenance->failure_description ?? $maintenance->title, 50) }}</td>
                                    <td>{{ $maintenance->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-outline-info me-1" title="Ver Detalhes Completo">
                                            👁️ Ver
                                        </a>
                                        <a href="{{ route('maintenances.edit', $maintenance->id) }}" class="btn btn-sm btn-warning" title="Editar Registo de Manutenção">
                                            ✏️ Editar
                                        </a>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else 
            <div class="alert alert-warning mt-3">
                A variável de manutenções ($maintenances) não está disponível no Controller.
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>