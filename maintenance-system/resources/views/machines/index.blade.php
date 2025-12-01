<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Máquinas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>⚙️ Lista de Equipamentos e Máquinas</h1>
            <a href="{{ route('machines.create') }}" class="btn btn-primary">
                ➕ Adicionar Nova Máquina
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if ($machines->isEmpty())
            <div class="alert alert-info">
                Ainda não há máquinas registadas. Adicione uma nova máquina para começar!
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover border">
                    <thead class="table-dark">
                        <tr>
                            <th>Nº Interno</th>
                            <th>Tipo de Equipamento</th>
                            <th>Marca / Modelo</th>
                            <th>Localização</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($machines as $machine)
                            <tr>
                                <td><strong>{{ $machine->numero_interno }}</strong></td>
                                <td>{{ $machine->tipo_equipamento }}</td>
                                <td>{{ $machine->marca }} / {{ $machine->modelo }}</td>
                                <td>{{ $machine->localizacao }}</td>
                                
                                <td>
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
                                </td>

                                <td>
                                    <a href="{{ route('machines.show', $machine->id) }}" class="btn btn-sm btn-outline-info me-1">Ver</a>
                                    <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                                    
                                    <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Tem certeza que deseja eliminar a máquina {{ $machine->numero_interno }}? Esta ação não pode ser desfeita.')">
                                            Apagar
                                        </button>
                                    </form>
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