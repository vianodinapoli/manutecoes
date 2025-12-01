<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Máquina: {{ $machine->name }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Detalhes da Máquina: <span class="text-primary">{{ $machine->name }}</span></h1>
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
                        <h5 class="mb-0">Informação Básica</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nº de Série:</strong> {{ $machine->serial_number }}</li>
                        <li class="list-group-item"><strong>Chassi:</strong> {{ $machine->chassi }}</li>
                        <li class="list-group-item"><strong>Localização:</strong> {{ $machine->location }}</li>
                        <li class="list-group-item"><strong>Data de Registo:</strong> {{ $machine->created_at->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Descrição Detalhada</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $machine->description ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

        </div> <hr class="my-4">

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
                                            'Pendente' => 'bg-warning text-dark',
                                            'Em Progresso' => 'bg-info',
                                            'Concluída' => 'bg-success',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge_class }}">{{ $maintenance->status }}</span>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($maintenance->failure_description, 50) }}</td>
                                <td>{{ $maintenance->created_at->format('d/m/Y') }}</td>
                               <td>
                                   <form action="{{ route('maintenances.destroy', $maintenance->id) }}" method="POST" class="d-inline ms-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" 
                    title="Eliminar permanentemente este registo"
                    onclick="return confirm('ATENÇÃO: Tem certeza que deseja eliminar este registo de manutenção ID {{ $maintenance->id }}? Esta ação é irreversível.')">
                🗑️ Eliminar Registo
            </button>
        </form>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>